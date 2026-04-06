<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class AssetAssignmentIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $query = Employee::with([
            'department:id,areanom,unit_id',
            'currentAssets' => fn($q) => $q->select('assets.id', 'assets.device_type_id'),
            'currentAssets.deviceType:id,equipo'
        ])
            ->whereHas('assetAssignments', function ($q) {
            $q->where('is_current', DB::raw('true'));
        });

        // Filtrado por contexto de región/unidad basado en Rol
        // Si no es super admin, las reglas globales "RegionUnitScope" lo filtrarán a su unidad automáticamente (como está definido en el modelo)
        // Pero para asegurar que no rompa o salte el scope:
        if ($user && $user->role !== 'super_admin' && $user->unit_id) {
            $query->whereHas('department', function ($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            });
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('nombre', 'ilike', '%' . $this->search . '%')
                    ->orWhere('apellido_pat', 'ilike', '%' . $this->search . '%')
                    ->orWhere('apellido_mat', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('department', function ($q) {
                    $q->where('areanom', 'ilike', '%' . $this->search . '%');
                }
                );
            });
        }

        $employees = $query->orderBy('nombre')->paginate(10);

        return view('livewire.asset-assignment-index', [
            'employees' => $employees,
        ]);
    }
}
