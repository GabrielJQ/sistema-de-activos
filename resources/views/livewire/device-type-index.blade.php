<div>
    {{-- Filtro de Búsqueda --}}
    <div class="mb-4">
        <div class="input-group modern-search shadow-sm" style="max-width:380px;">
            <span class="input-group-text bg-white border-end-0">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   class="form-control border-start-0 shadow-none bg-white"
                   placeholder="Buscar por nombre o descripción...">
        </div>
        
        <div wire:loading wire:target="search" class="text-muted small mt-1 ms-2">
            <i class="fas fa-spinner fa-spin me-1"></i> Buscando...
        </div>
    </div>

    {{-- Cards --}}
    <div class="row g-4" wire:loading.class="opacity-50">
        @forelse($deviceTypes as $type)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card modern-card h-100 border-0 shadow-soft">

                    <div class="card-body d-flex flex-column justify-content-between">

                        {{-- Info --}}
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $type->image_path ? asset($type->image_path) : asset('images/dispositivos/default.png') }}"
                                 alt="{{ $type->equipo }}"
                                 class="device-img bg-light p-2 rounded-3" style="width: 90px; height: 90px; object-fit: contain;">

                            <div class="overflow-hidden">
                                <h5 class="fw-bold mb-1 text-truncate" title="{{ $type->equipo }}">{{ $type->equipo }}</h5>
                                <p class="text-muted small mb-0 text-truncate" title="{{ $type->descripcion }}">
                                    {{ $type->descripcion ?? 'Sin descripción' }}
                                </p>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="d-flex justify-content-end gap-2 mt-4 flex-wrap">
                            <a href="{{ route('device_types.edit', $type->id) }}"
                               class="btn btn-guinda btn-sm px-3 py-2 shadow-sm d-flex align-items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </a>

                            <button type="button"
                                    class="btn btn-outline-danger btn-sm px-3 py-2 shadow-sm d-flex align-items-center gap-1"
                                    onclick="if(confirm('¿Deseas eliminar el tipo de dispositivo?')) document.getElementById('delete-device-type-{{ $type->id }}').submit();"
                            >
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            <form id="delete-device-type-{{ $type->id }}" action="{{ route('device_types.destroy', $type->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="fs-5 text-muted">No se encontraron tipos de dispositivos para tu búsqueda.</p>
                <a href="{{ route('device_types.create') }}"
                   class="btn btn-guinda px-4 py-2 shadow-sm mt-2">
                    <i class="fas fa-plus me-1"></i> Crear uno nuevo
                </a>
            </div>
        @endforelse

    </div>

    {{-- Paginación Automática y Bootstrap --}}
    <div class="mt-4 bg-white p-3 rounded-4 shadow-sm h-100 table-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="text-muted small">
                Mostrando {{ $deviceTypes->firstItem() ?? 0 }} a {{ $deviceTypes->lastItem() ?? 0 }} de {{ $deviceTypes->total() }} registros.
            </div>
            <div class="pagination-container">
                {{ $deviceTypes->links() }}
            </div>
        </div>
    </div>
</div>
