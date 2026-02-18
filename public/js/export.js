    document.addEventListener('DOMContentLoaded', function() {
    let extraColumnIndex = 0;
    const container = document.getElementById('extraColumnsContainer');
    const addBtn = document.getElementById('addExtraColumn');
    const exportForm = document.getElementById('exportForm');

    // Agregar nueva columna extra
    addBtn.addEventListener('click', function() {
        const row = document.createElement('div');
        row.classList.add('d-flex', 'gap-2', 'mb-2', 'extra-column-row');
        row.innerHTML = `
            <input type="text" name="extra_columns[${extraColumnIndex}][name]" class="form-control" placeholder="Nombre de la columna" required>
            <input type="text" name="extra_columns[${extraColumnIndex}][value]" class="form-control" placeholder="Valor por defecto (opcional)">
            <button type="button" class="btn btn-danger btn-sm remove-column"><i class="fas fa-trash-alt"></i></button>
        `;
        container.appendChild(row);
        extraColumnIndex++;

        // Botón eliminar
        row.querySelector('.remove-column').addEventListener('click', function() {
            container.removeChild(row);
        });
    });

    // Validación antes de enviar
    exportForm.addEventListener('submit', function(e) {
        const extraColumns = container.querySelectorAll('input[name$="[name]"]');
        for (let col of extraColumns) {
            if (col.value.trim() === '') {
                alert('El nombre de la columna extra no puede estar vacío.');
                col.focus();
                e.preventDefault();
                return false;
            }
        }
    });

    // Seleccionar/Deseleccionar todos
    const selectAll = document.getElementById('selectAll');
    selectAll.addEventListener('change', function(){
        document.querySelectorAll('input[name="columns[]"]').forEach(cb => cb.checked = selectAll.checked);
    });
});