<div>
    {{-- Caja de Búsqueda Livewire --}}
    @if(hasRole('super_admin'))
    <div class="row mb-4">
        <div class="col-md-6 col-sm-12">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white text-muted border-end-0">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       class="form-control border-start-0 ps-0 form-control-lg bg-white" 
                       placeholder="Buscar por unidad o región..."
                       style="font-size: 0.95rem;">
            </div>
            
            <div wire:loading wire:target="search" class="text-muted small mt-1 ms-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Buscando...
            </div>
        </div>
    </div>
    @endif

    {{-- GRID DE CARDS --}}
    <div class="row g-4" wire:loading.class="opacity-50" wire:target="search, previousPage, nextPage, gotoPage">
        @forelse($units as $unit)
            @php
                $technician = $unit->technician?->employee;
            @endphp

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card technician-card shadow-sm border-0 h-100">

                    {{-- HEADER --}}
                    <div class="card-header bg-guinda text-white rounded-top">
                        <div class="fw-semibold" title="{{ $unit->uninom }}">{{ Str::limit($unit->uninom, 40) }}</div>
                        <small class="opacity-75" title="{{ $unit->region->regnom }}">{{ Str::limit($unit->region->regnom, 40) }}</small>
                    </div>

                    {{-- BODY --}}
                    <div class="card-body">
                        {{-- Técnico --}}
                        <div class="mb-2">
                            <span class="fw-semibold">Técnico:</span><br>

                            @if($technician)
                                <i class="fas fa-user-check text-success me-1"></i>
                                <span title="{{ $technician->full_name }}">{{ Str::limit($technician->full_name, 35) }}</span>
                            @else
                                <span class="text-danger fw-semibold">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Sin técnico asignado
                                </span>
                            @endif
                        </div>

                        {{-- Estado --}}
                        <div>
                            @if($technician)
                                <span class="badge bg-success">Asignado</span>
                            @else
                                <span class="badge bg-danger">Pendiente</span>
                            @endif
                        </div>
                    </div>

                    {{-- FOOTER --}}
                    <div class="card-footer bg-light border-0 text-end">
                        <button class="btn btn-sm btn-guinda-outline modern-btn" 
                            wire:click="openAssignModal({{ $unit->region->id }}, {{ $unit->id }}, '{{ addslashes($unit->uninom) }}', '{{ $technician->id ?? '' }}')">
                            <i class="fas fa-edit me-1"></i>
                            {{ $technician ? 'Cambiar' : 'Asignar' }}
                        </button>
                    </div>

                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm border-0">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="fs-5 text-muted mb-0">No se encontraron unidades.</p>
            </div>
        @endforelse
    </div>

    {{-- Paginación Automática y Bootstrap --}}
    @if($units->hasPages())
    <div class="mt-4 bg-white p-3 rounded-4 shadow-sm h-100 table-card border-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="text-muted small">
                Mostrando {{ $units->firstItem() ?? 0 }} a {{ $units->lastItem() ?? 0 }} de {{ $units->total() }} registros.
            </div>
            <div class="pagination-container">
                {{ $units->links() }}
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL LIVEWIRE --}}
    <div class="modal fade" id="livewireAssignModal" tabindex="-1" data-bs-backdrop="static" wire:ignore.self>
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form wire:submit.prevent="saveTechnician" class="modal-content">
                
                <div class="modal-header bg-guinda text-white">
                    <h5 class="modal-title fw-semibold">
                        <i class="fas fa-tools me-2"></i> Asignar Técnico
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body modal-body-soft">

                    {{-- Loader de carga mientra Livewire procesa openAssignModal --}}
                    <div wire:loading wire:target="openAssignModal" class="w-100 text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-guinda"></i>
                        <p class="mt-2 text-muted">Cargando datos de la unidad...</p>
                    </div>

                    {{-- Formulario --}}
                    <div wire:loading.remove wire:target="openAssignModal">
                        {{-- Unidad (Info) --}}
                        @if(hasRole(['super_admin']))
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Unidad</label>
                            <input type="text"
                                class="form-control modern-select bg-light"
                                wire:model="modal_unit_name"
                                readonly>
                        </div>
                        @endif

                        {{-- Técnico --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Empleado Técnico</label>
                            <select wire:model="employee_id"
                                    class="form-select modern-select @error('employee_id') border-danger @enderror"
                                    required>
                                <option value="">Seleccione un empleado...</option>
                                @foreach($unitEmployees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->nombre }} {{ $emp->paterno }} {{ $emp->materno }}</option>
                                @endforeach
                            </select>
                            @error('employee_id') <span class="text-danger small"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</span> @enderror
                        </div>

                        <div class="alert alert-warning small d-flex align-items-start gap-2 mb-0">
                            <i class="fas fa-info-circle mt-1"></i>
                            <div>
                                Solo puede existir <strong>un técnico por unidad</strong>.
                                El técnico anterior será reemplazado.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer justify-content-start">
                    <button type="submit" class="btn btn-guinda-outline px-3" wire:loading.attr="disabled" wire:target="saveTechnician">
                        <span wire:loading.remove wire:target="saveTechnician"><i class="fas fa-save me-1"></i> Guardar</span>
                        <span wire:loading wire:target="saveTechnician"><i class="fas fa-spinner fa-spin me-1"></i> Guardando...</span>
                    </button>
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS PARA CONTROLAR EL MODAL --}}
    @script
    <script>
        $wire.on('show-technician-modal', () => {
            let modalEl = document.getElementById('livewireAssignModal');
            let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        });

        $wire.on('hide-technician-modal', () => {
            let modalEl = document.getElementById('livewireAssignModal');
            let modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
        });
    </script>
    @endscript

</div>
