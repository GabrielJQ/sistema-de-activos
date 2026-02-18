<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportEmployees extends Command
{
    protected $signature = 'import:employees {file=database/seeders/empleados.csv}';
    protected $description = 'Importar empleados desde un archivo CSV con validación';

    public function handle()
    {
        $file = base_path($this->argument('file'));

        if (!file_exists($file) || !is_readable($file)) {
            $this->error("El archivo $file no existe o no es legible.");
            return 1;
        }

        $header = null;
        $data = [];
        $lineNumber = 1;

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = $row;
                    $this->info("Encabezados detectados: " . implode(', ', $header));
                    continue;
                }
                
                $lineNumber++;
                if (count($row) != count($header)) {
                    $this->error("Error en línea $lineNumber: número de columnas no coincide.");
                    continue;
                }

                $record = array_combine($header, $row);

                // Validar campos básicos antes de agregar
                if (empty($record['nombre']) || empty($record['apellido_pat']) || empty($record['expediente'])) {
                    $this->warn("Fila $lineNumber ignorada: faltan datos obligatorios (nombre, apellido_pat o expediente).");
                    continue;
                }

                // Validar que department_id sea entero o null
                $department_id = null;
                if (isset($record['department_id']) && is_numeric($record['department_id']) && $record['department_id'] !== '') {
                    $department_id = (int) $record['department_id'];
                }
                // Preparar datos para inserción
                $data[] = [
                    'expediente' => $record['expediente'] ?? null,
                    'nombre' => $record['nombre'] ?? null,
                    'apellido_pat' => $record['apellido_pat'] ?? null,
                    'apellido_mat' => $record['apellido_mat'] ?? null,
                    'curp' => $record['curp'] ?? null,
                    'department_id' => $department_id,
                    'puesto' => $record['puesto'] ?? null,
                    'tipo' => $record['tipo'] ?? 'Confianza',
                    'email' => $record['email'] ?? null,
                    'telefono' => $record['telefono'] ?? null,
                    'extension' => $record['extension'] ?? null,
                    'status' => $record['status'] ?? 'Activo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            fclose($handle);
        }

        if (empty($data)) {
            $this->warn("No se encontraron registros válidos para insertar.");
            return 0;
        }

        DB::table('employees')->insert($data);
        $this->info("Se insertaron " . count($data) . " empleados desde el CSV.");

        return 0;
    }
}
