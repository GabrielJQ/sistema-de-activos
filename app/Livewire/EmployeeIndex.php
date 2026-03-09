<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;

class EmployeeIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $activeTab = 'active'; // 'active', 'inactive'

    // Resetear la paginación cada vez que se busque algo o se cambie de pestaña
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $query = Employee::query()->with('department');

        // Búsqueda por pestaña
        if ($this->activeTab === 'active') {
            $query->where('status', 'Activo');
        }
        else {
            $query->where('status', 'Inactivo');
        }

        // Aplicar filtro de búsqueda
        if ($this->search !== '') {
            $query->where(function ($q) {
                // Nombre completo
                $q->where('nombre', 'ilike', '%' . $this->search . '%')
                    ->orWhere('apellido_pat', 'ilike', '%' . $this->search . '%')
                    ->orWhere('apellido_mat', 'ilike', '%' . $this->search . '%')
                    // Expediente / CURP / Correo
                    ->orWhere('expediente', 'ilike', '%' . $this->search . '%')
                    ->orWhere('curp', 'ilike', '%' . $this->search . '%')
                    ->orWhere('email', 'ilike', '%' . $this->search . '%')
                    // Puesto / Tipo
                    ->orWhere('puesto', 'ilike', '%' . $this->search . '%')
                    ->orWhere('tipo', 'ilike', '%' . $this->search . '%')
                    // Departamento (relación)
                    ->orWhereHas('department', function ($qDept) {
                    $qDept->where('areanom', 'ilike', '%' . $this->search . '%');
                }
                );
            });
        }

        $employees = $query->latest('id')->paginate(10);

        return view('livewire.employee-index', [
            'employees' => $employees,
        ]);
    }
}
