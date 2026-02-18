<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    /**
     * Assign multiple assets to an employee.
     */
    public function assignAssets(array $assetIds, ?int $employeeId, string $assignedAt, ?string $observations, string $type, ?string $temporaryHolder)
    {
        return DB::transaction(function () use ($assetIds, $employeeId, $assignedAt, $observations, $type, $temporaryHolder) {
            foreach ($assetIds as $assetId) {
                $assignment = AssetAssignment::assignToEmployee(
                    assetId: $assetId,
                    employeeId: $employeeId,
                    assignedAt: $assignedAt,
                    observations: $observations,
                    assignmentType: $type,
                    temporaryHolder: $temporaryHolder
                );

                /** @var Asset $asset */
                $asset = Asset::find($assetId);
                if ($asset) {
                    $asset->update([
                        'department_id' => $assignment->employee->department_id,
                    ]);
                }
            }
        });
    }

    /**
     * Handle asset return (devolución).
     */
    public function returnAsset(AssetAssignment $assignment)
    {
        return DB::transaction(function () use ($assignment) {
            if (!$assignment->is_current) {
                return;
            }

            $assignment->update([
                'is_current' => \Illuminate\Support\Facades\DB::raw('false'),
                'returned_at' => now(),
            ]);

            // Reassign to technician
            $newAssignment = AssetAssignment::assignToEmployee(
                assetId: $assignment->asset_id,
                employeeId: null,
                assignedAt: now(),
                observations: 'Devolución de activo al Encargado de Informatica.',
                assignmentType: 'normal'
            );

            $asset = $assignment->asset;
            $asset->update([
                'department_id' => $newAssignment->employee->department_id,
                'estado' => 'RESGUARDADO',
            ]);
        });
    }

    /**
     * Finalize decommissioning of assets.
     */
    public function confirmBaja(array $ids, Employee $employee, string $motivo)
    {
        return DB::transaction(function () use ($ids, $employee, $motivo) {
            $assignments = AssetAssignment::with('asset')
                ->whereIn('id', $ids)
                ->where('employee_id', $employee->id)
                ->where('is_current', 'true')
                ->lockForUpdate()
                ->get();

            foreach ($assignments as $a) {
                $a->update([
                    'is_current' => \Illuminate\Support\Facades\DB::raw('false'),
                    'returned_at' => now(),
                    'observations' => $motivo,
                ]);

                $newAssignment = AssetAssignment::assignToEmployee(
                    assetId: $a->asset_id,
                    employeeId: null,
                    assignedAt: now(),
                    observations: 'Liberación/Baja: ' . $motivo,
                    assignmentType: 'normal'
                );

                if ($a->asset) {
                    $a->asset->update([
                        'department_id' => $newAssignment->employee->department_id,
                        'estado' => 'RESGUARDADO',
                    ]);
                }
            }
        });
    }
}
