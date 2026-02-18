<?php

namespace App\Imports;

use App\Models\Region;
use App\Models\Unit;
use App\Models\Department;
use App\Models\Address;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StructureImport implements ToCollection, WithHeadingRow
{
    protected $requiredColumns = [
        'regcve','regnom','unicve','uninom','areacve','areanom','tipo'
    ];

    // Importar estructura organizacional desde Excel/CSV
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception("El archivo está vacío.");
        }

        $headers = array_map('strtolower', $rows->first()->keys()->toArray());

        foreach ($this->requiredColumns as $required) {
            if (!in_array($required, $headers)) {
                throw new \Exception("Falta la columna obligatoria: {$required}");
            }
        }

        foreach ($rows as $index => $row) {

            foreach ($this->requiredColumns as $col) {
                if (empty($row[$col])) {
                    continue 2; // 🔥 NO aborta todo
                }
            }

            // ===============================
            // Normalización
            // ===============================
            $regcve  = (int) $row['regcve'];
            $regnom  = strtoupper(trim($row['regnom']));
            $unicve  = (int) $row['unicve'];
            $uninom  = strtoupper(trim($row['uninom']));
            $areacve = (int) $row['areacve'];
            $areanom = strtoupper(trim($row['areanom']));
            $tipo    = ucfirst(strtolower(trim($row['tipo'] ?? 'Oficina')));

            // ===============================
            // REGIÓN
            // ===============================
            $region = Region::updateOrCreate(
                ['regcve' => $regcve],
                ['regnom' => $regnom]
            );

            // ===============================
            // UNIDAD
            // ===============================
            $unit = Unit::updateOrCreate(
                [
                    'unicve' => $unicve,
                    'region_id' => $region->id
                ],
                ['uninom' => $uninom]
            );

            $address = null;

            $hasAddress = !empty($row['calle']) ||
                          !empty($row['cp']) ||
                          !empty($row['colonia']) ||
                          !empty($row['municipio']) ||
                          !empty($row['ciudad']) ||
                          !empty($row['estado']);

            if ($hasAddress) {
                $address = Address::updateOrCreate(
                    [
                        'calle' => strtoupper(trim($row['calle'] ?? '')),
                        'cp'    => trim($row['cp'] ?? '')
                    ],
                    [
                        'colonia'   => strtoupper(trim($row['colonia'] ?? '')),
                        'municipio' => strtoupper(trim($row['municipio'] ?? '')),
                        'ciudad'    => strtoupper(trim($row['ciudad'] ?? '')),
                        'estado'    => strtoupper(trim($row['estado'] ?? '')),
                    ]
                );
            }

            Department::updateOrCreate(
                [
                    'areacve' => $areacve,
                    'unit_id' => $unit->id
                ],
                [
                    'areanom'    => $areanom,
                    'tipo'       => in_array($tipo, ['Oficina','Almacen','Otro']) ? $tipo : 'Oficina',
                    'address_id' => $address?->id
                ]
            );
        }
    }
}