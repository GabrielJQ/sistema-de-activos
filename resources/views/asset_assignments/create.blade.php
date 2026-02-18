@extends('layouts.admin')

@section('title', 'Nueva Asignación de Activos')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="text-guinda fw-bold mb-0 d-flex align-items-center gap-2">
            <span class="icon-circle bg-guinda text-white">
                <i class="fas fa-tasks"></i>
            </span>
            Nueva Asignación de Activos
        </h1>
        <small class="text-muted">Asignación de activos a empleados o usuarios temporales</small>
    </div>

    <a href="{{ route('asset_assignments.index') }}" class="btn btn-secondary shadow-sm px-3">
        <i class="fas fa-arrow-left me-1"></i> Volver al menu de Asignaciones
    </a>
</div>
@stop

@section('content')
<div class="container-fluid py-3">

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-soft" role="alert" id="alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-soft" role="alert" id="alert-error">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('asset_assignments.store') }}" method="POST" id="assignmentForm">
        @csrf

        {{-- =================== FILTROS =================== --}}
        <div class="card border-0 shadow-soft rounded-4 mb-4">
            <div class="card-body">
                <h6 class="section-title">
                    <i class="fas fa-filter"></i> Datos de la asignación
                </h6>

                <div class="row g-3 align-items-end">

                    <div class="col-12 col-md-4">
                        <label for="department_id" class="form-label fw-semibold">
                            <i class="fas fa-building me-1"></i> Departamento
                        </label>
                        <select id="department_id" class="form-select">
                            <option value="">-- Todos --</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->areanom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="employee_id" class="form-label fw-semibold">
                            <i class="fas fa-user me-1"></i> Empleado
                        </label>
                        <select id="employee_id" name="employee_id" class="form-select" required>
                            <option value="">-- Seleccione --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" data-department="{{ $employee->department_id }}">
                                    {{ $employee->full_name }} ({{ $employee->department->areanom ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="assignment_type" class="form-label fw-semibold">
                            <i class="fas fa-user-clock me-1"></i> Tipo de asignación
                        </label>
                        <select name="assignment_type" id="assignment_type" class="form-select">
                            <option value="normal">Normal</option>
                            <option value="temporal">Temporal</option>
                        </select>
                    </div>

                </div>

                <div class="row mt-3">
                    <div class="col-12 col-md-4" id="temporary_holder_container">
                        <label for="temporary_holder" class="form-label fw-semibold">
                            <i class="fas fa-user-edit me-1"></i> Ocupante temporal
                        </label>
                        <input type="text" name="temporary_holder" id="temporary_holder" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        {{-- =================== ACTIVOS =================== --}}
        <div class="row g-4 mb-4">

            {{-- DISPONIBLES --}}
            <div class="col-lg-7">
                <div class="card h-100 border-0 shadow-soft rounded-4">
                    <div class="card-header bg-guinda text-white rounded-top-4">
                        <i class="fas fa-boxes me-1"></i> Activos Disponibles
                    </div>
                    <div class="card-body p-0">
                        <table id="availableAssetsTable"
                               class="table table-striped table-bordered mb-0 display nowrap w-100">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tag</th>
                                    <th>Serie</th>
                                    <th>Tipo</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                $mainDeviceTypes = [
                                    'Equipo Escritorio',
                                    'Escritorio Avanzada',
                                    'Laptop Avanzada',
                                    'Laptop Intermedia',
                                    'Equipo All In One'
                                ];
                            @endphp

                            @foreach($assets as $asset)
                                @if($asset->estado !== 'BAJA')
                                    @php
                                        $tipo = $asset->deviceType->equipo ?? 'Sin tipo';
                                        $isMain = in_array($tipo, $mainDeviceTypes);
                                    @endphp

                                    <tr data-id="{{ $asset->id }}"
                                        data-tag="{{ $asset->tag }}"
                                        data-main="{{ $isMain ? 1 : 0 }}">
                                        <td>{{ $asset->tag }}</td>
                                        <td>{{ $asset->serie }}</td>

                                        {{-- 👇 data-order para ordenar principal primero (0) y periféricos después (1) --}}
                                        <td data-order="{{ $isMain ? 0 : 1 }}">{{ $tipo }}</td>

                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-actions-new shadow-sm add-btn">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            {{-- SELECCIONADOS --}}
            <div class="col-lg-5">
                <div class="card h-100 border-0 shadow-soft rounded-4 d-flex flex-column">
                    <div class="card-header bg-guinda text-white rounded-top-4">
                        <i class="fas fa-check-circle me-1"></i> Activos Seleccionados
                    </div>

                    <div class="card-body flex-grow-1 selected-scroll">
                        <div id="selectedAssets" class="d-flex flex-column gap-2"></div>
                    </div>


                    <div class="card-footer bg-white border-0 d-flex justify-content-end">
                        <button type="submit" class="btn btn-actions-new px-4">
                            <i class="fas fa-save me-1"></i> Asignar Activos
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- =================== FECHA Y OBSERVACIONES =================== --}}
        <div class="card border-0 shadow-soft rounded-4">
            <div class="card-body">
                <h6 class="section-title">
                    <i class="fas fa-clipboard-list"></i> Información adicional
                </h6>

                <div class="mb-3">
                    <label for="assigned_at" class="form-label fw-semibold">
                        <i class="fas fa-calendar-alt me-1"></i> Fecha de asignación
                    </label>
                    <input type="date" name="assigned_at" class="form-control"
                           value="{{ now()->format('Y-m-d') }}" required>
                </div>

                <div>
                    <label for="observations" class="form-label fw-semibold">
                        <i class="fas fa-comment-alt me-1"></i> Observaciones
                    </label>
                    <textarea name="observations" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>

    </form>
</div>
@stop

@section('css')
<style>
:root {
    --guinda: #611232;
    --guinda-dark: #4b0f27;
}

.text-guinda { color: var(--guinda) !important; }
.bg-guinda { background-color: var(--guinda) !important; }

.shadow-soft {
    box-shadow: 0 6px 16px rgba(0,0,0,.08);
}

/* Header icon */
.icon-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Secciones */
.section-title {
    font-weight: 600;
    color: var(--guinda);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
    border-left: 4px solid var(--guinda);
    padding-left: .6rem;
}

/* Botones */
.btn-actions-new {
    background-color: var(--guinda);
    border-color: var(--guinda);
    color: #fff;
    border-radius: .55rem;
}
.btn-actions-new:hover {
    background-color: var(--guinda-dark);
    border-color: var(--guinda-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(97,18,50,.3);
    color: #fff;
}

/* Selected assets */
#selectedAssets .asset-card {
    border: 1px solid var(--guinda);
    border-radius: .5rem;
    padding: .4rem .6rem;
    background: #f8f5f7;
}

/* Inputs */
.form-select,
.form-control {
    border-radius: .55rem;
}
#temporary_holder_container {
    display: none;
}
/* Mantener tamaño y solo crecer el scroll */
.selected-scroll{
    max-height: 420px;   
    overflow-y: auto;
    overflow-x: hidden;
}

