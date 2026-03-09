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
                       placeholder="Buscar por Expediente, Nombre, Tipo, Correo o Departamento...">
            </div>
            
            <div wire:loading wire:target="search" class="text-muted small mt-1">
                <i class="fas fa-spinner fa-spin me-1"></i> Buscando...
            </div>
        </div>
    </div>

    {{-- Controles Pestañas Estilo Moderno --}}
    <ul class="nav nav-tabs modern-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'active' ? 'active' : '' }}" 
                    wire:click="setTab('active')">
                <i class="fas fa-user-check me-1"></i> Activos
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link custom-tab {{ $activeTab === 'inactive' ? 'active' : '' }}" 
                    wire:click="setTab('inactive')">
                <i class="fas fa-user-slash me-1"></i> Inactivos
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
                        <th>Expediente</th>
                        <th>Nombre completo</th>
                        <th>Departamento</th>
                        <th>Puesto</th>
                        <th>Tipo</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Extensión</th>
                        <th>Estado</th>
                        <th class="{{ hasRole(['super_admin','admin','editor']) ? '' : 'd-none' }}">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td class="fw-semibold">{{ $emp->expediente }}</td>
                            <td>{{ $emp->full_name }}</td>

                            <td class="text-muted">
                                <i class="fas fa-building me-1"></i>
                                {{ $emp->department->areanom ?? '—' }}
                            </td>

                            <td>{{ $emp->puesto }}</td>
                            <td>{{ $emp->tipo }}</td>
                            <td>{{ $emp->email }}</td>
                            <td>{{ $emp->telefono }}</td>
                            <td>{{ $emp->extension }}</td>

                            {{-- ESTADO --}}
                            <td class="text-center">
                                <span class="badge {{ $emp->status === 'Activo' ? 'bg-success' : 'bg-danger' }} px-2 py-1 badge-soft text-white">
                                    {{ $emp->status }}
                                </span>
                            </td>

                            {{-- ACCIONES --}}
                            <td class="{{ hasRole(['super_admin','admin','editor']) ? '' : 'd-none' }} text-center">

                                @if(hasRole(['super_admin','admin','editor']))
                                    
                                    {{-- EDITAR --}}
                                    <a href="{{ route('employees.edit', $emp->id) }}"
                                       class="btn btn-warning btn-sm mb-1">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- ELIMINAR --}}
                                    <button type="button"
                                            class="btn btn-danger btn-sm mb-1"
                                            onclick="if(confirm('¿Deseas eliminar este empleado?')) document.getElementById('delete-employee-{{ $emp->id }}').submit();"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <form id="delete-employee-{{ $emp->id }}" action="{{ route('employees.destroy', $emp->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="fas fa-search fa-2x mb-3 text-light"></i><br>
                                No se encontraron empleados para la búsqueda actual.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Paginación Automática y Bootstrap --}}
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Mostrando {{ $employees->firstItem() ?? 0 }} a {{ $employees->lastItem() ?? 0 }} de {{ $employees->total() }} registros.
                </div>
                <div>
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
