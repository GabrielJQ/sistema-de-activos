<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;

class DepartmentIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $tipo = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTipo()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Department::with(['address', 'unit.region']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('areanom', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('unit', fn($u) => $u->where('uninom', 'ilike', '%' . $this->search . '%'))
                    ->orWhereHas('address', fn($a) =>
                $a->where('calle', 'ilike', '%' . $this->search . '%')
                ->orWhere('colonia', 'ilike', '%' . $this->search . '%')
                ->orWhere('cp', 'like', '%' . $this->search . '%')
                );
            });
        }

        if ($this->tipo !== '') {
            $query->where('tipo', $this->tipo);
        }

        $departments = $query->orderBy('areanom')->paginate(6);

        return view('livewire.department-index', [
            'departments' => $departments,
        ]);
    }
}
