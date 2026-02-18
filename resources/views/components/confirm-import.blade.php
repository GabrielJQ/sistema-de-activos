<div class="modal fade" id="globalConfirmImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">

            <div class="modal-header bg-guinda text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-import me-2"></i>
                    Confirmar importación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center py-4">
                <p class="fs-5 mb-2" id="confirmImportText">
                    ¿Deseas continuar con la importación?
                </p>

                <p class="text-muted mb-0">
                    Solo se importarán los registros válidos.
                </p>
            </div>

            <div class="modal-footer justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="button" id="confirmImportAction" class="btn btn-guinda px-4">
                    <i class="fas fa-check me-1"></i> Sí, importar
                </button>
            </div>

        </div>
    </div>
</div>
