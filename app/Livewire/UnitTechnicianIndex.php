<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Unit;
use App\Models\Employee;
use App\Models\UnitTechnician;

class UnitTechnicianIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    // Propiedades del Modal
    public $modal_region_id = '';
    public $modal_unit_id = '';
    public $modal_unit_name = '';
    public $employee_id = '';
    public $unitEmployees = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAssignModal($regionId, $unitId, $unitName, $currentEmployeeId = null)
    {
        $this->modal_region_id = $regionId;
        $this->modal_unit_id = $unitId;
        $this->modal_unit_name = $unitName;
        $this->employee_id = $currentEmployeeId ?? '';

        // Cargar empleados activos que pertenezcan a algún departamento de ESTA unidad
        $this->unitEmployees = Employee::withoutGlobalScopes()
            ->where('status', 'Activo')
            ->whereHas('department', fn($q) => $q->where('unit_id', $unitId))
            ->orderBy('nombre')
            ->get();

        // Disparar evento para que JS abra el modal puro de Bootstrap sin bugs
        $this->dispatch('show-technician-modal');
    }

    public function saveTechnician()
    {
        $this->validate([
            'modal_region_id' => 'required',
            'modal_unit_id' => 'required',
            'employee_id' => 'required',
        ], [
            'employee_id.required' => 'Debes seleccionar un empleado de la lista.'
        ]);

        $user = auth()->user();

        // Verificación de Seguridad
        if ($user->role !== 'super_admin' && $user->unit_id != $this->modal_unit_id) {
            abort(403, 'No tienes permiso para modificar esta unidad.');
        }

        UnitTechnician::updateOrCreate(
        ['unit_id' => $this->modal_unit_id],
        [
            'region_id' => $this->modal_region_id,
            'employee_id' => $this->employee_id,
            'is_active' => true,
        ]
        );

        $this->dispatch('hide-technician-modal');

        session()->flash('success', 'Técnico asignado correctamente a ' . $this->modal_unit_name);
    }

    public function render()
    {
        $user = auth()->user();

        // Consultar unidades y hacer pre-carga de relaciones
        $query = Unit::with(['region', 'technician.employee']);

        // Si NO es super admin, forzar a que solo vea SU unidad
        if ($user->role !== 'super_admin') {
            $query->where('id', $user->unit_id);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('uninom', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('region', fn($r) => $r->where('regnom', 'ilike', '%' . $this->search . '%'));
            });
        }

        // Paginamos de 12 en 12 (3 filas de 4 cards)
        $units = $query->orderBy('uninom')->paginate(12);

        return view('livewire.unit-technician-index', [
            'units' => $units,
        ]);
    }
}
