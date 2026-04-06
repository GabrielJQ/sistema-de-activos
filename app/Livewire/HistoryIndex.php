<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\Asset;

/**
 * Componente Livewire: HistoryIndex
 * 
 * PROPÓSITO:
 * Este componente fue creado para reemplazar la antigua vista principal del Historial,
 * resolviendo un grave problema de "Memory Leak" (Cuello de botella de memoria).
 * Anteriormente, el HistoryController extraía el 100% de los empleados y activos a la RAM.
 * 
 * OPTIMIZACIÓN:
 * Ahora usamos "Lazy Loading" (Carga diferida) asíncrona mediante Livewire.
 * El servidor procesa únicamente 10 registros a la vez, interactuando directamente 
 * a nivel de SQL. Esto blinda al sistema contra colapsos de memoria (Out of Memory)
 * sin importar el crecimiento de la base de datos a futuro.
 */
class HistoryIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $activeTab = 'employees'; // employees, assets
    public $searchEmployees = '';
    public $searchAssets = '';

    public function updatingSearchEmployees()
    {
        $this->resetPage('employeesPage');
    }

    public function updatingSearchAssets()
    {
        $this->resetPage('assetsPage');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage('employeesPage');
        $this->resetPage('assetsPage');
    }

    public function render()
    {
        $employees = [];
        $assets = [];

        if ($this->activeTab === 'employees') {
            /**
             * ESTRATEGIA DE OPTIMIZACIÓN (Empleados):
             * Solo consultamos empleados que tengan asginaciones.
             * Usamos 'with' para traer relaciones pre-cargadas (Evita N+1 local).
             * IMPORTANTE: No usamos ->get() aquí, armamos el "query builder"
             * para que Laravel lo ponga en cola.
             */
            $queryEmp = Employee::whereHas('assetAssignments')
                ->with(['currentAssets.deviceType', 'department']);

            if ($this->searchEmployees !== '') {
                // Filtro asíncrono atado a wire:model.live.debounce
                $queryEmp->where(function ($q) {
                    $q->where('nombre', 'ilike', '%' . $this->searchEmployees . '%')
                        ->orWhere('apellido_pat', 'ilike', '%' . $this->searchEmployees . '%')
                        ->orWhere('apellido_mat', 'ilike', '%' . $this->searchEmployees . '%')
                        ->orWhereHas('department', function ($qd) {
                        $qd->where('areanom', 'ilike', '%' . $this->searchEmployees . '%');
                    }
                    );
                });
            }

            // ->paginate() es la clave de la optimización: 
            // Solo descarga los 10 registros exactos de esta página,
            // reduciendo el consumo de RAM de cientos de Megabytes a unos pocos Kilobytes.
            $employees = $queryEmp->orderBy('nombre')->paginate(10, ['*'], 'employeesPage');
        }
        else {
            /**
             * ESTRATEGIA DE OPTIMIZACIÓN (Bienes/Activos):
             * Igual que en empleados, preparamos la consulta pero NO extraemos los datos todavía.
             */
            $queryAss = Asset::has('assignments')
                ->with([
                'deviceType',
                'department',
            ]);

            if ($this->searchAssets !== '') {
                $queryAss->where(function ($q) {
                    $q->where('tag', 'ilike', '%' . $this->searchAssets . '%')
                        ->orWhere('serie', 'ilike', '%' . $this->searchAssets . '%')
                        ->orWhereHas('deviceType', function ($qd) {
                        $qd->where('equipo', 'ilike', '%' . $this->searchAssets . '%');
                    }
                    );
                });
            }

            // Solo 10 activos directos desde PostgreSQL a memoria.
            $assets = $queryAss->orderBy('tag')->paginate(10, ['*'], 'assetsPage');
        }

        return view('livewire.history-index', [
            'employees' => $employees,
            'assets' => $assets,
        ]);
    }
}
