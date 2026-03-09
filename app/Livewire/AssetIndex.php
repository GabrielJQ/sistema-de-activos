<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Builder;

class AssetIndex extends Component
{
    use WithPagination;

    // Usamos el tema de bootstrap para la paginación de Livewire
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $activeTab = 'assigned'; // 'assigned', 'unassigned', 'damaged', 'inactive'

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
        $query = Asset::query()->with(['deviceType', 'supplier', 'department', 'currentHolder.employee']);

        // Obtenemos la lista de equipos principales (configurada en config/assets.php)
        $mainDevices = config('assets.main_devices') ?? [
            'Equipo All In One',
            'Equipo Escritorio',
            'Escritorio Avanzada',
            'Laptop de Avanzada',
            'Laptop de Intermedia'
        ];

        // Aplicamos la lógica de pestañas (similar a cómo lo hacía tu controlador)
        switch ($this->activeTab) {
            case 'assigned':
                $query->whereNotIn('estado', ['BAJA', 'SINIESTRO', 'RESGUARDADO'])
                    ->whereHas('currentHolder') // Tiene resguardante
                    ->whereHas('deviceType', function ($q) use ($mainDevices) {
                    $q->whereIn('equipo', $mainDevices);
                });
                break;
            case 'unassigned':
                $query->whereNotIn('estado', ['BAJA', 'SINIESTRO', 'OPERACION'])
                    ->where('estado', 'RESGUARDADO')
                    ->whereHas('deviceType', function ($q) use ($mainDevices) {
                    $q->whereIn('equipo', $mainDevices);
                });
                break;
            case 'damaged':
                $query->where('estado', 'DANADO');
                break;
            case 'inactive':
                $query->where('estado', 'BAJA');
                break;
        }

        // Aplicar el filtro de búsqueda si el usuario escribe algo
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('tag', 'ilike', '%' . $this->search . '%')
                    ->orWhere('serie', 'ilike', '%' . $this->search . '%')
                    ->orWhere('marca', 'ilike', '%' . $this->search . '%')
                    ->orWhere('modelo', 'ilike', '%' . $this->search . '%')
                    // Buscar por equipo de la relación deviceType
                    ->orWhereHas('deviceType', function ($qDevice) {
                    $qDevice->where('equipo', 'ilike', '%' . $this->search . '%');
                }
                )
                    // Buscar por nombre de empleado de la relación currentHolder -> employee
                    ->orWhereHas('currentHolder.employee', function ($qEmp) {
                    $qEmp->where('nombre', 'ilike', '%' . $this->search . '%')
                        ->orWhere('apellido_pat', 'ilike', '%' . $this->search . '%')
                        ->orWhere('apellido_mat', 'ilike', '%' . $this->search . '%');
                }
                );
            });
        }

        // Paginar resultados a 15 por página
        $assets = $query->latest('id')->paginate(10);

        return view('livewire.asset-index', [
            'assets' => $assets,
        ]);
    }
}
