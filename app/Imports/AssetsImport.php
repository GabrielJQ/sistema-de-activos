
use App\Models\AssetNetworkInterface;
use App\Models\ImportTask;
use App\Models\UnitTechnician;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssetsImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, ShouldQueue
{
    protected int $taskId;
    protected $currentRow = 0; // Aproximado, ya que es por chunks

     public int $timeout = 1200;

    public function __construct(int $taskId)
    {
        $this->taskId = $taskId;
    }

    public function collection(Collection $rows)
    {
        $task = ImportTask::find($this->taskId);
        if (!$task || $task->status === 'canceled') {
            return;
        }

        // Precarga de catálogos
        $units = \App\Models\Unit::all()->keyBy(fn($u) => strtoupper(trim($u->uninom)));
        $suppliers = Supplier::all()->keyBy(fn($s) => strtoupper(trim($s->prvnombre)));
        $deviceTypes = DeviceType::all()->keyBy(fn($d) => strtoupper(trim($d->equipo)));

        // Cache local para departamentos creados al vuelo durante esta importación
        // Estructura: ['UNIDAD_ID' => ['NOMBRE_DEPTO' => $deptObject]]
        $departmentsCache = [];

        // Pre-cargar todos los departamentos existentes para no consultar uno por uno si ya existen
        $allDepartments = Department::select('id', 'unit_id', 'areanom')->get();
        foreach ($allDepartments as $dept) {
            $dName = strtoupper(trim($dept->areanom));
            $departmentsCache[$dept->unit_id][$dName] = $dept;
        }

        // Mapa de empleados: "NOMBRE COMPLETO" => Employee Object
        $employees = Employee::all()->mapWithKeys(function ($emp) {
            $fullName = strtoupper(trim($emp->nombre . ' ' . $emp->apellido_pat . ' ' . $emp->apellido_mat));
            return [$fullName => $emp];
        });


        // 0. Configurar límites y resolver contexto global (Técnico)
        set_time_limit(0);
        $importUser = $task->user;
        $technician = null;
        try {
            $technician = AssetAssignment::resolveTechnicianForImport($importUser);
        } catch (\Throwable $e) {
            // Fallback: se manejará dentro del loop si es requerido
        }

        $newErrors = [];
        $rowsProcessedCount = 0;

        foreach ($rows as $index => $row) {
            $rowsProcessedCount++;

            // 1. Normalización
            $normalized = $this->normalizeRow($row);

            // Ignorar filas completamente en blanco (frecuentes al final de Excels editados)
            if (empty($normalized['TAG']) && empty($normalized['SERIE']) && empty($normalized['EQUIPO'])) {
                continue;
            }

            // 2. Validación Básica
            $validator = Validator::make($normalized, [
                'TAG' => 'required_without:SERIE',
                'SERIE' => 'required_without:TAG',
                'EQUIPO' => 'required',
                'MARCA' => 'required',
                'PROVEEDOR' => 'required',
                'UNIDAD' => 'required', // Obligatorio para UBICAR el activo/departamento
            ]);

            if ($validator->fails()) {
                $this->logError($newErrors, $normalized, 'Faltan datos obligatorios: ' . implode(', ', $validator->errors()->all()));
                continue;
            }

            // 3. Validación de Catálogos (Existencia)
            $tipo = $deviceTypes[$normalized['EQUIPO']] ?? null;
            if (!$tipo) {
                $this->logError($newErrors, $normalized, "Tipo de equipo '{$normalized['EQUIPO']}' desconocido.");
                continue;
            }

            $proveedor = $suppliers[$normalized['PROVEEDOR']] ?? null;
            if (!$proveedor) {
                $this->logError($newErrors, $normalized, "Proveedor '{$normalized['PROVEEDOR']}' desconocido.");
                continue;
            }

            // 3.1 Validar UNIDAD
            $unitName = $normalized['UNIDAD'];
            $unitObj = $units[$unitName] ?? null;
            if (!$unitObj) {
                $this->logError($newErrors, $normalized, "La unidad '{$unitName}' no existe.");
                continue;
            }

            // 3.2 Resolver DEPARTAMENTO (Auto-Creación Genérica)
            $deptName = $normalized['DEPARTAMENTO'];
            $deptFinal = null;

            if (!empty($deptName)) {
                // Buscar en cache local
                if (isset($departmentsCache[$unitObj->id][$deptName])) {
                    $deptFinal = $departmentsCache[$unitObj->id][$deptName];
                } else {
                    // "MAGIA": Si no existe, lo creamos
                    try {
                        $deptFinal = Department::create([
                            'unit_id' => $unitObj->id,
                            'areanom' => $deptName,
                            'tipo' => 'Oficina', // Default
                            'areacve' => 0, // 0 o generar alguno si es requerido unique? (check schema: unsignedInteger index, not unique)
                        ]);

                        // Agregar al cache para siguientes filas
                        $departmentsCache[$unitObj->id][$deptName] = $deptFinal;

                    } catch (\Exception $e) {
                        $this->logError($newErrors, $normalized, "Error al crear departamento '{$deptName}' en '{$unitName}': " . $e->getMessage());
                        continue;
                    }
                }
            }

            // 4. Lógica de Empleado vs Técnico
            $empleado = null;
            if (!empty($normalized['RESGUARDO'])) {
                $empleado = $employees[$normalized['RESGUARDO']] ?? null;
                // Si hay resguardo escrito pero no coincide, es un error de datos
                if (!$empleado) {
                    $this->logError($newErrors, $normalized, "El empleado de resguardo '{$normalized['RESGUARDO']}' no fue encontrado.");
                    continue;
                }
            }

            // Iniciar Transacción para esta fila
            DB::beginTransaction();
            try {
                // Validación de Técnico (Fallback)
                if (!$technician) {
                    throw new \Exception("No hay técnico de informática configurado para el usuario importador (Falló resolución inicial).");
                }
                
                // ==============================================================
                // INICIO: Regla de Override de Departamento
                // ==============================================================
                // 1. Por defecto, asignamos estrictamente el ID del departamento original del empleado
                // (Si la fila no tiene empleado marcado, hacemos fallback al departamento de la celda del Excel, 
                // y si la celda también está vacía, finalmente al departamento del técnico actual).
                $finalDepartment = $empleado?->department ?? ($deptFinal ?? $technician->department);

                // 2. Si identificamos al empleado en esta fila del Excel, validamos si es el técnico
                if ($empleado) {
                    $isTechnician = \App\Models\UnitTechnician::where('employee_id', $empleado->id)
                        ->where('is_active', 'true') // Aseguramos que el chequeo sea estrictamente booleano válido
                        ->exists();

                    // 3. Aplicar Excepción (Override): Si es Técnico Y Excel trae departamento válido ($deptFinal)
                    if ($isTechnician && !empty($normalized['DEPARTAMENTO']) && $deptFinal) {
                        // El excel trajo texto en DEPARTAMENTO, y $deptFinal ya es el objeto Department
                        // (resuelto o creado dinámicamente arriba en Validación de Catálogos).
                        $finalDepartment = $deptFinal;
                    }
                }
                
                if (!$finalDepartment) {
                    throw new \Exception("No se pudo determinar el departamento para el activo.");
                }
                // ==============================================================
                // FIN: Regla de Override de Departamento
                // ==============================================================

                // Crear/Actualizar Activo
                // 1. Determinar la llave única de búsqueda dinámicamente
                $matchCondicion = [];
                if (!empty($normalized['SERIE'])) {
                $matchCondicion['serie'] = $normalized['SERIE'];
                } else {
                $matchCondicion['tag'] = $normalized['TAG'];
                }

                // 2. Crear o Actualizar el Activo
                $asset = Asset::updateOrCreate(
                $matchCondicion, // Buscamos SOLO por la llave fuerte (Serie o Tag)
                [
                // Pasamos todos los valores que se van a actualizar o crear
                'tag' => $normalized['TAG'], 
                'serie' => $normalized['SERIE'], 
                'device_type_id' => $tipo->id,
                'marca' => $normalized['MARCA'],
                'modelo' => $normalized['MODELO'] ?? 'S/M',
                'estado' => $empleado ? $normalized['ESTADO'] : 'RESGUARDADO',
                'propiedad' => $normalized['PROPIEDAD'],
                'supplier_id' => $proveedor->id,
                'department_id' => $finalDepartment->id,
                'activo' => $normalized['ACTIVO'],
                ]
                );

                // IP Address
                if (!empty($normalized['IP_ADDRESS'])) {
                    $requiresIp = $tipo->requires_ip || stripos($tipo->equipo, 'Impresora') !== false;
                    if ($requiresIp) {
                        AssetNetworkInterface::updateOrCreate(
                            ['asset_id' => $asset->id],
                            ['ip_address' => $normalized['IP_ADDRESS']]
                        );
                    }
                }

                // Asignación
                if ($empleado) {
                    // Crear asignación directa 
                    // (Nota: Podríamos usar AssetAssignment::assignToEmployee service, pero aquí simplificamos create directo 
                    // para evitar overheads de cierre/apertura complejos en imports masivos, 
                    // AUNQUE el usuario pidió "sin parches". Usar el servicio es lo más correcto).

                    // Verificamos si ya está asignado a este mismo empleado para no duplicar historial
                    $currentAssignment = $asset->assignments()->where('is_current', DB::raw('true'))->first();

                    if (!$currentAssignment || $currentAssignment->employee_id !== $empleado->id) {
                        AssetAssignment::assignToEmployee(
                            assetId: $asset->id,
                            employeeId: $empleado->id,
                            assignedAt: now(),
                            observations: 'Importación masiva',
                            assignmentType: 'normal',
                            temporaryHolder: null,
                            explicitDepartmentId: $finalDepartment->id
                        );
                    }

                } else {
                    // Si no tiene empleado, se asigna al técnico (Resguardado)
                    $currentAssignment = $asset->assignments()->where('is_current', DB::raw('true'))->first();
                    // Si ya está asignado a alguien y ahora viene sin resguardo... ¿lo quitamos? 
                    // Asumiremos que si viene vacío es "Stock/Resguardo".

                    if ($currentAssignment && $currentAssignment->employee_id !== $technician->id) {
                        AssetAssignment::assignToEmployee(
                            assetId: $asset->id,
                            employeeId: null, // null usará lógica de técnico internamente
                            assignedAt: now(),
                            observations: 'Importación: En Resguardo',
                            assignmentType: 'normal',
                            temporaryHolder: null,
                            explicitDepartmentId: $finalDepartment->id
                        );
                    } elseif (!$currentAssignment) {
                        // Primera vez, asignar a técnico
                        AssetAssignment::assignToEmployee(
                            assetId: $asset->id,
                            employeeId: null,
                            assignedAt: now(),
                            observations: 'Alta por Importación (Resguardo)',
                            assignmentType: 'normal',
                            temporaryHolder: null,
                            explicitDepartmentId: $finalDepartment->id
                        );
                    }
                    // Usamos el estado del CSV si existe, si no, 'RESGUARDADO'
                    $estadoFinal = !empty($normalized['ESTADO']) ? $normalized['ESTADO'] : 'RESGUARDADO';
                    $asset->update(['estado' => $estadoFinal]);
                }

                DB::commit();

            } catch (\Throwable $e) {
                DB::rollBack();
                $this->logError($newErrors, $normalized, "Error interno: " . $e->getMessage());
            }
        }

        // Actualizar tarea con progreso y errores
        $this->updateTaskProgress($rowsProcessedCount, $newErrors);
    }

    private function normalizeRow($row)
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $key = strtoupper(trim(str_replace(' ', '_', $key)));
            $value = trim((string) $value);
            $normalized[$key] = $value === '' ? null : $value;
        }

        // Mapeo a llaves estándar internas
        return [
            'TAG' => $normalized['TAG'] ?? null,
            'SERIE' => strtoupper($normalized['SERIE'] ?? ''),
            'EQUIPO' => strtoupper($normalized['EQUIPO'] ?? ''),
            'MARCA' => strtoupper($normalized['MARCA'] ?? ''),
            'MODELO' => strtoupper($normalized['MODELO'] ?? ''),
            'ESTADO' => strtoupper($normalized['ESTADO'] ?? 'OPERACION'),
            'PROPIEDAD' => strtoupper($normalized['PROPIEDAD'] ?? 'ARRENDADO'),
            'PROVEEDOR' => strtoupper($normalized['PROVEEDOR'] ?? ''),
            'UNIDAD' => strtoupper($normalized['UNIDAD'] ?? ''), // Nueva columna obligatoria
            'DEPARTAMENTO' => strtoupper($normalized['DEPARTAMENTO'] ?? ''),
            'RESGUARDO' => strtoupper($normalized['RESGUARDO'] ?? ''), // Nombre del empleado
            'ACTIVO' => strtoupper($normalized['ACTIVO'] ?? 'SI'),
            'IP_ADDRESS' => $normalized['IP'] ?? $normalized['DIRECCION_IP'] ?? null,
        ];
    }

    private function logError(&$errorsArray, $data, $message)
    {
        $identifier = $data['TAG'] ?? $data['SERIE'] ?? 'Fila Desconocida';
        $errorsArray[] = "Items ({$identifier}): {$message}";
    }

    private function updateTaskProgress($processedCount, $newErrors)
    {
        $task = ImportTask::find($this->taskId);
        if (!$task)
            return;

        // Merge errores existentes con nuevos
        $existingErrors = $task->errors ?? [];
        if (!is_array($existingErrors))
            $existingErrors = [];

        $allErrors = array_merge($existingErrors, $newErrors);

        $task->increment('processed_rows', $processedCount);
        $task->update(['errors' => $allErrors]);
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();
                $total = array_sum($totalRows) - count($totalRows); // Restar headers
                ImportTask::where('id', $this->taskId)->update([
                    'total_rows' => $total,
                    'status' => 'processing',
                    'errors' => [] // Reset errors start
                ]);
            },
            AfterImport::class => function (AfterImport $event) {
                $task = ImportTask::find($this->taskId);
                if ($task && $task->status !== 'canceled') {
                    $errors = $task->errors ?? [];
                    // Force update to ensure UI sees it
                    $task->status = (count($errors) > 0) ? 'completed_with_errors' : 'completed';
                    $task->save();
                }
            },
            \Maatwebsite\Excel\Events\ImportFailed::class => function ($event) {
                $task = ImportTask::find($this->taskId);
                if ($task) {
                    $errors = is_array($task->errors) ? $task->errors : [];
                    $errors[] = $event->getException()->getMessage();
                    $task->update([
                        'status' => 'failed',
                        'errors' => $errors
                    ]);
                }
            },
        ];
    }
}


