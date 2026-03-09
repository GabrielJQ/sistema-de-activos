<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;

class SupplierIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    // Resetear la pág cada vez que busques
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Supplier::query();

        // ========== BÚSQUEDA ==========
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('prvnombre', 'ilike', '%' . $this->search . '%')
                    ->orWhere('contrato', 'ilike', '%' . $this->search . '%')
                    ->orWhere('telefono', 'ilike', '%' . $this->search . '%')
                    ->orWhere('enlace', 'ilike', '%' . $this->search . '%');
            });
        }

        // Cargar 9 registros porque las tarjetas son de 3 en 3 (o 2 en 2 en tablet)
        $suppliers = $query->orderBy('prvnombre')->paginate(9);

        return view('livewire.supplier-index', [
            'suppliers' => $suppliers,
        ]);
    }
}
