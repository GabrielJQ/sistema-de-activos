<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Department;
use App\Models\ImportTask;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmployeesImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings, WithChunkReading, WithEvents, ShouldQueue
{
    protected int $taskId;

    public function __construct(int $taskId)
    {
        $this->taskId = $taskId;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape' => '\\',
        ];
    }

    public function collection(Collection $rows)
    {
        $task = ImportTask::find($this->taskId);
        if (!$task || $task->status === 'canceled') {
            return;
        }

        set_time_limit(0);

        // 1. Precarga de Unidades (para validar que existan)
        $units = \App\Models\Unit::all()->keyBy(
            fn($u) => strtoupper(trim($u->uninom))
        );

        // 2. Precarga de Departamentos agrupados por Unidad
        // Estructura: ['NOMBRE_UNIDAD' => ['NOMBRE_DEPTO' => $deptObject]]
        $departmentsByUnit = [];
        $allDepartments = Department::with('unit')->get();

        foreach ($allDepartments as $dept) {
            if ($dept->unit) {
                $unitName = strtoupper(trim($dept->unit->uninom));
                $deptName = strtoupper(trim($dept->areanom));
                $departmentsByUnit[$unitName][$deptName] = $dept;
            }
        }

        $newErrors = [];
        $rowsProcessedCount = 0;

        foreach ($rows as $row) {
            $rowsProcessedCount++;

            // 1. Normalización
            $normalized = $this->normalizeRow($row);

            // 2. Validación
            $validator = Validator::make($normalized, [
                'EXPEDIENTE' => 'required',
                'NOMBRE' => 'required',
                'UNIDAD' => 'required', // Ahora es obligatorio
                'DEPARTAMENTO' => 'required',
            ]);

            if ($validator->fails()) {
                $this->logError($newErrors, $normalized, 'Datos incompleto: ' . implode(', ', $validator->errors()->all()));
                continue;
            }

            // 3. Catálogos (Búsqueda en cascada)
            $unitName = $normalized['UNIDAD'];
            $deptName = $normalized['DEPARTAMENTO'];
            $deptFinal = null;

            // Paso A: Validar Unidad
            if (!isset($units[$unitName])) {
                $this->logError($newErrors, $normalized, "Unidad '{$unitName}' no existe.");
                continue;
            }

            // Paso B: Buscar Departamento DENTRO de esa unidad
            if (isset($departmentsByUnit[$unitName][$deptName])) {
                $deptFinal = $departmentsByUnit[$unitName][$deptName];
            } else {
                $this->logError($newErrors, $normalized, "El departamento '{$deptName}' no pertenece a la unidad '{$unitName}' o no existe.");
                continue;
            }

            try {
                // Crear o actualizar empleado
                Employee::updateOrCreate(
                    ['expediente' => $normalized['EXPEDIENTE']],
                    [
                        'nombre' => $normalized['NOMBRE'],
                        'apellido_pat' => $normalized['APELLIDO_PAT'],
                        'apellido_mat' => $normalized['APELLIDO_MAT'],
                        'curp' => $normalized['CURP'],
                        'department_id' => $deptFinal->id,
                        'puesto' => $normalized['PUESTO'],
                        'tipo' => $this->normalizeTipo($normalized['TIPO']),
                        'email' => $normalized['EMAIL'],
                        'telefono' => $normalized['TELEFONO'],
                        'extension' => $normalized['EXTENSION'],
                        'status' => $normalized['STATUS'],
                    ]
                );
            } catch (\Exception $e) {
                $this->logError($newErrors, $normalized, "Error al guardar: " . $e->getMessage());
            }
        }

        // Actualizar progreso
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

        return [
            'EXPEDIENTE' => $normalized['EXPEDIENTE'] ?? null,
            'NOMBRE' => strtoupper($normalized['NOMBRE'] ?? ''),
            'APELLIDO_PAT' => strtoupper($normalized['APELLIDO_PAT'] ?? ''),
            'APELLIDO_MAT' => strtoupper($normalized['APELLIDO_MAT'] ?? ''),
            'CURP' => strtoupper($normalized['CURP'] ?? ''),
            'UNIDAD' => strtoupper($normalized['UNIDAD'] ?? ''), // Nueva columna
            'DEPARTAMENTO' => strtoupper($normalized['DEPARTAMENTO'] ?? ''),
            'PUESTO' => strtoupper($normalized['PUESTO'] ?? ''),
            'TIPO' => strtoupper($normalized['TIPO'] ?? 'CONFIANZA'),
            'EMAIL' => strtoupper($normalized['EMAIL'] ?? ''),
            'TELEFONO' => strtoupper($normalized['TELEFONO'] ?? ''),
            'EXTENSION' => strtoupper($normalized['EXTENSION'] ?? ''),
            'STATUS' => ucfirst(strtolower($normalized['STATUS'] ?? 'Activo')),
        ];
    }

    private function logError(&$errorsArray, $data, $message)
    {
        $identifier = $data['EXPEDIENTE'] ?? $data['NOMBRE'] ?? 'Fila Desconocida';
        $errorsArray[] = "Expediente {$identifier}: {$message}";
    }

    private function updateTaskProgress($processedCount, $newErrors)
    {
        $task = ImportTask::find($this->taskId);
        if (!$task)
            return;

        $existingErrors = $task->errors ?? [];
        if (!is_array($existingErrors))
            $existingErrors = [];

        $allErrors = array_merge($existingErrors, $newErrors);

        $task->increment('processed_rows', $processedCount);
        $task->update(['errors' => $allErrors]);
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();
                $total = array_sum($totalRows) - count($totalRows);
                ImportTask::where('id', $this->taskId)->update([
                    'total_rows' => $total,
                    'status' => 'processing',
                    'errors' => []
                ]);
            },
            AfterImport::class => function (AfterImport $event) {
                $task = ImportTask::find($this->taskId);
                if ($task && $task->status !== 'canceled') {
                    $status = (count($task->errors ?? []) > 0) ? 'completed_with_errors' : 'completed';
                    $task->update(['status' => $status]);
                }
            },
            ImportFailed::class => function (ImportFailed $event) {
                ImportTask::where('id', $this->taskId)->update([
                    'status' => 'failed',
                    'errors' => array_merge(
                        ImportTask::find($this->taskId)->errors ?? [],
                        ['Fatal Error: ' . $event->getException()->getMessage()]
                    )
                ]);
            },
        ];
    }

    protected function normalizeTipo($tipo)
    {
        return match ($tipo) {
            'SINDICALIZADO' => 'Sindicalizado',
            'CONFIANZA' => 'Confianza',
            'EVENTUAL' => 'Eventual',
            'HONORARIOS' => 'Honorarios',
            'OTRO' => 'Otro',
            default => 'Otro',
        };
    }
}
