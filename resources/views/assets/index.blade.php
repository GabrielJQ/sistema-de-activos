@extends('layouts.admin')

@section('title', 'Inventario de Activos')

@section('content_header')
<div class="mb-3">
    <h1 class="view-title fw-bold text-guinda d-flex align-items-center gap-2">
        <span class="icon-circle bg-guinda text-white">
            <i class="fas fa-search"></i>
        </span>
        Inventario de Activos
    </h1>
    <small class="text-muted">
        Consulta, administra y exporta el inventario de activos informáticos
    </small>
</div>
@stop

@section('content')
<div class="container-fluid">

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-soft rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-soft rounded-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Acciones --}}
    <div class="card border-0 shadow-soft rounded-4 mb-4">
        <div class="card-body py-3 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 btn_actions">

                <div>
                    @if(hasRole(['super_admin','admin']))
                        <a href="{{ route('assets.create') }}" class="btn btn-actions-new">
                            <i class="fas fa-plus me-1"></i> Nuevo Activo
                        </a>
                    @else
                        <div style="width:150px;"></div>
                    @endif
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    @if(hasRole(['super_admin','admin']))
                        <a href="{{ route('assets.import') }}" class="btn btn-guinda-outline">
                            <i class="fas fa-file-import me-1"></i> Importar
                        </a>
                    @endif

                    <a href="{{ route('assets.export') }}" class="btn btn-secondary">
                        <i class="fas fa-file-export me-1"></i> Exportar
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="card border-0 shadow-soft rounded-4">
        <div class="card-body p-0">

            <ul class="nav nav-tabs asset-tabs border-0 px-3 pt-3" id="assetTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active custom-tab" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assigned">
                        <i class="fas fa-user-check me-1"></i> Asignados
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link custom-tab" id="unassigned-tab" data-bs-toggle="tab" data-bs-target="#unassigned">
                        <i class="fas fa-user-clock me-1"></i> Disponibles
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link custom-tab" id="damaged-tab" data-bs-toggle="tab" data-bs-target="#damaged">
                        <i class="fas fa-exclamation-triangle me-1"></i> Dañados
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link custom-tab" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactive">
                        <i class="fas fa-ban me-1"></i> Inactivos
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4" id="assetTabsContent">

                <div class="tab-pane fade show active" id="assigned">
                    <div class="table-card">
                        @include('assets.partials.asset_table', [
                            'activos' => $assignedAssets,
                            'damagedTab' => false,
                            'inactiveTab' => false
                        ])
                    </div>
                </div>

                <div class="tab-pane fade" id="unassigned">
                    <div class="table-card">
                        @include('assets.partials.asset_table', [
                            'activos' => $unassignedAssets,
                            'damagedTab' => false,
                            'inactiveTab' => false
                        ])
                    </div>
                </div>

                <div class="tab-pane fade" id="damaged">
                    <div class="table-card">
                        @include('assets.partials.asset_table', [
                            'activos' => $damagedAssets,
                            'damagedTab' => true,
                            'inactiveTab' => false
                        ])
                    </div>
                </div>

                <div class="tab-pane fade" id="inactive">
                    <div class="table-card">
                        @include('assets.partials.asset_table', [
                            'activos' => $inactiveAssets,
                            'damagedTab' => false,
                            'inactiveTab' => true
                        ])
                    </div>
                </div>

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
/* ALERTAS */
/* ============================= */
.shadow-soft { box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important; }

/* ============================= */
/* BOTONES PRINCIPALES */
/* ============================= */
.btn-actions-new {
    border-radius: .55rem;
    font-weight: 500;
    background-color: #611232 !important;
    border: 1px solid #611232 !important;
    color: #fff !important;
    transition: .25s ease-in-out;
}
.btn-actions-new:hover {
    background-color: #4b0f27 !important;
    border-color: #4b0f27 !important;
}

.btn-guinda-outline {
    border-radius: .55rem;
    font-weight: 500;
    background: #fff !important;
    color: #611232 !important;
    border: 1px solid #611232 !important;
    transition: .25s ease-in-out;
}
.btn-guinda-outline:hover {
    background-color: #4b0f27 !important;
    border-color: #4b0f27 !important;
    color: #fff !important;
}

.btn-info {
    background-color: #0d6efd;
    color: #fff;
    border-radius: 0.5rem;
    border: 1px solid #0d6efd;
    transition: 0.3s ease;
}
.btn-info:hover {
    background-color: #1d7dfc;
    color: #fff;
    box-shadow: 0 0 8px rgba(13,110,253,0.3);
}
/* Botón gris */
.btn-secondary {
    border-radius: .55rem;
    font-weight: 600;
}

/* ============================= */
/* TABS */
/* ============================= */
.nav-tabs .custom-tab {
    border: none !important;
    color: #611232 !important;
    font-weight: 600;
    border-bottom: 3px solid transparent !important;
    background: transparent !important;
    padding: .85rem 1.2rem !important;
    transition: .25s ease;
}
.nav-tabs .custom-tab.active {
    border-bottom: 3px solid #611232 !important;
    background: #f5f5f5 !important;
}
.nav-tabs .custom-tab:hover {
    border-bottom: 3px solid #61123280 !important;
}

/* ============================= */
/* TARJETA (contenedor de tablas) */
/* ============================= */
.table-card {
    border-radius: 1rem !important;
    background: #fff;
    padding: 1.2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

/* ============================= */
/* TABLAS */
/* ============================= */
.modern-table th,
.modern-table td {
    padding: .85rem 1rem !important;
    font-size: .9rem;
}

/* Hover de tabla IGUAL QUE EMPLEADOS */
.modern-table tbody tr:hover {
    background-color: #611232 !important;
    color: #fff !important;
}

/* Forzar textos internos a blanco en hover */
.modern-table tbody tr:hover td * {
    color: #fff !important;
}

/* PERO 👉 los botones conservan su propio color */
.modern-table tbody tr:hover td .btn,
.modern-table tbody tr:hover td .btn i {
    color: inherit !important;
}

/* ============================= */
/* ENCABEZADOS DATATABLES */
/* ============================= */
table.dataTable thead th {
    background-color: #000 !important;
    color: #fff !important;
    text-align: center !important;
    border: none !important;
    padding: 14px !important;
}

/* ============================= */
/* BADGES */
/* ============================= */
.badge-soft {
    background-color: #611232 !important;
    color: #fff !important;
    border-radius: .45rem !important;
    font-size: .8rem !important;
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
    function initDataTable(table) {
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
        initDataTable(this);
    });

    // Inicializar tablas al cambiar pestaña
    $('#assetTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).data('bs-target');
        $(target).find('.datatable').each(function() {
            initDataTable(this);
            $(this).DataTable().columns.adjust();
        });
    });

    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Quitar preloader
    $('.preloader').remove();
});

</script>
@stop
