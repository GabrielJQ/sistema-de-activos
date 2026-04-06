<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DeviceType;

class DeviceTypeIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    // Resetear la compaginación cuando se realiza una búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = DeviceType::query();

        // Búsqueda
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('equipo', 'ilike', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'ilike', '%' . $this->search . '%');
            });
        }

        // Recuperar y paginar de 6 en 6 por si las imágenes son grandes
        $deviceTypes = $query->orderBy('equipo')->paginate(6);

        return view('livewire.device-type-index', [
            'deviceTypes' => $deviceTypes,
        ]);
    }
}
