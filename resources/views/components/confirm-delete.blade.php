<div class="modal fade" id="globalConfirmDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">

            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center py-4">
                <p class="fs-5 mb-1" id="confirmDeleteText">
                    ¿Deseas eliminar este registro?
                </p>
                <p class="fw-bold text-danger fs-5" id="confirmDeleteName"></p>
                <p class="text-muted mb-0">
                    Esta acción no se puede deshacer.
                </p>
            </div>

            <div class="modal-footer justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <form id="globalDeleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        Sí, eliminar
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
