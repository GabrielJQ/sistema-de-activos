<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;
use App\Services\AssetService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BulkUpdateTest extends TestCase
{
    // use RefreshDatabase; // Be careful using this on a real dev env if it wipes DB. I'll avoid for now or be careful.
    // Instead of RefreshDatabase, I will create data and delete it.

    public function test_bulk_update_service_logic()
    {
        // 1. Create a dummy user for context if needed (Service uses user sometimes, but bulkUpdateByTag doesn't seemingly use auth directly in the logic part being tested, passing data)
        // Actually the controller uses auth to check roles.

        // 2. Create a dummy Asset
        $tag = 'TEST-BULK-' . rand(1000, 9999);
        $supplier = \App\Models\Supplier::first();
        $department = \App\Models\Department::first();

        if (!$supplier || !$department) {
            $this->markTestSkipped('No hay proveedores o departamentos en la BD para probar.');
        }

        $asset = Asset::create([
            'tag' => $tag,
            'device_type_id' => 1,
            'marca' => 'MarcaOriginal',
            'modelo' => 'ModeloOriginal',
            'estado' => 'OPERACION',
            'serie' => 'SN-' . rand(1000, 9999),
            'supplier_id' => $supplier->id,
            'department_id' => $department->id,
        ]);

        echo "\nCreado activo de prueba: ID {$asset->id} TAG {$tag}\n";
        echo "Marca original: {$asset->marca}\n";

        // 3. Call Service
        $service = app(AssetService::class);

        $data = [
            'bulk_marca' => 'MarcaNueva',
            'bulk_modelo' => 'ModeloNuevo',
            // 'bulk_supplier_id' => ...
        ];

        try {
            $service->bulkUpdateByTag($tag, $data);
            echo "Servicio ejecutado.\n";
        }
        catch (\Exception $e) {
            echo "Excepción: " . $e->getMessage() . "\n";
        }

        // 4. Verify
        $asset->refresh();
        echo "Marca actual: {$asset->marca}\n";
        echo "Modelo actual: {$asset->modelo}\n";

        if ($asset->marca === 'MarcaNueva' && $asset->modelo === 'ModeloNuevo') {
            echo "EXITO: Los cambios se aplicaron.\n";
        }
        else {
            echo "FALLO: Los cambios NO se aplicaron.\n";
        }

        // Cleanup
        $asset->delete();
    }
}
