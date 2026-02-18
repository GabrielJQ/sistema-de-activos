namespace App\Services;

use App\Models\Department;

class DepartmentProvisioner
{
    public static function ensureBaseDepartmentsForUnit($unitId)
    {
        $base = [
            ['areacve' => 99901, 'areanom' => 'INFORMATICA', 'tipo' => 'Oficina'],
            ['areacve' => 99902, 'areanom' => 'ADMINISTRACION', 'tipo' => 'Oficina'],
            ['areacve' => 99903, 'areanom' => 'PERSONAL', 'tipo' => 'Oficina'],
            ['areacve' => 99904, 'areanom' => 'TESORERIA', 'tipo' => 'Oficina'],
            ['areacve' => 99905, 'areanom' => 'CONTABILIDAD', 'tipo' => 'Oficina'],
            ['areacve' => 99906, 'areanom' => 'LOGISTICA', 'tipo' => 'Oficina'],
            ['areacve' => 99907, 'areanom' => 'ALMACEN', 'tipo' => 'Almacen'],
        ];

        foreach ($base as $dept) {
            Department::firstOrCreate(
                ['areacve' => $dept['areacve'], 'unit_id' => $unitId],
                ['areanom' => $dept['areanom'], 'tipo' => $dept['tipo']]
            );
        }
    }
}
