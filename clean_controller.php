<?php
$content = <<<'EOD'
<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Supplier;
use App\Models\DeviceType;
use App\Models\Department;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetsExport;
use App\Imports\AssetsImport;
use App\Exports\GenericExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Support\Facades\DB;
use App\Models\AssetNetworkInterface;
use App\Models\UnitTechnician;
use App\Models\AssetAssignment;
use App\Http\Requests\AssetRequest;
use App\Services\AssetService;

class AssetController extends Controller
{
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }
    // Mostrar listado de activos
    public function index(Request $request)
    {
        $busqueda = $request->input('busqueda');

        // Lista de equipos principales
        $mainDevices = config('assets.main_devices');

        // Obtener departamento de informática
        $informaticaDept = Department::where('areanom', 'INFORMATICA')->first();
        //1
        $assignedAssets = Asset::with(['deviceType', 'currentHolder', 'department'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('tag', 'like', "%$busqueda%")
                        ->orWhere('serie', 'like', "%$busqueda%")
                        ->orWhere('marca', 'like', "%$busqueda%")
                        ->orWhere('modelo', 'like', "%$busqueda%")
                        ->orWhereHas('supplier', fn($q) => $q->where('nombre', 'like', "%$busqueda%"));
                });
            })
            ->whereNotIn('estado', ['BAJA', 'SINIESTRO', 'RESGUARDADO'])
            ->whereHas('currentHolder')
            ->whereHas(
                'deviceType',
                fn($q) =>
                $q->whereIn('equipo', $mainDevices)
            )
            ->orderBy('created_at', 'desc')
            ->get();
        //2
        $unassignedAssets = Asset::with(['deviceType', 'department', 'supplier'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('tag', 'like', "%$busqueda%")
                        ->orWhere('serie', 'like', "%$busqueda%")
                        ->orWhere('marca', 'like', "%$busqueda%")
                        ->orWhere('modelo', 'like', "%$busqueda%")
                        ->orWhereHas(
                            'supplier',
                            fn($q) =>
                            $q->where('nombre', 'like', "%$busqueda%")
                        );
                });
            })
            ->whereNotIn('estado', ['BAJA', 'SINIESTRO', 'OPERACION'])
            ->where('estado', 'RESGUARDADO')
            ->whereHas(
                'deviceType',
                fn($q) =>
                $q->whereIn('equipo', $mainDevices)
            )
            ->orderBy('created_at', 'desc')
            ->get();
        //3
        $damagedAssets = Asset::with(['deviceType', 'currentHolder', 'department'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('tag', 'like', "%$busqueda%")
                        ->orWhere('serie', 'like', "%$busqueda%")
                        ->orWhere('marca', 'like', "%$busqueda%")
                        ->orWhere('modelo', 'like', "%$busqueda%")
                        ->orWhereHas('supplier', fn($q) => $q->where('nombre', 'like', "%$busqueda%"));
                });
            })
            ->whereIn('estado', ['DANADO', 'SINIESTRO'])
            ->orderBy('created_at', 'desc')
            ->get();
        //4
        $inactiveAssets = Asset::with(['deviceType', 'currentHolder', 'department'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('tag', 'like', "%$busqueda%")
                        ->orWhere('serie', 'like', "%$busqueda%")
                        ->orWhere('marca', 'like', "%$busqueda%")
                        ->orWhere('modelo', 'like', "%$busqueda%")
                        ->orWhereHas('supplier', fn($q) => $q->where('nombre', 'like', "%$busqueda%"));
                });
            })
            ->where('estado', 'BAJA')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('assets.index', [
            'assignedAssets' => $assignedAssets,
            'unassignedAssets' => $unassignedAssets,
            'damagedAssets' => $damagedAssets,
            'inactiveAssets' => $inactiveAssets,
            'damagedTab' => $request->input('tab') === 'damaged',
            'inactiveTab' => $request->input('tab') === 'inactive',
        ]);

    }

    // Mostrar formulario para crear un nuevo activo
    public function create()
    {
        $suppliers = Supplier::all();
        $deviceTypes = DeviceType::all();
        $departments = Department::all();

        return view('assets.create', compact('suppliers', 'deviceTypes', 'departments'));
    }

    // Guardar un nuevo activo
    public function store(AssetRequest $request)
    {
        try {
            $existingAssetsCount = 0;
            $newAsset = $this->assetService->createAsset($request->validated(), auth()->user());

            return redirect()->route('assets.index')
                ->with('success', 'Activo creado correctamente.' .
                    ($request->modo_registro === 'REEMPLAZO' ? ' Se procesaron activos por reemplazo.' : ''));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Mostrar formulario para editar un activo existente
    public function edit(Asset $asset)
    {
        $suppliers = Supplier::all();
        $deviceTypes = DeviceType::all();
        $departments = Department::all();

        return view('assets.edit', compact('asset', 'suppliers', 'deviceTypes', 'departments'));
    }

    public function update(AssetRequest $request, Asset $asset)
    {
        $validated = $request->validated();

        $estadoActual = $asset->estado;
        $nuevoEstado = $validated['estado'];
        $estadoPrevio = session('estado_previo_' . $asset->id);

        if ($estadoActual === 'BAJA' && $nuevoEstado !== 'BAJA') {

            // Solo super_admin y admin pueden intentar restaurarlo
            if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
                return back()->with('error', 'Solo un administrador puede restaurar un activo dado de baja.');
            }

            // Debe confirmar contraseña
            if (
                !$request->filled('password_confirm') ||
                !Hash::check($request->password_confirm, auth()->user()->password)
            ) {
                return back()->with('error', 'Contraseña incorrecta. No se puede restaurar el activo.');
            }
        }

        // OPERACION → RESGUARDADO prohibido
        if ($estadoActual === 'OPERACION' && $nuevoEstado === 'RESGUARDADO') {
            return back()->with('error', 'No puedes pasar de OPERACION a RESGUARDADO.');
        }

        // RESGUARDADO → OPERACION prohibido
        if ($estadoActual === 'RESGUARDADO' && $nuevoEstado === 'OPERACION') {
            return back()->with('error', 'No puedes pasar de RESGUARDADO a OPERACION.');
        }

        // Reglas si está dañado
        if ($estadoActual === 'DANADO') {

            if (!$estadoPrevio) {
                if (!in_array($nuevoEstado, ['OPERACION', 'RESGUARDADO'])) {
                    return back()->with('error', 'Un activo dañado solo puede volver a OPERACION o RESGUARDADO.');
                }
            }

            if ($estadoPrevio === 'OPERACION' && $nuevoEstado !== 'OPERACION') {
                return back()->with('error', 'Este activo solo puede regresar a OPERACION.');
            }

            if ($estadoPrevio === 'RESGUARDADO' && $nuevoEstado !== 'RESGUARDADO') {
                return back()->with('error', 'Este activo solo puede regresar a RESGUARDADO.');
            }
        }

        // Guardar estado previo en sesión para validaciones futuras
        session(['estado_previo_' . $asset->id => $estadoActual]);

        try {
            $this->assetService->updateAsset($asset, $validated);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }



        return redirect()
            ->route('assets.index')
            ->with('success', 'Activo actualizado correctamente.');
    }

    public function destroy(Request $request, Asset $asset)
    {
        // Si viene desde dañado o baja, eliminar SOLO ese activo
        if ($request->individual == 1) {

            // borrar asignaciones
            $asset->assignments()->delete();

            // borrar activo
            $asset->delete();

            return back()->with('success', "Activo eliminado correctamente.");
        }

        //Eliminación grupal por TAG

        $tag = $asset->tag;

        $relatedAssets = Asset::where('tag', $tag)->get();

        /** @var Asset $item */
        foreach ($relatedAssets as $item) {
            $item->assignments()->delete();
            $item->delete();
        }

        return redirect()->route('assets.index')
            ->with('success', "Todos los activos con el TAG $tag fueron eliminados correctamente.");
    }


    public function showByTag($tag)
    {
        // Define el orden de equipos principales (custom)
        $mainOrder = [
            'Equipo All In One',
            'Equipo Escritorio',
            'Escritorio Avanzada',
            'Laptop de Avanzada',
            'Laptop de Intermedia',
        ];

        //Ordenamos: principales primero, luego todo lo demás
        $assets = Asset::query()
            ->with(['deviceType', 'supplier', 'department', 'currentHolder'])
            ->where('tag', $tag)
            ->where('estado', '!=', 'BAJA')
            ->leftJoin('device_types as dt', 'dt.id', '=', 'assets.device_type_id')
            ->select('assets.*')
            ->orderByRaw("
                CASE 
                    WHEN dt.equipo IN ('" . implode("','", $mainOrder) . "') THEN 0 
                    ELSE 1 
                END
            ")
            ->orderByRaw("
                CASE dt.equipo
" . collect($mainOrder)->map(fn($item, $index) => "WHEN '$item' THEN $index")->implode("\n") . "
                    ELSE " . count($mainOrder) . "
                END
            ") // PostgreSQL compatible ordering
            ->orderBy('assets.created_at', 'asc')
            ->get();

        if ($assets->isEmpty()) {
            return redirect()->route('assets.index')
                ->with('error', 'No se encontraron activos con el TAG especificado o están dados de baja.');
        }

        $mainAsset = $assets->first();

        return view('assets.show', compact('assets', 'mainAsset'));
    }

    public function bulkUpdateByTag(Request $request, string $tag)
    {
        if (!hasRole(['super_admin', 'admin']))
            abort(403);

        $request->validate([
            'bulk_estado' => 'nullable|in:OPERACION,GARANTIA,SINIESTRO,RESGUARDADO,DANADO,BAJA,OTRO',
            'bulk_supplier_id' => 'nullable|exists:suppliers,id',
            'bulk_department_id' => 'nullable|exists:departments,id',
            'bulk_marca' => 'nullable|string|max:255',
            'bulk_modelo' => 'nullable|string|max:255',
        ]);

        try {
            $this->assetService->bulkUpdateByTag($tag, $request->validated());
            return back()->with('success', 'Acciones masivas aplicadas correctamente al TAG: ' . $tag);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Acciones masivas aplicadas correctamente al TAG: ' . $tag);
    }

    // Mostrar formulario de importación
    public function importForm()
    {
        return view('assets.import');
    }

    // Procesar archivo de importación
    public function import(Request $request)
    {
        // Validación de archivo
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ], [
            'file.mimes' => 'El archivo debe ser CSV o Excel (.xls/.xlsx)',
            'file.required' => 'Debes seleccionar un archivo.',
        ]);

        // Verificar que haya empleados
        if (\App\Models\Employee::count() === 0) {
            return redirect()->back()->with('error', 'No se pueden importar activos: primero debes registrar empleados.');
        }

        // Verificar que exista ENCARGADO de la unidad
        $user = auth()->user();
        $hasTechnician = UnitTechnician::where('unit_id', $user->unit_id)
            ->where('is_active', 'true')
            ->exists();

        if (!$hasTechnician) {
            return redirect()->back()->with(
                'error',
                'No se pueden importar activos: no has seleccionado a un encargado (técnico) de la unidad.'
            );
        }

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = $file->store('imports');

        $task = \App\Models\ImportTask::create([
            'user_id' => auth()->id(),
            'filename' => $filename,
            'status' => 'pending',
        ]);

        Excel::queueImport(new \App\Imports\AssetsImport($task->id), $path);

        return redirect()->route('assets.import.progress', $task->id)
            ->with('success', 'Importación iniciada en segundo plano.');
    }

    public function exportForm()
    {
        $columns = [
            'resguardo' => 'Resguardo',
            'empleado_correo' => 'Correo',
            'empleado_expediente' => 'Expediente',
            'empleado_curp' => 'CURP',
            'region' => 'Regional',
            'unidad' => 'Unidad',
            'direccion_completa' => 'Direccion',
            'department_id' => 'Departamento',
            'empleado_puesto' => 'Puesto',
            'empleado_telefono' => 'Telefono',
            'empleado_extension' => 'Extension',
            'tag' => 'TAG',
            'device_type_equipo' => 'Equipo',
            'estado' => 'Estado',
            'marca' => 'Marca',
            'modelo' => 'Modelo',
            'serie' => 'Serie',
        ];

        $filterColumns = [
            'resguardo' => 'Resguardante',
            'department_id' => 'Departamento',
            'tag' => 'TAG',
            'device_type_equipo' => 'Equipo',
            'marca' => 'Marca',
            'estado' => 'Estado',
        ];

        $departments = Department::pluck('areanom', 'id');
        $tipos = DeviceType::pluck('equipo');
        $employees = \App\Models\Employee::orderBy('nombre')->get()->mapWithKeys(fn($emp) => [
            $emp->id => $emp->full_name
        ]);

        return view('assets.export', compact('columns', 'filterColumns', 'departments', 'tipos', 'employees'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'columns' => 'required|array|min:1',
            'format' => 'required|in:csv,xlsx,pdf',
            'filter_column' => 'nullable|string',
            'filter_value' => 'nullable|string',
            'extra_columns' => 'nullable|array',
            'extra_columns.*.name' => 'required_with:extra_columns|string|max:50',
            'extra_columns.*.value' => 'nullable|string|max:255',
        ]);

        $columns = $request->input('columns');
        $format = $request->input('format');
        $dataset = $request->input('dataset');
        $filterColumn = $request->input('filter_column');
        $filterValue = $request->input('filter_value');
        $extraColumns = $request->input('extra_columns', []);

        // Query base
        $query = Asset::with(['deviceType', 'department', 'currentHolder.employee']);

        // Filtros adicionales
        if ($filterColumn && $filterValue) {
            switch ($filterColumn) {
                case 'department_id':
                    $query->whereHas('department', fn($q) => $q->where('id', $filterValue));
                    break;

                case 'resguardo':
                    $query->whereHas('currentHolder', function ($q) use ($filterValue) {
                        $q->where('employee_id', $filterValue)
                            ->where('is_current', 'true');
                    });
                    break;
                case 'device_type_equipo':
                    $query->whereHas('deviceType', fn($q) => $q->where('equipo', 'like', "%$filterValue%"));
                    break;

                default:
                    $query->where($filterColumn, 'like', "%$filterValue%");
                    break;
            }
        }

        $assets = $query->get();

        if ($assets->isEmpty()) {
            return redirect()->back()->with('error', 'No hay activos que coincidan con el filtro.');
        }

        // Mapear los datos según columnas seleccionadas y columnas extras
        $data = $assets->map(function ($asset) use ($columns, $extraColumns) {

            // Relaciones principales
            $employee = $asset->currentHolder?->employee;
            $dept = $employee?->department ?? $asset->department;
            $address = $dept?->address;
            $unit = $dept?->unit;
            $region = $unit?->region;

            $row = [];

            foreach ($columns as $col) {

                switch ($col) {

                    // Resguardo
                    case 'resguardo':
                        $row['Resguardo'] = $employee?->full_name ?? 'Informática';
                        break;

                    // Empleado
                    case 'empleado_correo':
                        $row['Correo'] = $employee->email ?? '';
                        break;

                    case 'empleado_expediente':
                        $row['Expediente'] = $employee->expediente ?? '';
                        break;

                    case 'empleado_curp':
                        $row['CURP'] = $employee->curp ?? '';
                        break;

                    case 'empleado_puesto':
                        $row['Puesto'] = $employee->puesto ?? '';
                        break;

                    case 'empleado_telefono':
                        $row['Telefono'] = $employee->telefono ?? '';
                        break;

                    case 'empleado_extension':
                        $row['Extension'] = $employee->extension ?? '';
                        break;

                    // Region y Unidad
                    case 'region':
                        $row['Regional'] = $region->regnom ?? '';
                        break;

                    case 'unidad':
                        $row['Unidad'] = $unit->uninom ?? '';
                        break;

                    // Dirección completa
                    case 'direccion_completa':

                        $calle = $address->calle ?? '';
                        $colonia = $address->colonia ?? '';
                        $cp = $address->cp ?? '';
                        $municipio = $address->municipio ?? '';
                        $estado = $address->estado ?? '';

                        $direccion = trim(
                            ($calle ? "$calle, " : "") .
                            ($colonia ? "COL. $colonia, " : "") .
                            ($cp ? "CP $cp, " : "") .
                            ($municipio ? "$municipio, " : "") .
                            ($estado ? "$estado" : "")
                            ,
                            ", "
                        );

                        $row['Direccion'] = $direccion;
                        break;

                    // Departamento
                    case 'department_id':
                        $row['Departamento'] = $dept->areanom ?? '';
                        break;

                    //Activo
                    case 'tag':
                        $row['TAG'] = $asset->tag ?? '';
                        break;

                    case 'device_type_equipo':
                        $row['Equipo'] = $asset->deviceType->equipo ?? '';
                        break;

                    case 'estado':
                        $row['Estado'] = $asset->estado ?? '';
                        break;

                    case 'marca':
                        $row['Marca'] = $asset->marca ?? '';
                        break;

                    case 'modelo':
                        $row['Modelo'] = $asset->modelo ?? '';
                        break;

                    case 'serie':
                        $row['Serie'] = $asset->serie ?? '';
                        break;

                }
            }

            foreach ($extraColumns as $extra) {
                $row[$extra['name']] = $extra['value'] ?? '';
            }

            return $row;
        });


        // Exportar según formato
        $timestamp = now()->format('Y-m-d_H-i-s');

        if ($format === 'csv') {
            $filename = "activos_{$timestamp}.csv";
            $handle = fopen($filename, 'w');

            if ($data->isNotEmpty()) {
                fputcsv($handle, array_keys($data->first()));
            }

            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
            return response()->download($filename)->deleteFileAfterSend(true);
        }

        if ($format === 'xlsx') {

            $headings = array_keys($data->first());

            return Excel::download(
                new \App\Exports\StyledAssetsExport(
                    $data->toArray(),
                    $headings
                ),
                "activos_{$timestamp}.xlsx"
            );
        }


        if ($format === 'pdf') {
            $filename = "activos_{$timestamp}.pdf";
            return Pdf::loadView('assets.pdf', ['data' => $data])->stream($filename);
        }

        return redirect()->back()->with('error', 'Formato no soportado.');
    }

    public function showImport()
    {
        return view('assets.import');
    }

    public function downloadTemplate()
    {
        $headers = [
            ['tag', 'equipo', 'marca', 'modelo', 'serie', 'estado', 'propiedad', 'proveedor', 'unidad', 'departamento', 'resguardo', 'activo']
        ];

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "plantilla_activos_{$timestamp}.xlsx";

        $export = new class ($headers) implements FromArray {
            protected $data;
            public function __construct($data)
            {
                $this->data = $data; }
            public function array(): array
            {
                return $this->data; }
        };

        return Excel::download($export, $filename);
    }
    public function downloadInstructionsPDF()
    {
        // Lista de tipos de equipo
        $deviceTypes = [
            'Biometrico' => 'Dispositivo para control de acceso por huella',
            'Cargador' => 'Cargador de laptop intermedia o avanzado',
            'CCTV' => 'Sistema de videovigilancia de circuito cerrado',
            'Docking' => 'Base de expansión para laptops',
            'Equipo All In One' => 'Computadora con monitor integrado',
            'Equipo Escritorio' => 'Computadora de escritorio',
            'Escritorio Avanzada' => 'Equipo de escritorio para servidor',
            'Firewall' => 'Dispositivo de seguridad para red',
            'Impresora Multifuncional' => 'Impresora con escáner, copiadora e impresión',
            'Laptop de Avanzada' => 'Laptop con periféricos avanzados',
            'Laptop de Intermedia' => 'Laptop',
            'Lector DVD' => 'Unidad lectora de discos DVD',
            'Modem Satelital' => 'Dispositivo de conexión a internet vía satélite',
            'Monitor' => 'Pantalla para visualización de contenido',
            'Mouse' => 'Dispositivo apuntador',
            'No Break' => 'Sistema de respaldo eléctrico (UPS básico)',
            'Portatil' => 'Dispositivo móvil, generalmente laptop',
            'Proyector' => 'Dispositivo combinado de proyección',
            'Router' => 'Router para conexión a internet',
            'Router LTE' => 'Router con conexión LTE para red celular',
            'Switch' => 'Dispositivo de red para interconexión de equipos',
            'Tableta' => 'Dispositivo táctil portátil',
            'Teclado' => 'Periférico de entrada de texto',
            'Telefonia IP' => 'Teléfono con IP',
            'Telefono' => 'Dispositivo de comunicación convencional',
            'UPS' => 'Sistema de alimentación ininterrumpida',
            'Video Proyector' => 'Dispositivo para proyectar imagen o video',
        ];
        // Lista de proveedores
        $suppliers = [
            'FOCUS' => 'IA-008VSS005-E26-2021',
            'SYNNEX' => null,
            'INDUSTRIAS SANDOVAL' => 'PSG/424/2021',
            'STE' => null,
            'ALIMENTACION PARA EL BIENESTAR' => null,
        ];
        // Lista de departamentos
        $departments = [
            'GERENCIA DE SUCURSAL',
            'SUBGERENCIA DE ABASTO',
            'SUBGERENCIA DE OPERACIONES',
            'LOGISTICA Y TRANSPORTES',
            'PRESUPUESTO',
            'CONTABILIDAD',
            'TESORERIA',
            'ADMINISTRACION',
            'PERSONAL',
            'INFORMATICA',
            'ASUNTOS JURIDICOS',
            'JURIDICO',
            'CONSEJOS COMUNITARIOS',
            'ALMACEN CENTRAL',
            'AYUTLA MIXES',
            'CUAJIMOLOYAS',
            'IXTLAN DE JUAREZ',
            'MAGDALENA OCOTLAN',
            'SAN ANDRES HIDALGO',
            'SAN JOSE EL CHILAR',
            'SAN PEDRO JUCHATENGO',
            'SANTA MARIA LACHIXIO',
            'SANTIAGO MATATLAN',
            'SANTO TOMAS TAMAZULAPAN',
            'TEOTITLAN DE FLORES MAGON',
            'VALLES CENTRALES'
        ];

        return Pdf::loadView('assets.instructions_pdf', compact('deviceTypes', 'suppliers', 'departments'))
            ->stream('Instrucciones_Llenado_Activos.pdf');
    }

    public function report(Asset $asset)
    {
        // Relacionar con empleado actual si existe
        $employee = $asset->currentHolder?->employee;

        return view('assets.report', compact('asset', 'employee'));
    }

    public function submitReport(Request $request, Asset $asset)
    {
        $request->validate([
            'description' => 'required|string|max:2000',
            'ip' => 'nullable|string|max:255',
            'resguardo' => 'nullable|string|max:255',
            'damage_image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $employee = $asset->currentHolder?->employee;

        // Guardar imagen
        $imagePaths = [];
        if ($request->hasFile('damage_images')) {
            foreach ($request->file('damage_images') as $file) {
                $imagePaths[] = $file->store('damage_reports', 'public');
            }
        }
        // Datos para la vista PDF
        $data = [
            'asset' => $asset,
            'employee' => $employee,
            'description' => $request->description,
            'ip' => $request->ip,
            'resguardo' => $request->resguardo,
            'imagePaths' => $imagePaths,
            'fecha' => now()->format('d/m/Y H:i'),
        ];
        // Generar PDF
        $pdf = Pdf::loadView('assets.damage_report_pdf', $data);
        // Descargar PDF
        $filename = 'Reporte_Daño_' . ($asset->tag ?? 'sin_tag') . '.pdf';
        return $pdf->stream($filename);
    }

    // Verificar si hay una importación activa para el usuario actual
    public function checkActiveImport()
    {
        $task = \App\Models\ImportTask::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'processing'])
            ->latest()
            ->first();

        if ($task) {
            return response()->json([
                'active' => true,
                'task_id' => $task->id,
                'status' => $task->status,
                'progress' => $task->total_rows > 0 ? round(($task->processed_rows / $task->total_rows) * 100) : 0,
                'filename' => $task->filename
            ]);
        }

        return response()->json(['active' => false]);
    }
}
EOD;

file_put_contents('app/Http/Controllers/AssetController.php', $content);
echo "AssetController cleaned successfully";
