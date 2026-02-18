@extends('layouts.admin')

@section('title', 'Asignaciones')

@section('content')
<div class="container-fluid">

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-soft border-0 rounded-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-soft border-0 rounded-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="view-title text-guinda fw-bold d-flex align-items-center gap-2 mb-1">
                <span class="icon-circle bg-guinda text-white">
                    <i class="fas fa-tasks"></i>
                </span>
                Asignaciones de Activos
            </h1>
            <small class="text-muted">
                Administración y consulta de activos asignados a empleados
            </small>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="card border-0 shadow-soft rounded-4 mb-4">
        <div class="card-body d-flex flex-wrap gap-2 align-items-center">

            @if(hasRole(['super_admin','admin','collaborator']))
            <a href="{{ route('asset_assignments.create') }}" class="btn btn-actions-new px-3">
                <i class="fas fa-plus me-1"></i> Nueva Asignación
            </a>
            @endif

            @if(hasRole(['super_admin','admin','collaborator']))
            <button type="button" 
                class="btn btn-guinda-outline px-3"
                data-bs-toggle="modal" 
                data-bs-target="#downloadModal">
                <i class="fas fa-download me-1"></i> Descarga Masiva
            </button>
            @endif

        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="downloadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('asset_assignments.bulkDownload') }}" method="POST">
                @csrf
                <div class="modal-content border-0 rounded-4 shadow-soft">

                    <div class="modal-header bg-guinda text-white rounded-top-4">
                        <h5 class="modal-title fw-semibold" id="downloadModalLabel">
                            Generación de Resguardos y Códigos QR
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body modal-body-soft p-4">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipo de generación</label>
                            <select id="generationType" class="form-select modern-select" required>
                                <option value="">-- Selecciona --</option>
                                <option value="resguardos">Resguardos</option>
                                <option value="qr">Códigos QR</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="filterType" class="form-label fw-semibold">Tipo de descarga</label>
                            <select name="filter_type" id="filterType" class="form-select modern-select" required>
                                <option value="all">Todos</option>
                                <option value="department">Por Departamento</option>
                                <option value="employee">Por Empleado</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="departmentSelect">
                            <label class="form-label fw-semibold">Departamento</label>
                            <select name="department_id" id="department_id" class="form-select modern-select">
                                <option value="">-- Selecciona un departamento --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->areanom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="employeeSelect">
                            <label class="form-label fw-semibold">Empleado</label>
                            <select name="employee_id" id="employee_id" class="form-select modern-select">
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-department="{{ $emp->department_id }}">
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer justify-content-between px-4 pb-4">
                        <button type="submit" class="btn btn-actions-new px-4">
                            <i class="fas fa-download me-1"></i> Descargar
                        </button>
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card border-0 shadow-soft rounded-4">
        <div class="card-body table-responsive p-3">
            <table class="table table-hover align-middle datatable modern-table" id="employeesTable">
                <thead class="table-dark">
                    <tr>
                        <th>Empleado</th>
                        <th>Departamento</th>
                        <th class="text-center">Activos</th>
                        <th>Tipo más común</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($employees as $employee)
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
                @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>
@stop

@section('css')
<style>
/* COLOR CORPORATIVO */
.text-guinda { color: #611232 !important; }
.bg-guinda { background-color: #611232 !important; }

/* ALERT SUAVE */
.shadow-soft { box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important; }

/* BOTONES (mantienen color en hover) */
.btn-actions-new, .btn-guinda-outline {
    border-radius: .55rem;
    font-weight: 500;
    background-color: #611232 !important;
    color : #fff !important;
    border: 1px solid #611232 !important;
    transition: .25s ease-in-out;
}

/* Hover: permanece guinda, solo se oscurece ligeramente */
.btn-actions-new:hover,
.btn-guinda-outline:hover {
    background-color: #4b0f27 !important;
    border-color: #4b0f27 !important;
    color: #fff !important;
}

/* TABLA */
.modern-table th, .modern-table td {
    padding: .85rem 1rem !important;
    font-size: .9rem;
}

/* Hover tabla: mantiene texto blanco para contraste */
.modern-table tbody tr:hover { 
    background-color: #611232 !important; 
    color: #fff !important;
}

/* BADGE */
.badge-soft {
    border-radius: .45rem;
    font-size: .8rem;
}

/* BOTÓN ACCIÓN*/
.modern-btn {
    border-radius: .45rem !important;
    transition: .25s ease;
    background-color: #611232 !important;
    border-color: #611232 !important;
    color: #fff !important;
}

.modern-btn:hover {
    background-color: #4b0f27 !important;
    border-color: #4b0f27 !important;
    color: #fff !important;
    transform: translateY(-1px);
}

/* MODAL */
.modal-body-soft { background: #faf7f9 !important; }
.modern-select { border-radius: .45rem !important; }

/* TARJETA */
.table-card { border-radius: 1rem !important; }
/* Modal más ancho y equilibrado */
#downloadModal .modal-dialog {
    max-width: 1500px; /* control fino del ancho */
}

/* RESPONSIVE */
@media (max-width: 576px) {
    .btn-actions-new, .btn-guinda-outline { width: 100%; }
}

</style>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    const table = $('#employeesTable');
    if (!$.fn.DataTable.isDataTable(table)) {
        const hasData = table.find('tbody tr').not(':has(td[colspan])').length > 0;
        if (hasData) {
            table.DataTable({
                responsive: true,
                autoWidth: false,
                destroy: true,
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "&rsaquo;",
                        previous: "&lsaquo;"
                    },
                    emptyTable: "No hay datos",
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100]
            });
        }
    }

    // Fade out alerts
    setTimeout(() => $('.alert-success, .alert-danger').fadeOut(500), 5000);
});

