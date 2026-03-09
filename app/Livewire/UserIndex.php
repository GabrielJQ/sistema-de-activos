<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class UserIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $activeTab = 'all'; // all, super_admin, admin, collaborator, visitor

    // Resetear la pág cada vez que busques
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
        $auth = auth()->user();
        $query = User::query()->with(['region', 'unit']);

        // ========== REGLAS DE SEGURIDAD (Sacadas de UserController) ==========
        if (!$auth->isSuperAdmin()) {
            $query->where('region_id', $auth->region_id);
            if ($auth->unit_id !== null) {
                $query->where('unit_id', $auth->unit_id);
            }
            // Admin no ve super_admin
            $query->where('role', '!=', 'super_admin');
        }

        // ========== FILTRO DE PESTAÑAS (Roles) ==========
        if ($this->activeTab !== 'all') {
            $query->where('role', $this->activeTab);
        }

        // ========== BÚSQUEDA ==========
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                    ->orWhere('email', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('region', function ($qReg) {
                    $qReg->where('regnom', 'ilike', '%' . $this->search . '%');
                }
                )
                    ->orWhereHas('unit', function ($qUnit) {
                    $qUnit->where('uninom', 'ilike', '%' . $this->search . '%');
                }
                );
            });
        }

        // 12 registros por página ajusta perfecto en 1, 2, 3 o 4 columnas 
        $users = $query->latest('id')->paginate(12);

        // Pasamos metadata de roles para la Vista
        $rolesData = [
            'super_admin' => ['label' => 'Super Administradores', 'icon' => '👑'],
            'admin' => ['label' => 'Administradores', 'icon' => '🛡️'],
            'collaborator' => ['label' => 'Colaboradores', 'icon' => '👷'],
            'visitor' => ['label' => 'Visitantes', 'icon' => '👤'],
        ];

        return view('livewire.user-index', [
            'users' => $users,
            'rolesData' => $rolesData
        ]);
    }
}
