<div wire:poll.30s>
    {{-- Caja de Búsqueda Livewire --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted border-end-0">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       class="form-control border-start-0 ps-0" 
                       placeholder="Buscar por nombre, correo, región o unidad...">
            </div>
            
            <div wire:loading wire:target="search" class="text-muted small mt-1">
                <i class="fas fa-spinner fa-spin me-1"></i> Buscando...
            </div>
        </div>
    </div>

    {{-- Controles Pestañas Estilo Moderno por Rol --}}
    <ul class="nav nav-tabs modern-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'all' ? 'active' : '' }}" 
                    wire:click="setTab('all')">
                <i class="fas fa-users me-1"></i> Todos
            </button>
        </li>
        @if(auth()->user()->isSuperAdmin())
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'super_admin' ? 'active' : '' }}" 
                    wire:click="setTab('super_admin')">
                👑 Super Administradores
            </button>
        </li>
        @endif
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'admin' ? 'active' : '' }}" 
                    wire:click="setTab('admin')">
                🛡️ Administradores
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'collaborator' ? 'active' : '' }}" 
                    wire:click="setTab('collaborator')">
                👷 Colaboradores
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'visitor' ? 'active' : '' }}" 
                    wire:click="setTab('visitor')">
                👤 Visitantes
            </button>
        </li>
    </ul>

    {{-- Indicador de carga masiva durante cambios de pestaña --}}
    <div wire:loading wire:target="setTab" class="w-100 text-center py-4">
        <div class="spinner-border text-guinda" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="text-muted mt-2">Cargando datos en vivo...</p>
    </div>

    {{-- Cuadrícula de Usuarios --}}
    <div wire:loading.class="opacity-50">
        <div class="row g-4 mt-2">
            @forelse($users as $user)
                @php 
                    $roleData = $rolesData[$user->role] ?? ['label' => 'Desconocido', 'icon' => '❓'];
                @endphp
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card shadow-sm border-0 rounded-4 user-card h-100" id="user-{{ $user->id }}">
                        <div class="card-body p-4 d-flex flex-column">

                            {{-- Top --}}
                            <div class="user-top">
                                <div class="user-main">
                                    <div class="user-name-line">
                                        <span class="user-role-icon">{!! $roleData['icon'] !!}</span>
                                        <h5 class="card-title fw-bold mb-0 user-name">{{ $user->name }}</h5>
                                    </div>
                                    {{-- Badge estado --}}
                                    <span class="badge px-1 py-2 status badge-status
                                        @if($user->isOnline()) bg-success @else bg-secondary @endif">
                                        <i class="fas @if($user->isOnline()) fa-circle @else fa-circle-notch @endif me-1"></i>
                                        <span class="status-text">
                                            @if($user->isOnline()) En línea&nbsp;&nbsp;@else Desconectado&nbsp; @endif 
                                        </span>
                                    </span>
                                    <hr class="my-2 soft-hr">
                                    <div class="user-email-line text-muted small">
                                        <i class="fas fa-envelope me-1 text-guinda"></i>
                                        <span class="user-email-text fw-semibold">{{ $user->email }}</span>
                                    </div>
                                    <div class="small mt-1 text-muted">
                                        <strong>Rol:</strong> {{ $roleData['label'] }}
                                    </div>
                                </div>
                            </div>

                            {{-- Separador --}}
                            <hr class="my-2 soft-hr">

                            {{-- Info --}}
                            <div class="user-meta d-grid gap-2">
                                <div class="d-flex align-items-start gap-2">
                                    <span class="meta-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <div class="small">
                                        <div class="text-muted">Región</div>
                                        <div class="fw-semibold">{{ $user->region?->regnom ?? '—' }}</div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-2">
                                    <span class="meta-icon">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <div class="small">
                                        <div class="text-muted">Unidad</div>
                                        <div class="fw-semibold">{{ $user->unit?->uninom ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer acciones --}}
                            <div class="mt-auto pt-3">
                                <div class="d-flex justify-content-end gap-2">

                                    {{-- Botón Editar --}}
                                    @if(
                                        (auth()->user()->isSuperAdmin() && $user->id !== auth()->user()->id) ||
                                        (auth()->user()->isAdmin() && !in_array($user->role, ['admin','super_admin']))
                                    )
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="btn btn-sm btn-warning px-3 shadow-sm btn-action"
                                           data-bs-toggle="tooltip"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    {{-- Botón Eliminar --}}
                                    @if(
                                        (auth()->user()->isSuperAdmin() && $user->id !== auth()->user()->id)
                                        ||
                                        (auth()->user()->isAdmin() && !in_array($user->role, ['admin','super_admin']))
                                    )
                                        <button type="button"
                                            class="btn btn-sm btn-danger px-3 shadow-sm btn-action"
                                            data-bs-toggle="tooltip"
                                            title="Eliminar"

                                            onclick="if(confirm('¿Deseas eliminar al usuario?')) document.getElementById('delete-user-{{ $user->id }}').submit();"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <form id="delete-user-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                {{-- Estado vacío por rol --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="empty-icon">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Sin usuarios encontrados</div>
                                <div class="text-muted small">No se encontró ningún usuario con los filtros seleccionados.</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Paginación Automática y Bootstrap --}}
        <div class="mt-4 bg-white p-3 rounded-4 shadow-sm table-card">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Mostrando {{ $users->firstItem() ?? 0 }} a {{ $users->lastItem() ?? 0 }} de {{ $users->total() }} registros.
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