.selected-scroll{
    scrollbar-gutter: stable;
}

.card .card-body{
    min-height: 0;
}

</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
const removedRowsById = new Map();
$(document).ready(function() {

    const selectedAssets = document.getElementById('selectedAssets');
    const form = document.getElementById('assignmentForm');
    const assignmentTypeSelect = document.getElementById('assignment_type');
    const temporaryHolderInput = document.getElementById('temporary_holder');
    const tempHolderContainer = document.getElementById('temporary_holder_container');

    const table = $('#availableAssetsTable').DataTable({
        paging: true,
        pageLength: 10,
        lengthChange: false,
        info: false,
        scrollY: "350px",
        scrollCollapse: true,
        order: [[0, 'asc'], [2, 'asc']],
        language: {
            search: "Buscar:",
            emptyTable: "No hay datos disponibles",
            paginate: { next: "›", previous: "‹" }
        }
    });


    new Sortable(selectedAssets, { animation: 150 });

    function aplicarColoresPorTag() {
        const rows = table.rows({ search: 'applied' }).nodes();
        let lastTag = null;
        let toggle = false;

        $(rows).each(function () {
            const tag = $(this).find('td').eq(0).text().trim();
            if (tag !== lastTag) {
                toggle = !toggle;
                lastTag = tag;
            }
            $(this)
                .removeClass('tag-color-1 tag-color-2')
                .addClass(toggle ? 'tag-color-1' : 'tag-color-2');
        });
    }

    function mostrarSoloBotonPrincipal() {
        const rows = table.rows({ search: 'applied' }).nodes();

        // Agrupar filas por TAG
        const groups = new Map();
        $(rows).each(function () {
            const $tr = $(this);
            const tag = $tr.find('td').eq(0).text().trim();
            if (!groups.has(tag)) groups.set(tag, []);
            groups.get(tag).push($tr);
        });

        // Para cada TAG: elegir 1 fila "principal" (data-main=1). Si no existe, usar la primera.
        groups.forEach((arr) => {

            let $principal = null;

            // Buscar principal por data-main
            for (const $tr of arr) {
                if (String($tr.data('main')) === '1') {
                    $principal = $tr;
                    break;
                }
            }

            // Fallback: si no hay principal, usa la primera fila del TAG
            if (!$principal) $principal = arr[0];

            // Pintar botones: solo el principal lleva +
            arr.forEach(($tr) => {
                const actionCell = $tr.find('td').eq(3);

                if ($tr.is($principal)) {
                    actionCell.html(`
                        <button type="button" class="btn btn-sm add-btn btn-actions-new shadow-sm" aria-label="Agregar activo">
                            <i class="fas fa-plus"></i>
                        </button>
                    `);
                } else {
                    actionCell.html(`<span class="text-muted"></span>`);
                }
            });
        });
    }

    // 🔹 Control visual del ocupante temporal
    function toggleTemporaryHolder() {
        if (assignmentTypeSelect.value === 'temporal') {
            tempHolderContainer.style.display = 'block';
        } else {
            tempHolderContainer.style.display = 'none';
            temporaryHolderInput.value = '';
        }
    }

    // Ejecutar al cargar
    toggleTemporaryHolder();

    // Escuchar cambios
    assignmentTypeSelect.addEventListener('change', toggleTemporaryHolder);

    // ---------------------------------------------------------
    // NUEVA FUNCIÓN → Mostrar “–” solo en el principal de SELECTED
    // ---------------------------------------------------------
    function mostrarSoloPrincipalSeleccionados() {
        const cards = $('#selectedAssets .asset-card');
        let lastTag = null;

        cards.each(function () {
            const card = $(this);
            const tag = card.data('tag');
            const btnContainer = card.find('.remove-btn'); // Este es un <button>

            if (tag !== lastTag) {
                // Principal: mostrar botón
                lastTag = tag;
                btnContainer.html(`<i class="fas fa-minus"></i>`);
                btnContainer.show();
            } else {
                // Secundario: OCULTAR BOTÓN POR COMPLETO
                btnContainer.hide();  
            }
        });
    }

    // ---------------------------------------------------------
    // AGREGAR ACTIVO (TODOS LOS DEL MISMO TAG)
    // ---------------------------------------------------------
    $('#availableAssetsTable').on('click', '.add-btn', function() {

        const row = $(this).closest('tr');
        const tag = row.find('td').eq(0).text();

        table.rows().every(function() {
            const node = $(this.node());
            const rowTag = node.find('td').eq(0).text();

            if (rowTag === tag) {

                const id = node.data('id');
                const serie = node.find('td').eq(1).text();
                const tipo = node.find('td').eq(2).text();

                if (selectedAssets.querySelector(`[data-id="${id}"]`)) return;

                const card = $(`
                    <div class="asset-card" data-id="${id}" data-tag="${tag}">
                        <div class="info">${tag} - ${serie} - ${tipo}</div>
                        <button type="button" class="btn btn-sm btn-actions-new remove-btn"></button>
                    </div>
                `);

                $('#selectedAssets').append(card);

                node.addClass('d-none');
            }
        });

        mostrarSoloPrincipalSeleccionados();
    });

    // ---------------------------------------------------------
    // QUITAR ACTIVO (TODOS LOS DEL MISMO TAG)
    // ---------------------------------------------------------
    $('#selectedAssets').on('click', '.remove-btn', function() {
        const card = $(this).closest('.asset-card');
        const tag = card.data('tag');

        const cardsSameTag = $(`#selectedAssets .asset-card`).filter(function() {
            return $(this).data('tag') === tag;
        });

        cardsSameTag.each(function() {
            const c = $(this);
            const id = c.data('id');

            table.rows().every(function() {
                const node = $(this.node());
                if (node.data('id') == id) {
                    node.removeClass('d-none');
                }
            });

            c.remove();
        });

        mostrarSoloPrincipalSeleccionados();
    });

    table.on('draw', function() {
        aplicarColoresPorTag();
        mostrarSoloBotonPrincipal();
    });

    aplicarColoresPorTag();
    mostrarSoloBotonPrincipal();
    mostrarSoloPrincipalSeleccionados();

    form.addEventListener('submit', function(e){
        document.querySelectorAll('input[name="asset_ids[]"]').forEach(i => i.remove());

        const cards = selectedAssets.querySelectorAll('.asset-card');
        if(cards.length === 0){
            e.preventDefault();
            if(!document.getElementById('error-no-assets')){
                const errorDiv = document.createElement('div');
                errorDiv.id = 'error-no-assets';
                errorDiv.className = 'text-danger mb-2';
                errorDiv.innerText = 'Debe seleccionar al menos un activo.';
                form.prepend(errorDiv);
            }
            return;
        }

        cards.forEach(card => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'asset_ids[]';
            input.value = card.dataset.id;
            form.appendChild(input);
        });
    });

    $('#department_id').on('change', function() {
        const deptId = $(this).val();
        $('#employee_id option').each(function() {
            const empDept = $(this).data('department');
            $(this).toggle(!deptId || empDept == deptId);
        });
        $('#employee_id').val('');
    });

    assignmentTypeSelect.addEventListener('change', function() {
        tempHolderContainer.style.display = this.value === 'temporal' ? 'block' : 'none';
        if(this.value !== 'temporal'){ temporaryHolderInput.value = ''; }
    });

    setTimeout(() => $('#alert-success').fadeOut(500), 5000);
    setTimeout(() => $('#alert-error').fadeOut(500), 5000);

});


</script>

@stop
