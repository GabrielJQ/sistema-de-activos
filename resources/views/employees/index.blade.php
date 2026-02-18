@extends('layouts.admin')

@section('title', 'Empleados')

@section('content_header')
<div class="d-flex align-items-center gap-2 mb-2">
    <span class="icon-circle bg-guinda text-white">
        <i class="fas fa-users"></i>
    </span>
    <h1 class="view-title fw-bold text-guinda mb-0">
        Directorio de Empleados
    </h1>
</div>
@stop

@section('content')
<div class="container-fluid">

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-soft rounded-4 mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-soft rounded-4 mb-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Acciones superiores --}}
    <div class="card border-0 shadow-soft rounded-4 mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">

            {{-- Nuevo --}}
            @if(hasRole(['super_admin','admin']))
                <a href="{{ route('employees.create') }}" class="btn btn-actions-new px-4">
                    <i class="fas fa-plus me-1"></i> Nuevo Empleado
                </a>
            @else
                <div></div>
            @endif

            {{-- Import / Export --}}
            <div class="d-flex gap-2 flex-wrap ms-auto">
                @if(hasRole(['super_admin','admin']))
                    <a href="{{ route('employees.showImport') }}" class="btn btn-guinda-outline px-4">
                        <i class="fas fa-file-import me-1"></i> Importar
                    </a>
                @endif

                <a href="{{ route('employees.exportForm') }}" class="btn btn-secondary px-4">
                    <i class="fas fa-file-export me-1"></i> Exportar
                </a>
            </div>

        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs modern-tabs mb-3" id="employeeTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link custom-tab active"
                    data-bs-toggle="tab"
                    data-bs-target="#active"
                    type="button">
                <i class="fas fa-user-check me-1"></i> Activos
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link custom-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#inactive"
                    type="button">
                <i class="fas fa-user-slash me-1"></i> Inactivos
            </button>
        </li>
    </ul>

    {{-- Contenido --}}
    <div class="tab-content">

        <div class="tab-pane fade show active" id="active">
            <div class="table-card">
                @include('employees.partials.employee_table', ['employees' => $activeEmployees])
            </div>
        </div>

        <div class="tab-pane fade" id="inactive">
            <div class="table-card">
                @include('employees.partials.employee_table', ['employees' => $inactiveEmployees])
            </div>
        </div>

    </div>

</div>
@stop


@section('css')
<style>
/* ============================= */
/* PALETA CORPORATIVA */
/* ============================= */
.text-guinda { color: #611232 !important; }
.bg-guinda { background-color: #611232 !important; }

/* ============================= */
/* ICONO DE ENCABEZADO */
/* ============================= */
.icon-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ============================= */
/* SOMBRA SUAVE */
/* ============================= */
.shadow-soft {
    box-shadow: 0 6px 16px rgba(0,0,0,.08) !important;
}

/* ============================= */
/* BOTONES */
/* ============================= */
.btn-actions-new,
.btn-guinda-outline {
    border-radius: .55rem;
    font-weight: 600;
    transition: .25s ease-in-out;
}

/* Guinda sólido */
.btn-actions-new {
    background-color: #611232 !important;
    border: 1px solid #611232 !important;
    color: #fff !important;
}
.btn-actions-new:hover {
    background-color: #4b0f27 !important;
    border-color: #4b0f27 !important;
}

/* Guinda outline */
.btn-guinda-outline {
    background: #fff !important;
    color: #611232 !important;
    border: 1px solid #611232 !important;
}
.btn-guinda-outline:hover {
    background-color: #611232 !important;
    color: #fff !important;
}

/* Secundario */
.btn-secondary {
    border-radius: .55rem;
    font-weight: 600;
}

/* ============================= */
/* TABS */
/* ============================= */
.modern-tabs .custom-tab {
    border: none;
    background: transparent;
    color: #611232;
    font-weight: 600;
    border-bottom: 3px solid transparent;
    padding: .85rem 1.3rem;
    transition: .25s ease;
}
.modern-tabs .custom-tab.active {
    border-bottom: 3px solid #611232;
    background: #f5f5f5;
}
.modern-tabs .custom-tab:hover {
    border-bottom: 3px solid rgba(97,18,50,.5);
}

/* ============================= */
/* TARJETA TABLA */
/* ============================= */
.table-card {
    border-radius: 1rem;
    background: #fff;
    padding: 1rem;
    box-shadow: 0 6px 16px rgba(0,0,0,.06);
}

/* ============================= */
/* TABLAS */
/* ============================= */
.modern-table th,
.modern-table td {
    padding: .85rem 1rem !important;
    font-size: .9rem;
    vertical-align: middle;
}

/* Hover corporativo */
.modern-table tbody tr:hover {
    background-color: #611232 !important;
    color: #fff !important;
}

/* Encabezados DataTable */
table.dataTable thead th {
    background-color: #000 !important;
    color: #fff !important;
    text-align: center;
    border: none !important;
}

/* ============================= */
/* RESPONSIVE */
/* ============================= */
@media (max-width: 576px) {
    .btn-actions-new,
    .btn-guinda-outline,
    .btn-secondary {
        width: 100%;
        text-align: center;
    }
}


</style>
@stop


@section('js')
<script>
$(document).ready(function () {

    function init(table) {
        if (!$.fn.DataTable.isDataTable(table)) {

            let hasData = $(table).find('tbody tr').not(':has(td[colspan])').length > 0;

            if (hasData) {
                $(table).DataTable({
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
                    lengthMenu: [10, 25, 50, 100],
                });
            }
        }
    }

    // Inicializar tabla visible
    $('.tab-pane.active .datatable').each(function() {
        init(this);
    });

    // Inicializar al cambiar pestaña
    $('#employeeTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).data('bs-target');

        $(target).find('.datatable').each(function() {
            init(this);
            try { $(this).DataTable().columns.adjust(); } catch {}
        });
    });

});
</script>
@stop
