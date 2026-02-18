<?php

namespace App\Http\Controllers;

use App\Models\UnitTechnician;
use App\Models\Region;
use App\Models\Unit;
use App\Models\Employee;
use Illuminate\Http\Request;

class UnitTechnicianController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Super Admin → TODAS LAS UNIDADES
        if ($user->role === 'super_admin') {

            $regions = Region::with(['units.technician.employee'])->get();

        }
        // Admin de Unidad → SOLO SU UNIDAD
        else {

            $regions = Region::whereHas('units', function ($q) use ($user) {
                $q->where('id', $user->unit_id);
            })
                ->with([
                    'units' => function ($q) use ($user) {
                        $q->where('id', $user->unit_id)
                            ->with('technician.employee');
                    }
                ])
                ->get();
        }

        return view('unit_technicians.index', compact('regions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'region_id' => 'required|exists:regions,id',
            'unit_id' => 'required|exists:units,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $user = auth()->user();

        // Security Check: If not super_admin, can only assign to own unit
        if ($user->role !== 'super_admin' && $user->unit_id != $request->unit_id) {
            abort(403, 'No tienes permiso para modificar esta unidad.');
        }

        UnitTechnician::updateOrCreate(
            ['unit_id' => $request->unit_id],
            [
                'region_id' => $request->region_id,
                'employee_id' => $request->employee_id,
                'is_active' => true,
            ]
        );

        return back()->with('success', 'Técnico asignado correctamente.');
    }
    public function employeesByUnit(Unit $unit)
    {
        $user = auth()->user();

        // Security Check
        if ($user->role !== 'super_admin' && $user->unit_id != $unit->id) {
            abort(403, 'Unauthorized');
        }

        $employees = Employee::withoutGlobalScopes()
            ->where('status', 'Activo')
            ->whereHas(
                'department',
                fn($q) =>
                $q->where('unit_id', $unit->id)
            )
            ->orderBy('nombre')
            ->get();

        return response()->json(
            $employees->map(fn($e) => [
                'id' => $e->id,
                'text' => $e->full_name,
            ])
        );
    }


}
