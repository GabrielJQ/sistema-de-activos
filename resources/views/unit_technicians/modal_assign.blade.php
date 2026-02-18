<div class="modal fade" id="assignTechnicianModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form method="POST" action="{{ route('unit-technicians.store') }}" class="modal-content">
            @csrf

            <div class="modal-header bg-guinda text-white">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-tools me-2"></i> Asignar Técnico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body modal-body-soft">

                <input type="hidden" name="region_id" id="modal_region_id">
                <input type="hidden" name="unit_id" id="modal_unit_id">

                {{-- Unidad --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Unidad</label>
                    <input type="text"
                           class="form-control modern-select bg-light"
                           id="modal_unit_name"
                           readonly>
                </div>

                {{-- Técnico --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Empleado Técnico</label>
                    <select name="employee_id"
                            id="employeeSelect"
                            class="form-select modern-select"
                            required>
                        <option value="">Seleccione un empleado</option>
                    </select>
                </div>

                <div class="alert alert-warning small d-flex align-items-start gap-2">
                    <i class="fas fa-info-circle mt-1"></i>
                    <div>
                        Solo puede existir <strong>un técnico por unidad</strong>.
                        El técnico anterior será reemplazado.
                    </div>
                </div>

            </div>

            <div class="modal-footer justify-content-start">
                <button type="submit" class="btn btn-guinda-outline px-3">
                    <i class="fas fa-save me-1"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
