document.addEventListener('click', function (e) {

    const btn = e.target.closest('[data-confirm-delete]');
    if (!btn) return;

    const modalEl = document.getElementById('globalConfirmDelete');
    if (!modalEl) {
        console.warn('Modal globalConfirmDelete no encontrado');
        return;
    }

    const name   = btn.dataset.name || '';
    const action = btn.dataset.action;
    const text   = btn.dataset.text || '¿Deseas eliminar este registro?';

    const nameEl = modalEl.querySelector('#confirmDeleteName');
    const textEl = modalEl.querySelector('#confirmDeleteText');
    const formEl = modalEl.querySelector('#globalDeleteForm');

    if (nameEl) nameEl.textContent = name;
    if (textEl) textEl.textContent = text;
    if (formEl && action) formEl.setAttribute('action', action);

    // AdminLTE / Bootstrap 4
    $(modalEl).modal('show');
});
