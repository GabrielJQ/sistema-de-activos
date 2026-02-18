<?php
$content = <<<'EOD'
<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetNetworkInterface;
use App\Models\UnitTechnician;
use Illuminate\Support\Facades\DB;

class AssetService
{
    /**
     * Create a new asset and handle automatic assignment.
     */
    public function createAsset(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $existingAssets = collect();
            $modoRegistro = $data['modo_registro'] ?? 'ALTA';

            if ($modoRegistro === 'REEMPLAZO') {
                $existingAssets = Asset::where('tag', $data['tag'])
                    ->where('device_type_id', $data['device_type_id'])
                    ->where('estado', '!=', 'BAJA')
                    ->get();

                if ($existingAssets->isEmpty()) {
                    throw new \Exception('No existe un activo previo para reemplazar con ese TAG y tipo.');
                }

                // Inherit state from the principal asset
                $data['estado'] = $existingAssets->first()->estado;
            }

            // Create new asset
            $assetData = collect($data)->except(['ip_address', 'modo_registro'])->toArray();
            $newAsset = Asset::create($assetData);

            if (!empty($data['ip_address'])) {
                AssetNetworkInterface::create([
                    'asset_id' => $newAsset->id,
                    'ip_address' => $data['ip_address'],
                ]);
            }

            // Automatic assignment logic
            $this->handleInitialAssignment($newAsset, $user, $existingAssets);

            return $newAsset;
        });
    }

    /**
     * Update an asset and its network interface.
     */
    public function updateAsset(Asset $asset, array $data)
    {
        return DB::transaction(function () use ($asset, $data) {
            $asset->update(collect($data)->except('ip_address')->toArray());

            if (!empty($data['ip_address'])) {
                AssetNetworkInterface::updateOrCreate(
                    ['asset_id' => $asset->id],
                    ['ip_address' => $data['ip_address']]
                );
            } else {
                $asset->networkInterface()->delete();
            }

            // If state changes to decommissioning states, close assignments
            if (in_array($data['estado'], ['BAJA', 'SINIESTRO'])) {
                $this->closeCurrentAssignment($asset, 'Activo marcado como ' . $data['estado']);
            }

            return $asset;
        });
    }

    /**
     * Handle initial assignment or replacement logic.
     */
    protected function handleInitialAssignment(Asset $newAsset, $user, $existingAssets)
    {
        $technician = UnitTechnician::getTechnicianForUser($user);

        if (!$technician) {
            throw new \Exception('No hay técnico asignado a esta unidad.');
        }

        // Initial assignment to technician
        AssetAssignment::assignToEmployee(
            assetId: $newAsset->id,
            employeeId: $technician->id,
            assignedAt: now(),
            observations: 'Asignación automática al técnico de la unidad',
            assignmentType: 'normal'
        );

        $newAsset->update([
            'department_id' => $technician->department_id,
            'estado' => 'RESGUARDADO',
        ]);

        /** @var Asset $oldAsset */
        foreach ($existingAssets as $oldAsset) {
            $currentAssignment = $oldAsset->assignments()
                ->where('is_current', \Illuminate\Support\Facades\DB::raw('true'))
                ->first();

            if ($currentAssignment) {
                // Close old
                $currentAssignment->update([
                    'is_current' => \Illuminate\Support\Facades\DB::raw('false'),
                    'returned_at' => now(),
                    'observations' => 'Activo reemplazado por un nuevo activo',
                ]);

                // Copy assignment to new asset
                $newAssignment = AssetAssignment::assignToEmployee(
                    assetId: $newAsset->id,
                    employeeId: $currentAssignment->employee_id,
                    assignedAt: now(),
                    observations: 'Asignación automática por sustitución de activo',
                    assignmentType: 'normal'
                );

                $newAsset->update(['department_id' => $newAssignment->employee->department_id]);
            }

            $oldAsset->update(['estado' => 'BAJA']);
        }
    }

    /**
     * Close the current assignment for an asset.
     */
    public function closeCurrentAssignment(Asset $asset, string $observations)
    {
        $currentAssignment = $asset->assignments()->where('is_current', \Illuminate\Support\Facades\DB::raw('true'))->first();

        if ($currentAssignment) {
            $currentAssignment->update([
                'is_current' => \Illuminate\Support\Facades\DB::raw('false'),
                'returned_at' => now(),
                'observations' => $observations,
            ]);
        }
    }

    /**
     * Bulk update assets by tag.
     */
    public function bulkUpdateByTag(string $tag, array $data)
    {
        return DB::transaction(function () use ($tag, $data) {
            $assets = Asset::where('tag', $tag)
                ->where('estado', '!=', 'BAJA')
                ->get();

            if ($assets->isEmpty()) {
                throw new \Exception('No hay activos activos para modificar en este TAG.');
            }

            // Mapear claves bulk_ a columnas reales y filtrar nulos
            $updates = [];
            if (!empty($data['bulk_supplier_id'])) $updates['supplier_id'] = $data['bulk_supplier_id'];
            if (!empty($data['bulk_department_id'])) $updates['department_id'] = $data['bulk_department_id'];
            if (!empty($data['bulk_marca'])) $updates['marca'] = $data['bulk_marca'];
            if (!empty($data['bulk_modelo'])) $updates['modelo'] = $data['bulk_modelo'];

            if (!empty($updates)) {
                Asset::where('tag', $tag)->where('estado', '!=', 'BAJA')->update($updates);
            }

            if (isset($data['bulk_estado'])) {
                $newStatus = $data['bulk_estado'];
                Asset::where('tag', $tag)->where('estado', '!=', 'BAJA')->update(['estado' => $newStatus]);

                if (in_array($newStatus, ['BAJA', 'SINIESTRO'])) {
                    /** @var Asset $asset */
                    foreach ($assets as $asset) {
                        if ($asset instanceof Asset) {
                            $this->closeCurrentAssignment($asset, 'Actualización masiva por TAG (' . $tag . ')');
                        }
                    }
                }
            }

            return true;
        });
    }
}
EOD;

file_put_contents('app/Services/AssetService.php', $content);
echo "AssetService cleaned successfully";