/* === Modal === */
const generationType = document.getElementById('generationType');
const form = document.querySelector('#downloadModal form');
const filterType = document.getElementById('filterType');
const labelFilter = document.querySelector('label[for="filterType"]');
const titleModal = document.getElementById('downloadModalLabel');
const departmentSelect = document.getElementById('departmentSelect');
const employeeSelect = document.getElementById('employeeSelect');
const department_id = document.getElementById('department_id');
const employee_id = document.getElementById('employee_id');

// Estado inicial del modal: mostrar solo "Todos" genérico
$('#downloadModal').on('show.bs.modal', function () {
    // Restablecer los selects
    generationType.value = "";
    resetAllFilters();

    // Poner el filtro en estado genérico
    filterType.innerHTML = `
        <option value="all">Todos</option>
        <option value="department">Por Departamento</option>
        <option value="employee">Por Empleado</option>
    `;
    labelFilter.innerText = "Selecciona tipo de descarga:";
    titleModal.innerText = "Generación de Resguardos y Códigos QR";
    form.action = "#";
});

generationType.addEventListener('change', function() {
    // Limpiar campos y ocultar secciones
    resetAllFilters();

    if (this.value === 'qr') {
        form.action = "{{ route('assets.bulkQrDownload') }}";
        titleModal.innerText = "Descarga Masiva de Códigos QR";
        labelFilter.innerText = "Selecciona tipo de descarga de códigos QR:";
        updateFilterOptions("QR");
    } else if (this.value === 'resguardos') {
        form.action = "{{ route('asset_assignments.bulkDownload') }}";
        titleModal.innerText = "Descarga Masiva de Resguardos";
        labelFilter.innerText = "Selecciona tipo de descarga de resguardos:";
        updateFilterOptions("Resguardo");
    } else {
        form.action = "#";
        labelFilter.innerText = "Selecciona tipo de descarga:";
        updateFilterOptions(""); // vuelve a “Todos”
    }
});

// Limpia todos los selects y oculta los divs de filtros
function resetAllFilters() {
    filterType.value = "all";
    departmentSelect.classList.add('d-none');
    employeeSelect.classList.add('d-none');
    department_id.value = "";
    employee_id.value = "";
}

// Actualiza las opciones del filtro según el tipo
function updateFilterOptions(typeLabel) {
    if (typeLabel) {
        filterType.innerHTML = `
            <option value="all">Todos los ${typeLabel}s</option>
            <option value="department">Por Departamento</option>
            <option value="employee">Por Empleado</option>
        `;
    } else {
        // Estado neutro (cuando aún no se selecciona tipo)
        filterType.innerHTML = `
            <option value="all">Todos</option>
            <option value="department">Por Departamento</option>
            <option value="employee">Por Empleado</option>
        `;
    }
}

/* === Filtros dependientes departamento/empleado === */
document.addEventListener('DOMContentLoaded', function () {
    const employeeOptions = Array.from(document.querySelectorAll('#employee_id option'));

    filterType.addEventListener('change', function () {
        departmentSelect.classList.add('d-none');
        employeeSelect.classList.add('d-none');

        if (this.value === 'department') {
            departmentSelect.classList.remove('d-none');
        }
        if (this.value === 'employee') {
            departmentSelect.classList.remove('d-none');
            employeeSelect.classList.remove('d-none');
            filterEmployeesByDepartment();
        }
    });

    document.getElementById('department_id').addEventListener('change', filterEmployeesByDepartment);

    function filterEmployeesByDepartment() {
        const deptId = document.getElementById('department_id').value;
        const employeeSelectEl = document.getElementById('employee_id');
        employeeSelectEl.innerHTML = '<option value="">-- Selecciona un empleado --</option>';

        employeeOptions.forEach(opt => {
            if (!deptId || opt.dataset.department === deptId) {
                employeeSelectEl.appendChild(opt.cloneNode(true));
            }
        });
    }
});
</script>
@stop
