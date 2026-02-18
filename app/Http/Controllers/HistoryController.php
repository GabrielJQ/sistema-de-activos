<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetAssignment;
use App\Models\Employee;
use App\Models\Asset;
use Barryvdh\DomPDF\Facade\Pdf;


class HistoryController extends Controller
{
    // Mostrar vista principal del historial
    public function index()
    {
        // Solo empleados con historial
        $employees = Employee::whereHas('assetAssignments')
            ->with(['currentAssets.deviceType', 'department'])
            ->get();

        //Todos los activos con sus relaciones
        $assets = Asset::with([
            'deviceType',
            'department',
            'assignments.employee.department'
        ])->get();

        return view('history.index', compact('employees', 'assets'));
    }

    // Mostrar historial de un empleado específico
    public function showEmployee(Employee $employee)
    {
        $history = AssetAssignment::with([
            'asset.deviceType',
            'asset.department'
        ])
        ->where('employee_id', $employee->id)
        ->orderByDesc('assigned_at')
        ->get();

        return view('history.show_employee', compact('employee', 'history'));
    }

    // Mostrar historial de un activo específico
    public function showAsset(Asset $asset)
    {
        $history = AssetAssignment::with([
            'employee.department'
        ])
        ->where('asset_id', $asset->id)
        ->orderByDesc('assigned_at')
        ->get();

        return view('history.show_asset', compact('asset', 'history'));
    }
    public function generateEmployeeReport($employeeId)
    {
        $employee = Employee::with(['assetAssignments.asset.deviceType', 'assetAssignments.asset.department'])->findOrFail($employeeId);

        // Prepara los datos para la tabla igual que en la vista web
        $data = $employee->assetAssignments->map(function($assignment) {
            $asset = $assignment->asset;
            $isDecommissioned = $asset->isDecommissioned();

            // Fecha de devolución o baja
            $returnedDate = $assignment->returned_at
                ?? ($assignment->is_current && $isDecommissioned ? $asset->updated_at : null);

            // Observaciones
            $observations = $isDecommissioned
                ? ($assignment->observations ?? 'Dado de baja')
                : ($assignment->observations ?? '-');

            return [
                'Activo' => $asset->tag ?? '-',
                'Tipo' => $asset->deviceType?->equipo ?? '-',
                'Serie' => $asset->serie ?? '-',
                'Departamento' => $asset->department?->areanom ?? '-',
                'Asignado' => $assignment->assigned_at->format('d/m/Y'),
                'Devuelto' => $returnedDate ? $returnedDate->format('d/m/Y') : '-',
                'Observaciones' => $observations,
                'Estado' => $isDecommissioned ? 'Baja' : 'En operación',
                'isDecommissioned' => $isDecommissioned 
            ];
        });

        $pdf = Pdf::loadView('history.employee_assets_pdf', [
            'employee' => $employee,
            'data' => $data
        ]);

        $filename = 'relacionActivos_'.$employee->full_name.'_'.now()->format('Y-m-d_H-i-s').'.pdf';
        return $pdf->stream($filename);
    }


}
