<div>
    {{-- ================= FILTROS ================= --}}
    <div class="card shadow-soft rounded-4 border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center">

                <div class="flex-grow-1" style="max-width: 340px; position: relative;">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           class="form-control"
                           placeholder="Buscar por nombre, unidad o dirección…">
                    <div wire:loading wire:target="search" class="text-muted small mt-1 position-absolute" style="bottom: -20px; left: 5px;">
                        <i class="fas fa-spinner fa-spin me-1"></i> Buscando...
                    </div>
                </div>

                <div style="max-width: 200px; position: relative;">
                    <select wire:model.live="tipo" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="Oficina">🏢 Oficina</option>
                        <option value="Almacen">🏬 Almacén</option>
                        <option value="Otro">📦 Otro</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= LISTADO ================= --}}
    <div class="row g-3" wire:loading.class="opacity-50">
        @forelse($departments as $dept)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dept-card h-100 border-0 shadow-soft rounded-4">
                    <div class="card-body d-flex flex-column justify-content-between">

                        <div>
                            <h5 class="fw-bold mb-1" title="{{ $dept->areanom }}">{{ $dept->areanom }}</h5>

                            <span class="badge badge-soft mb-2">
                                {{ $dept->tipo }}
                            </span>

                            <p class="text-muted mb-1" title="{{ $dept->unit->uninom ?? '-' }}">
                                <i class="fas fa-sitemap me-1"></i>
                                {{ Str::limit($dept->unit->uninom ?? '-', 40) }}
                            </p>

                            <p class="text-muted mb-0 small">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $dept->address->calle ?? '-' }},
                                {{ $dept->address->colonia ?? '-' }},
                                CP {{ $dept->address->cp ?? '-' }}
                            </p>
                        </div>

                        <div class="mt-3 d-flex justify-content-end gap-2 flex-wrap">
                            <a href="{{ route('departments.edit', $dept->id) }}"
                               class="btn btn-guinda btn-sm px-3">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>

                            <button type="button"
                                    class="btn btn-outline-danger btn-sm px-3"
                                    onclick="if(confirm('¿Deseas eliminar este departamento?')) document.getElementById('delete-dept-{{ $dept->id }}').submit();"
                            >
                                <i class="fas fa-trash me-1"></i> Eliminar
                            </button>
                            <form id="delete-dept-{{ $dept->id }}" action="{{ route('departments.destroy', $dept->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm border-0">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="fs-5 text-muted mb-0">No se encontraron departamentos para tu búsqueda.</p>
            </div>
        @endforelse
    </div>

    {{-- ================= PAGINACIÓN ================= --}}
    <div class="mt-4 bg-white p-3 rounded-4 shadow-sm table-card border-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="text-muted small">
                Mostrando {{ $departments->firstItem() ?? 0 }} a {{ $departments->lastItem() ?? 0 }} de {{ $departments->total() }} registros.
            </div>
            <div class="pagination-container">
                {{ $departments->links() }}
            </div>
        </div>
    </div>

</div>
