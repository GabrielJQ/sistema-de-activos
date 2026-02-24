<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Unit;
use App\Models\Address;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    //Seed de departamentos y almacenes
    public function run(): void
    {
        $data = [

            // =============================
            // OAXACA – VALLES CENTRALES
            // =============================
            [47, 'OAXACA', 1, 'Valles Centrales', 2051, 'GERENCIA DE SUCURSAL', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2061, 'SUBGERENCIA DE ABASTO', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2066, 'SUBGERENCIA DE OPERACIONES', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2069, 'LOGISTICA Y TRANSPORTES', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2072, 'PRESUPUESTO', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2073, 'CONTABILIDAD', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2074, 'TESORERIA', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2076, 'ADMINISTRACION', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2077, 'PERSONAL', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2078, 'INFORMATICA', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 47116, 'JURIDICO', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 47147, 'CONSEJOS COMUNITARIOS', 'Oficina', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],

            // =============================
            // ALMACENES – VALLES CENTRALES
            // =============================
            [47, 'OAXACA', 1, 'Valles Centrales', 2000, 'ALMACEN CENTRAL', 'Almacen', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2010, 'AYUTLA MIXES', 'Almacen', 'RUMBO AL CALVARIO...', null, '70823', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2014, 'CUAJIMOLOYAS', 'Almacen', 'AV. OAXACA NO. 10', null, '68785', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2017, 'IXTLAN DE JUAREZ', 'Almacen', 'VENUSTIANO CARRANZA', null, '68725', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2020, 'MAGDALENA OCOTLAN', 'Almacen', 'INDEPENDENCIA S/N', null, '71526', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2024, 'SAN ANDRES HIDALGO', 'Almacen', 'JUAREZ INTERIOR S/N', null, '68503', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2025, 'SAN JOSE EL CHILAR', 'Almacen', 'CARRETERA BENITO JUAREZ KM. 135', null, '68500', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2026, 'SAN PEDRO JUCHATENGO', 'Almacen', 'CARRETERA OAXACA - PUERTO ESCONDIDO KM. 153', null, '71920', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2029, 'SANTA MARIA LACHIXIO', 'Almacen', 'HIDALGO S/N', null, '71430', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2031, 'SANTIAGO MATATLAN', 'Almacen', 'PROLONGACION ALDAMA', null, '70440', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2033, 'SANTO TOMAS TAMAZULAPAN', 'Almacen', 'LAZARO CARDENAS S/N', null, '70866', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2036, 'TEOTITLAN DE FLORES MAGON', 'Almacen', 'CARRETERA TEOTITLAN - TEHUACAN KM. 2.5', null, '68540', 'OAXACA', 'OAXACA', 'OAXACA'],
            [47, 'OAXACA', 1, 'Valles Centrales', 2039, 'VALLES CENTRALES', 'Almacen', 'CARRETERA INTERNACIONAL CRISTOBAL COLON NO. 816', 'SANTA ROSA PANZACOLA', '68010', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', 'OAXACA'],

            // =============================
            // ISTMO – DEPARTAMENTOS
            // =============================
            [47, 'OAXACA', 2, 'Istmo', 2078, 'INFORMATICA', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2073, 'CONTABILIDAD', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2076, 'ADMINISTRACION', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2066, 'OPERACIONES', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2069, 'TRANSPORTE', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2061, 'ABASTO', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2074, 'TESORERIA', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2072, 'PRESUPUESTO', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 47116, 'JURIDICO', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2066, 'SUBGERENCIA DE OPERACIONES', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2051, 'SUBGERENCIA', 'Oficina', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2003, 'ALMACEN CENTRAL', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2015, 'TOMATAL', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2018, 'REFORMA', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2019, 'IDEALES', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2021, 'MORRO MAZATLAN', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2022, 'PALOMARES', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2023, 'PUEBLO NUEVO', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2027, 'HUAXPALTEPEC', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2028, 'HUATULCO', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2030, 'LAOLLAGA', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
            [47, 'OAXACA', 2, 'Istmo', 2032, 'NILTEPEC', 'Almacen', null, 'ISTMO', null, 'ISTMO', 'ISTMO', 'OAXACA'],
        ];

        foreach ($data as [
        $regcve, $regnom, $unicve, $uninom, $areacve, $areanom, $tipo,
        $calle, $colonia, $cp, $municipio, $ciudad, $estado
        ]) {

            $region = Region::firstOrCreate(
            ['regcve' => $regcve],
            ['regnom' => $regnom]
            );

            $unit = Unit::firstOrCreate(
            ['unicve' => $unicve, 'region_id' => $region->id],
            ['uninom' => $uninom]
            );

            $address = Address::firstOrCreate(
            ['calle' => $calle, 'cp' => $cp],
            [
                'colonia' => $colonia,
                'municipio' => $municipio,
                'ciudad' => $ciudad,
                'estado' => $estado
            ]
            );

            Department::firstOrCreate(
            [
                'areacve' => (string)$areacve,
                'unit_id' => $unit->id
            ],
            [
                'areanom' => $areanom,
                'tipo' => $tipo,
                'address_id' => $address->id
            ]
            );

        }
    }
}
