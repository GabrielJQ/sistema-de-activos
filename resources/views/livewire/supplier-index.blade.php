<div>
    {{-- Caja de Búsqueda Livewire --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white text-muted border-end-0">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       class="form-control border-start-0 ps-0 form-control-lg bg-white" 
                       placeholder="Buscar proveedor por nombre, contrato, teléfono o enlace..."
                       style="font-size: 0.95rem;">
            </div>
            
            <div wire:loading wire:target="search" class="text-muted small mt-1 ms-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Buscando...
            </div>
        </div>
    </div>

    {{-- Grid de proveedores --}}
    <div class="row g-4" wire:loading.class="opacity-50">
        @forelse($suppliers as $supplier)
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card supplier-card h-100 border-0 rounded-4 shadow-soft">

                    <div class="card-body d-flex flex-column">

                        {{-- Logo + Estado --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="supplier-logo-wrapper">
                                <img
                                    src="{{ $supplier->logo_path ? asset($supplier->logo_path) : asset('images/logos/default-logo.png') }}"
                                    alt="{{ $supplier->prvnombre }}"
                                    class="supplier-logo">
                            </div>

                            <span class="badge status-badge {{ $supplier->prvstatus ? 'bg-success-soft' : 'bg-secondary-soft' }}">
                                {{ $supplier->prvstatus ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        {{-- Información --}}
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-2 text-truncate" title="{{ $supplier->prvnombre }}">
                                {{ $supplier->prvnombre }}
                            </h5>

                            <p class="text-muted mb-1 small">
                                <i class="fas fa-file-contract me-1"></i>
                                <strong>Contrato:</strong> {{ $supplier->contrato ?? '-' }}
                            </p>

                            <p class="text-muted mb-1 small">
                                <i class="fas fa-phone me-1"></i>
                                <strong>Teléfono:</strong> {{ $supplier->telefono ?? '-' }}
                            </p>

                            <p class="text-muted mb-0 small text-truncate" title="{{ $supplier->enlace }}">
                                <i class="fas fa-link me-1"></i>
                                <strong>Enlace:</strong> 
                                @if($supplier->enlace)
                                    <a href="{{ Str::startsWith($supplier->enlace, 'http') ? $supplier->enlace : 'http://'.$supplier->enlace }}" target="_blank" class="text-decoration-none">{{ $supplier->enlace }}</a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>

                        {{-- Acciones --}}
                        @if(hasRole(['super_admin','admin']))
                        <div class="d-flex justify-content-end gap-2 mt-4 flex-wrap">
                            <a href="{{ route('suppliers.edit', $supplier) }}"
                               class="btn btn-guinda btn-sm px-3">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>

                            <button type="button"
                                class="btn btn-outline-danger btn-sm px-3"
                                onclick="if(confirm('¿Deseas eliminar este proveedor?')) document.getElementById('delete-supplier-{{ $supplier->id }}').submit();"
                            >
                                <i class="fas fa-trash me-1"></i> Eliminar
                            </button>
                            <form id="delete-supplier-{{ $supplier->id }}" action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            {{-- Estado vacío --}}
            <div class="col-12">
                <div class="empty-state text-center py-5 rounded-4 shadow-soft bg-white">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="fs-5 text-muted mb-2">No se encontraron proveedores que coincidan con la búsqueda.</p>
                    @if(hasRole(['super_admin','admin']))
                        <a href="{{ route('suppliers.create') }}" class="btn btn-guinda mt-2">
                            <i class="fas fa-plus me-1"></i> Crear proveedor
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    {{-- Paginación Automática y Bootstrap --}}
    <div class="mt-4 bg-white p-3 rounded-4 shadow-sm table-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="text-muted small">
                Mostrando {{ $suppliers->firstItem() ?? 0 }} a {{ $suppliers->lastItem() ?? 0 }} de {{ $suppliers->total() }} registros.
            </div>
            <div class="pagination-container">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</div>
