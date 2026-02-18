@extends('layouts.admin')

@section('title', 'Historial de Asignaciones')

@section('content_header')
<div class="history-hero shadow-soft rounded-4 p-3 p-md-4 mb-3">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
        <div class="d-flex align-items-start gap-3">
            <span class="icon-circle bg-guinda text-white shadow-sm">
                <i class="fas fa-history"></i>
            </span>

            <div>
                <h1 class="view-title fw-bold text-guinda d-flex align-items-center gap-2 mb-1">
                    Historial de Asignaciones
                </h1>
                <div class="d-flex flex-wrap gap-2 mt-1">
                    <span class="chip">
                        <i class="fas fa-filter me-1 text-guinda"></i>
                        Consulta histórica por empleado y por activo
                    </span>
                    <span class="chip chip-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Tip: usa el buscador para filtrar rápido
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-soft rounded-4 border-0" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="fas fa-check-circle mt-1"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-soft rounded-4 border-0" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="fas fa-exclamation-triangle mt-1"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tabs + content --}}
    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-0">

            <div class="tabs-wrap px-3 pt-3 pb-2 border-bottom">
                <ul class="nav nav-pills modern-pills gap-2" id="historyTabs" role="tablist">

                    <li class="nav-item">
                        <button class="nav-link active pill-tab"
                                id="employees-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#employees"
                                type="button" role="tab">
                            <i class="fas fa-user me-1"></i> Empleados
                        </button>
                    </li>

                    @if(hasRole(['super_admin','admin','collaborator']))
                    <li class="nav-item">
                        <button class="nav-link pill-tab"
                                id="assets-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#assets"
                                type="button" role="tab">
                            <i class="fas fa-laptop me-1"></i> Bienes
                        </button>
                    </li>
                    @endif

                </ul>
            </div>

            <div class="tab-content p-3 p-md-4" id="historyTabsContent">

                {{-- ================= EMPLEADOS ================= --}}
                <div class="tab-pane fade show active" id="employees" role="tabpanel">

                    <div class="table-card">
                        <div class="table-card-head">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mini-dot bg-guinda"></span>
                                <div class="fw-semibold text-dark">Empleados con historial</div>
                                <small class="text-muted">Consulta por persona</small>
                            </div>
                        </div>

                        <div class="table-responsive p-3">
                            <table class="table table-hover align-middle datatable-employees modern-table w-100">
                                <thead class="table-head">
                                    <tr>
                                        <th class="text-start">Empleado</th>
                                        <th class="text-start">Departamento</th>
                                        <th>Status</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="avatar-soft">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                    <div class="lh-sm">
                                                        <div class="fw-semibold text-dark">{{ $employee->full_name }}</div>
                                                        @if(!empty($employee->puesto))
                                                            <small class="text-muted">{{ $employee->puesto }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-start">
                                                <span class="chip chip-muted">
                                                    <i class="fas fa-building me-1"></i>
                                                    {{ $employee->department?->areanom ?? '-' }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                @php $status = strtolower($employee->status ?? ''); @endphp
                                                @if($status === 'activo')
                                                    <span class="status-pill status-ok">
                                                        <i class="fas fa-check me-1"></i> Activo
                                                    </span>
                                                @elseif($status === 'inactivo')
                                                    <span class="status-pill status-bad">
                                                        <i class="fas fa-times me-1"></i> Inactivo
                                                    </span>
                                                @else
                                                    <span class="status-pill status-mid">
                                                        <i class="fas fa-minus me-1"></i> Sin definir
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('history.showEmployee', $employee->id) }}"
                                                   class="btn btn-guinda btn-sm px-3 rounded-3">
                                                    <i class="fas fa-eye me-1"></i> Ver historial
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="empty-cell">
                                                <div class="empty-wrap">
                                                    <div class="empty-icon"><i class="fas fa-users-slash"></i></div>
                                                    <div class="fw-semibold text-dark mb-1">Sin resultados</div>
                                                    <div class="text-muted">No hay empleados con historial.</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- ================= ACTIVOS ================= --}}
                <div class="tab-pane fade" id="assets" role="tabpanel">

                    <div class="table-card">
                        <div class="table-card-head">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mini-dot bg-guinda"></span>
                                <div class="fw-semibold text-dark">Activos con historial</div>
                                <small class="text-muted">Consulta por TAG/DICO</small>
                            </div>
                        </div>

                        <div class="table-responsive p-3">
                            <table class="table table-hover align-middle datatable-assets modern-table w-100">
                                <thead class="table-head">
                                    <tr>
                                        <th class="text-start">TAG / DICO</th>
                                        <th class="text-start">Tipo</th>
                                        <th class="text-start">Serie</th>
                                        <th>Status</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($assets as $asset)
                                        <tr>
                                            <td class="text-start">
                                                <span class="tag-badge">
                                                    <i class="fas fa-tag me-1"></i>
                                                    {{ $asset->tag }}
                                                </span>
                                            </td>

                                            <td class="text-start">
                                                <span class="chip chip-muted">
                                                    <i class="fas fa-laptop me-1"></i>
                                                    {{ $asset->deviceType?->equipo ?? '-' }}
                                                </span>
                                            </td>

                                            <td class="text-start">
                                                <span class="fw-semibold text-dark">{{ $asset->serie ?? '-' }}</span>
                                            </td>

                                            <td class="text-center">
                                                <span class="status-pill {{ $asset->isDecommissioned() ? 'status-bad' : 'status-ok' }}">
                                                    {!! $asset->isDecommissioned()
                                                        ? '<i class="fas fa-ban me-1"></i> Baja'
                                                        : '<i class="fas fa-play-circle me-1"></i> En operación' !!}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('history.showAsset', $asset->id) }}"
                                                   class="btn btn-guinda btn-sm px-3 rounded-3">
                                                    <i class="fas fa-eye me-1"></i> Ver historial
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="empty-cell">
                                                <div class="empty-wrap">
                                                    <div class="empty-icon"><i class="fas fa-box-open"></i></div>
                                                    <div class="fw-semibold text-dark mb-1">Sin resultados</div>
                                                    <div class="text-muted">No hay activos con historial.</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>

            </div><!-- tab-content -->
        </div><!-- card-body -->
    </div><!-- card -->

