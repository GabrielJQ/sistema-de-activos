document.addEventListener('click', function (e) {

    const btn = e.target.closest('[data-confirm-import]');
    if (!btn) return;

    const modalEl = document.getElementById('globalConfirmImport');
    if (!modalEl) {
        console.warn('Modal globalConfirmImport no encontrado');
        return;
    }

    const text = btn.dataset.text || '¿Deseas continuar con la importación?';
    const onConfirm = btn.dataset.onConfirm;

    modalEl.querySelector('#confirmImportText').textContent = text;

    const confirmBtn = modalEl.querySelector('#confirmImportAction');

    confirmBtn.onclick = function () {
        if (onConfirm && typeof window[onConfirm] === 'function') {
            window[onConfirm]();
        }
        $(modalEl).modal('hide');
    };

    $(modalEl).modal('show');
});
