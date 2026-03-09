<div>
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
                       placeholder="Buscar por TAG, Serie, Marca, Modelo, Usuario o Tipo...">
            </div>
            
            <div wire:loading wire:target="search" class="text-muted small mt-1">
                <i class="fas fa-spinner fa-spin me-1"></i> Buscando...
            </div>
        </div>
    </div>

    {{-- Controles Pestañas Estilo Moderno --}}
    <ul class="nav nav-tabs asset-tabs border-0 mt-3 mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'assigned' ? 'active' : '' }}" 
                    wire:click="setTab('assigned')">
                <i class="fas fa-user-check me-1"></i> Asignados
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'unassigned' ? 'active' : '' }}" 
                    wire:click="setTab('unassigned')">
                <i class="fas fa-user-clock me-1"></i> Disponibles
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'damaged' ? 'active' : '' }}" 
                    wire:click="setTab('damaged')">
                <i class="fas fa-exclamation-triangle me-1"></i> Dañados
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'inactive' ? 'active' : '' }}" 
                    wire:click="setTab('inactive')">
                <i class="fas fa-ban me-1"></i> Inactivos
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

    {{-- Tabla de Datos Transparente ante la Carga --}}
    <div class="table-card" wire:loading.class="opacity-50">
        <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
            <table class="table table-hover table-striped table-sm align-middle modern-table">
                <thead class="table-dark text-white">
                    <tr>
                        <th>TAG</th>
                        <th>Equipo</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Serie</th>
                        <th>Estado</th>
                        <th>Resguardo</th>
                        <th>Departamento</th>
                        <th class="{{ hasRole(['super_admin','admin']) ? '' : 'd-none' }}">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $activo)
                        <tr>
                            <td class="fw-semibold">{{ $activo->tag }}</td>

                            <td>{{ $activo->deviceType->equipo ?? '—' }}</td>
                            <td>{{ $activo->marca }}</td>
                            <td>{{ $activo->modelo }}</td>
                            <td>{{ $activo->serie }}</td>

                            {{-- Estado --}}
                            <td>
                                @if($activo->estado === 'DANADO')
                                    <span class="badge" title="En uso pero dañado">
                                        <i class="fas fa-exclamation-triangle text-danger"></i>
                                        {{ $activo->estado }}
                                    </span>
                                @else
                                    {{ $activo->estado }}
                                @endif
                            </td>

                            {{-- Resguardo --}}
                            <td>
                                {{ $activo->currentHolder?->employee->full_name ?? 'Informática' }}
                            </td>

                            {{-- Departamento --}}
                            <td>
                                @if($activo->estado === 'BAJA')
                                    <span class="badge bg-danger">INACTIVO</span>
                                @elseif(empty($activo->currentHolder))
                                    <span class="badge bg-warning text-dark">DISPONIBLE</span>
                                @else
                                    {{ $activo->department->areanom ?? '—' }}
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="{{ hasRole(['super_admin','admin']) ? '' : 'd-none' }} text-center">
                                @if(hasRole(['super_admin','admin']))
                                    @if($activeTab !== 'damaged' && $activeTab !== 'inactive')
                                        <a href="{{ route('assets.group', $activo->tag) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detalles
                                        </a>
                                    @endif

                                    @if($activeTab === 'damaged')
                                        <a href="{{ route('assets.edit', $activo->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="{{ route('assets.report', $activo->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-file-alt"></i> Reportar
                                        </a>
                                    @endif

                                    @if($activeTab === 'inactive')
                                        <button class="btn btn-danger btn-sm text-white-hover"
                                            {{-- Normalmente usaríamos Alpine/JS para el data-confirm aquí --}}
                                            onclick="if(confirm('¿Eliminar este activo permanentemente?')) document.getElementById('delete-form-{{ $activo->id }}').submit();"
                                        >
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                        <form id="delete-form-{{ $activo->id }}" action="{{ route('assets.destroy', $activo->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fas fa-search fa-2x mb-3 text-light"></i><br>
                                No se encontraron registros de activos en este estado o para la búsqueda ingresada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Paginación Automática y Bootstrap - Esto es lo importante --}}
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Mostrando {{ $assets->firstItem() ?? 0 }} a {{ $assets->lastItem() ?? 0 }} de {{ $assets->total() }} registros.
                </div>
                <div>
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
