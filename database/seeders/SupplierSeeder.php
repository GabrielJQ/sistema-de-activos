<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        // Define la lista de proveedores
        $suppliers = [
            [
                'prvnombre' => 'FOCUS',
                'contrato' => 'OC-ALIMENTACION-040-2025',
                'logo_path' => 'images/logos/focus.png',
                'enlace' => 'Noemí Tizapantzi O.C',
                'telefono' => '+52 1 55 3733 3218',
            ],
            [
                'prvnombre' => 'SYNNEX',
                'contrato' => null,
                'logo_path' => 'images/logos/synnex.png',
                'enlace' => 'Noemí Tizapantzi O.C',
                'telefono' => '+52 1 55 3733 3218',
            ],
            [
                'prvnombre' => 'INDUSTRIAS SANDOVAL',
                'contrato' => 'PSG/053/2025',
                'logo_path' => 'images/logos/sandoval.png',
                'enlace' => 'Geovany',
                'telefono' => '+52 1 55 1703 9660',
            ],
            [
                'prvnombre' => 'STE',
                'contrato' => 'Oc-CM-054-2024',
                'logo_path' => 'images/logos/ste.png',
                'enlace' => 'Noemí Tizapantzi O.C',
                'telefono' => '+52 1 55 3733 3218',
            ],
            [
                'prvnombre' => 'ALIMENTACION PARA EL BIENESTAR',
                'contrato' => null,
                'logo_path' => 'images/logos/logoAlimentacionBienestar.png',
                'enlace' => 'Felix Fortino Armengol Ricardez',
                'telefono' => '+52 951 134 3540',
            ],
        ];

        foreach ($suppliers as $data) {
            Supplier::create([
                'prvnombre' => $data['prvnombre'],
                'contrato' => $data['contrato'],
                'prvstatus' => DB::raw('true'), // Fix for PostgreSQL strict types
                'logo_path' => $data['logo_path'],
                'enlace' => $data['enlace'],
                'telefono' => $data['telefono'],
            ]);
        }
    }
}