</div>
@stop

@section('css')
<style>
/* Paleta */
.text-guinda{ color:#611232!important; }
.bg-guinda{ background-color:#611232!important; }
.shadow-soft{ box-shadow: 0 8px 20px rgba(0,0,0,.08); }

/* Hero */
.history-hero{
    background: linear-gradient(135deg, rgba(97,18,50,.06), rgba(255,255,255,1));
    border: 1px solid rgba(0,0,0,.06);
}

/* Icon */
.icon-circle{
    width: 46px; height: 46px;
    border-radius: 50%;
    display:flex; align-items:center; justify-content:center;
}

/* Chips */
.chip{
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.45rem .75rem;
    border-radius: 999px;
    background:#fff;
    border:1px solid rgba(0,0,0,.08);
    font-size:.85rem;
}
.chip-muted{ background:#f8f9fa; }

/* Tabs pills */
.tabs-wrap{ background:#fff; }
.modern-pills .pill-tab{
    border-radius: 999px;
    padding: .55rem 1rem;
    font-weight: 700;
    color:#611232;
    background: rgba(97,18,50,.06);
    border: 1px solid rgba(97,18,50,.18);
    transition: .2s;
}
.modern-pills .pill-tab:hover{
    background: rgba(97,18,50,.10);
}
.modern-pills .pill-tab.active{
    color:#fff !important;
    background:#611232 !important;
    border-color:#611232 !important;
    box-shadow: 0 8px 16px rgba(97,18,50,.22);
}

/* Table card */
.table-card{
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 1rem;
    overflow: hidden;
    background:#fff;
}
.table-card-head{
    padding: .85rem 1rem;
    border-bottom: 1px solid rgba(0,0,0,.06);
    background: #fcfcfd;
}

/* Mini dot */
.mini-dot{ width:10px; height:10px; border-radius:50%; display:inline-block; }

/* Table head */
.table-head th{
    background:#0f0f10;
    color:#fff;
    font-weight:800;
    letter-spacing:.2px;
    border-bottom:0 !important;
    padding:.9rem 1rem !important;
    font-size:.86rem;
}

/* Table */
.modern-table td{
    padding: .9rem 1rem !important;
    font-size: .9rem;
    vertical-align: middle;
    border-color: rgba(0,0,0,.06);
}
.modern-table tbody tr:hover{
    background: rgba(97,18,50,.04) !important;
}

/* Avatar */
.avatar-soft{
    width: 34px; height: 34px;
    border-radius: 10px;
    display:flex; align-items:center; justify-content:center;
    background: rgba(97,18,50,.08);
    color:#611232;
}

/* TAG badge */
.tag-badge{
    display:inline-flex; align-items:center;
    padding:.45rem .75rem;
    border-radius:999px;
    background: rgba(97,18,50,.08);
    border:1px solid rgba(97,18,50,.18);
    color:#611232;
    font-weight:800;
    font-size:.85rem;
    white-space:nowrap;
}

/* Status pills */
.status-pill{
    display:inline-flex; align-items:center; justify-content:center;
    padding:.45rem .75rem;
    border-radius:999px;
    font-weight:800;
    font-size:.85rem;
    border:1px solid rgba(0,0,0,.08);
    white-space:nowrap;
}
.status-ok{
    background: rgba(25,135,84,.12);
    color:#198754;
    border-color: rgba(25,135,84,.25);
}
.status-bad{
    background: rgba(220,53,69,.12);
    color:#dc3545;
    border-color: rgba(220,53,69,.25);
}
.status-mid{
    background: rgba(108,117,125,.12);
    color:#6c757d;
    border-color: rgba(108,117,125,.25);
}

/* Buttons */
.btn-guinda{
    background-color:#611232 !important;
    border:1px solid #611232 !important;
    color:#fff !important;
}
.btn-guinda:hover{
    background-color:#4b0f27 !important;
    border-color:#4b0f27 !important;
}

/* Empty state */
.empty-cell{ padding: 52px 12px !important; text-align:center; background:#fcfcfd; }
.empty-wrap{ display:flex; flex-direction:column; align-items:center; gap:.35rem; }
.empty-icon{
    width: 52px; height: 52px;
    border-radius: 14px;
    display:flex; align-items:center; justify-content:center;
    background: rgba(0,0,0,.05);
    color:#222;
    font-size: 1.2rem;
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
                        paginate: { first:"Primero", last:"Último", next:"&rsaquo;", previous:"&lsaquo;" },
                        emptyTable: "No hay datos",
                    },
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                });
            }
        }
    }

    init($('.datatable-employees'));
    init($('.datatable-assets'));

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).data('bs-target');
        $(target).find('table').each(function () {
            init(this);
            try { $(this).DataTable().columns.adjust(); } catch {}
        });
    });

});
</script>
@stop
