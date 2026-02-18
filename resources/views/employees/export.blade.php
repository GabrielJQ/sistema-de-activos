@extends('layouts.admin')

@section('title', 'Exportar Empleados')

@section('content')
<div class="container-fluid py-4 d-flex justify-content-center">

    <div class="card border-0 shadow-soft rounded-4 w-100" style="max-width: 1000px;">

        {{-- Header --}}
        <div class="card-header bg-guinda text-white rounded-top-4 py-3 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-circle bg-white text-guinda">
                    <i class="fas fa-file-export"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Exportar Empleados</h5>
                    <small class="opacity-75">
                        Configura filtros, columnas y formato de exportación
                    </small>
                </div>
            </div>
        </div>

        <div class="card-body p-4 bg-light-soft">

            {{-- Mensajes --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-soft rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-soft rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('employees.export') }}" method="POST" id="exportForm" target="_blank">
                @csrf

                {{-- Filtro --}}
                <div class="section-block mb-4">
                    <h6 class="section-title">
                        <i class="fas fa-filter"></i> Filtro (Opcional)
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fas fa-columns"></i></span>
                                <select name="filter_column" id="filter_column" class="form-select">
                                    <option value="">-- Ninguna --</option>
                                    @foreach($columns as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6" id="filter_value_container">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fas fa-keyboard"></i></span>
                                <input type="text" name="filter_value" id="filter_value" class="form-control" placeholder="Escribe el valor...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Columnas --}}
                <div class="section-block mb-4">
                    <h6 class="section-title">
                        <i class="fas fa-columns"></i> Columnas a Exportar
                    </h6>

                    <div class="form-check mb-3 fs-6 fw-semibold">
                        <input type="checkbox" id="selectAll" class="form-check-input me-2" style="transform:scale(1.3)">
                        <label for="selectAll" class="form-check-label cursor-pointer">
                            Seleccionar / Deseleccionar todas
                        </label>
                    </div>

                    <div class="row g-2">
                        @foreach($columns as $key => $label)
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-check checkbox-card h-100">
                                    <input class="form-check-input me-2" type="checkbox" name="columns[]" value="{{ $key }}">
                                    <span>{{ $label }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Formato --}}
                <div class="section-block mb-4" style="max-width:320px;">
                    <h6 class="section-title">
                        <i class="fas fa-file-export"></i> Formato
                    </h6>

                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="fas fa-file"></i></span>
                        <select name="format" class="form-select" required>
                            <option value="csv">CSV</option>
                            <option value="xlsx">XLSX</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </div>

                {{-- Columnas extras --}}
                <div class="section-block mb-4">
                    <h6 class="section-title">
                        <i class="fas fa-plus"></i> Columnas Extras (Opcional)
                    </h6>

                    <div id="extraColumnsContainer" class="mb-3"></div>

                    <button type="button" id="addExtraColumn" class="btn btn-outline-guinda">
                        <i class="fas fa-plus"></i> Agregar Columna Extra
                    </button>
                </div>

                {{-- Acciones --}}
                <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary px-4">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-guinda px-4">
                        <i class="fas fa-file-export me-1"></i> Exportar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@stop
@section('css')
<style>
/* Paleta */
.bg-guinda { background-color:#611232!important; }
.text-guinda { color:#611232!important; }

/* Fondo suave */
.bg-light-soft { background:#fafafa; }

/* Sombra */
.shadow-soft { box-shadow:0 6px 16px rgba(0,0,0,.08); }

/* Icono header */
.icon-circle {
    width:42px;
    height:42px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    font-size:1.1rem;
}

/* Secciones */
.section-title {
    font-weight:600;
    color:#611232;
    font-size:1.1rem;
    margin-bottom:1rem;
    display:flex;
    align-items:center;
    gap:.5rem;
    border-left:4px solid #611232;
    padding-left:.6rem;
}

/* Inputs */
.form-control,.form-select {
    border-radius:.6rem;
    border:1.8px solid #ccc;
}
.form-control:focus,.form-select:focus {
    border-color:#611232;
    box-shadow:0 0 0 .2rem rgba(97,18,50,.2);
}
.input-group-text {
    background:#f3f3f3;
    border:1.8px solid #ccc;
}

/* Checkboxes tipo card */
.checkbox-card {
    display:flex;
    align-items:center;
    gap:.4rem;
    border:1px solid #ddd;
    border-radius:.5rem;
    padding:.5rem .6rem;
    cursor:pointer;
}
.checkbox-card:hover { background:#f7f4f6; }

/* Botones */
.btn-guinda {
    background:#611232;
    color:#fff;
    border:1px solid #611232;
    font-weight:600;
}
.btn-guinda:hover {
    background:#4b0f27;
    border-color:#4b0f27;
}
.btn-outline-guinda {
    border:1px dashed #611232;
    color:#611232;
}
.btn-outline-guinda:hover {
    background:#611232;
    color:#fff;
}
</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let extraColumnIndex = 0;
    const container = document.getElementById('extraColumnsContainer');
    const addBtn = document.getElementById('addExtraColumn');
    const exportForm = document.getElementById('exportForm');

    // Agregar columnas extras
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
        row.querySelector('.remove-column').addEventListener('click', () => container.removeChild(row));
    });

    // Validación de columnas extras
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

    // Select/Deselect all
    document.getElementById('selectAll').addEventListener('change', function(){
        document.querySelectorAll('input[name="columns[]"]').forEach(cb => cb.checked = this.checked);
    });

    // Filtro dinámico
    const filterColumn = document.getElementById('filter_column');
    const filterContainer = document.getElementById('filter_value_container');

    const departments = @json($departments);
    const tipos = @json($tipos);
    const employees = @json($employees);

    filterColumn.addEventListener('change', function() {
        let html = '';
        if (this.value === 'department_id') {
            html += '<select name="filter_value" id="filter_value" class="form-select">';
            html += '<option value="">-- Selecciona Departamento --</option>';
            for (const [id, name] of Object.entries(departments)) html += `<option value="${id}">${name}</option>`;
            html += '</select>';
        } else if (this.value === 'employee_id') {
            html += '<select name="filter_value" id="filter_value" class="form-select">';
            html += '<option value="">-- Selecciona Empleado --</option>';
            for (const [id, name] of Object.entries(employees)) html += `<option value="${id}">${name}</option>`;
            html += '</select>';
        } else if (this.value === 'tipo') {
            html += '<select name="filter_value" id="filter_value" class="form-select">';
            html += '<option value="">-- Selecciona Tipo --</option>';
            tipos.forEach(tipo => html += `<option value="${tipo}">${tipo}</option>`);
            html += '</select>';
        } else {
            html = `<input type="text" name="filter_value" id="filter_value" class="form-control" placeholder="Escribe el valor...">`;
        }

        filterContainer.innerHTML = `<div class="input-group input-group-lg">
            <span class="input-group-text bg-light text-muted"><i class="fas fa-keyboard"></i></span>
            ${html}
        </div>`;
    });
});
</script>
@stop
