<div>
    {{-- Caja de Búsqueda Livewire --}}
    <div class="row mb-4">
        <div class="col-md-6 col-sm-12">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white text-muted border-end-0">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="form-control border-start-0 ps-0 form-control-lg bg-white"
                       placeholder="Buscar por empleado, apellidos o departamento..."
                       style="font-size: 0.95rem;">
            </div>

            <div wire:loading wire:target="search" class="text-muted small mt-1 ms-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Buscando...
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card border-0 shadow-soft rounded-4" wire:loading.class="opacity-50" wire:target="search, previousPage, nextPage, gotoPage">
        <div class="card-body table-responsive p-3">
            <table class="table table-hover align-middle modern-table">
                <thead class="table-dark text-white">
                    <tr>
                        <th>Empleado</th>
                        <th>Departamento</th>
                        <th class="text-center">Activos</th>
                        <th>Tipo más común</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td class="fw-semibold">{{ $employee->full_name }}</td>
                        <td class="text-muted">
                            <i class="fas fa-building me-1"></i>{{ $employee->department_name }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-soft bg-guinda">
                                {{ $employee->currentAssets->count() }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            @php
                                $typesCount = $employee->currentAssets
                                    ->groupBy(fn($a) => $a->deviceType->equipo ?? 'N/A')
                                    ->map(fn($g) => $g->count())
                                    ->sortDesc();
                            @endphp

                            @forelse($typesCount as $type => $count)
                                <strong>{{ $type }}</strong>
                                <span class="text-secondary">({{ $count }})</span>@if(!$loop->last), @endif
                            @empty
                                —
                            @endforelse
                        </td>
                        <td class="text-center">
                            <a href="{{ route('asset_assignments.show', $employee->id) }}"
                               class="btn btn-sm modern-btn px-3">
                                <i class="fas fa-eye me-1"></i> Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-search fa-2x mb-3 text-light"></i><br>
                            No se encontraron empleados con asignaciones para tu búsqueda.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{-- Paginación Automática y Bootstrap --}}
            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
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
