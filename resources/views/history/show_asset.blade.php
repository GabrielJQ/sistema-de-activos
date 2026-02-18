@extends('layouts.admin')

@section('title', "Historial del Activo: {$asset->tag}")

@section('content')
<div class="container-fluid py-3">

    {{-- Encabezado / Hero --}}
    <div class="history-hero shadow-soft rounded-4 p-3 p-md-4 mb-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div class="d-flex align-items-start gap-3">
                <span class="icon-circle bg-guinda text-white shadow-sm">
                    <i class="fas fa-laptop-code"></i>
                </span>

                <div>
                    <h1 class="view-title fw-bold text-guinda mb-1 d-flex align-items-center gap-2">
                        Historial del Activo
                        <span class="badge bg-light text-dark border rounded-pill fw-semibold">
                            {{ $asset->tag }}
                        </span>
                    </h1>

                    <div class="d-flex flex-wrap gap-2 mt-1">
                        <span class="chip">
                            <i class="fas fa-layer-group me-1 text-guinda"></i>
                            {{ $asset->deviceType?->equipo ?? 'Sin tipo' }}
                        </span>
                        <span class="chip">
                            <i class="fas fa-barcode me-1 text-guinda"></i>
                            <span class="fw-semibold">Serie:</span> {{ $asset->serie ?? 'N/A' }}
                        </span>

                        @if(method_exists($asset,'isDecommissioned') && $asset->isDecommissioned())
                            <span class="badge bg-danger-subtle text-danger border rounded-pill px-3 py-2">
                                <i class="fas fa-ban me-1"></i> Dado de baja
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('history.index') }}" class="btn btn-light border shadow-sm px-3 py-2 rounded-3">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Card tabla --}}
    <div class="table-card shadow-soft rounded-4 bg-white overflow-hidden">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="mini-dot bg-guinda"></span>
                <div class="fw-semibold text-dark">Movimientos</div>
                <small class="text-muted">Asignaciones y devoluciones registradas</small>
            </div>

            {{-- Tip visual (no rompe DataTables) --}}
            <small class="text-muted d-none d-md-inline">
                <i class="fas fa-search me-1"></i> Usa el buscador para filtrar rápidamente
            </small>
        </div>

        <div class="table-responsive p-3">
            <table id="asset-history-table" class="table table-hover align-middle w-100 modern-table">
                <thead class="table-head text-center">
                    <tr>
                        <th class="text-start">Empleado</th>
                        <th class="text-start">Departamento</th>
                        <th>Asignado</th>
                        <th>Devuelto</th>
                        <th class="text-start">Observaciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($history as $h)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-soft">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <div class="lh-sm">
                                        <div class="fw-semibold text-dark">
                                            {{ $h->employee?->full_name ?? 'N/A' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $h->employee?->puesto ?? '' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td class="text-start">
                                <span class="chip chip-muted">
                                    <i class="fas fa-building me-1"></i>
                                    {{ $h->department?->areanom ?? $h->employee?->department?->areanom ?? 'N/A' }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if($h->assigned_at)
                                    <span class="date-pill">
                                        <i class="far fa-calendar-check me-1"></i>
                                        {{ $h->assigned_at->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($h->returned_at)
                                    <span class="date-pill date-pill-out">
                                        <i class="far fa-calendar-minus me-1"></i>
                                        {{ $h->returned_at->format('d/m/Y') }}
                                    </span>
                                @elseif($h->is_current)
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="fas fa-check me-1"></i> Actual
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-start">
                                <div class="obs text-muted">
                                    {{ $h->observations ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-cell">
                                <div class="empty-wrap">
                                    <div class="empty-icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <div class="fw-semibold text-dark mb-1">Sin movimientos</div>
                                    <div class="text-muted">
                                        {{ (method_exists($asset,'isDecommissioned') && $asset->isDecommissioned())
                                            ? 'Activo dado de baja'
                                            : 'Activo disponible' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

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

/* Icono */
.icon-circle{
    width: 46px;
    height: 46px;
    border-radius: 50%;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* Chip */
.chip{
    display:inline-flex;
    align-items:center;
    gap:.35rem;
    padding:.45rem .75rem;
    border-radius: 999px;
    background:#fff;
    border:1px solid rgba(0,0,0,.08);
    font-size:.85rem;
}
.chip-muted{
    background:#f8f9fa;
}

/* Card tabla */
.table-card{
    border: 1px solid rgba(0,0,0,.06);
}

/* Cabecera tabla */
.table-head th{
    background: #0f0f10;
    color:#fff;
    font-weight:700;
    letter-spacing:.2px;
    border-bottom: 0 !important;
    padding: .9rem 1rem !important;
    font-size:.86rem;
}
.table-head th:first-child{ border-top-left-radius: .75rem; }
.table-head th:last-child{ border-top-right-radius: .75rem; }

/* Tabla */
.modern-table td{
    padding: .9rem 1rem !important;
    font-size: .9rem;
    vertical-align: middle;
    border-color: rgba(0,0,0,.06);
}
.modern-table tbody tr:hover{
    background: rgba(97,18,50,.04) !important;
}

/* Avatar soft */
.avatar-soft{
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display:flex;
    align-items:center;
    justify-content:center;
    background: rgba(97,18,50,.08);
    color:#611232;
}

/* Fechas */
.date-pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:.35rem;
    padding:.45rem .65rem;
    border-radius: 999px;
    border:1px solid rgba(0,0,0,.08);
    background:#fff;
    font-size:.85rem;
    white-space:nowrap;
}
.date-pill-out{
    background:#f8f9fa;
}

/* Observaciones: evita que una obs enorme rompa la fila */
.obs{
    max-width: 520px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow:hidden;
}

/* Vacio */
.empty-cell{
    padding: 52px 12px !important;
    text-align:center;
    background: #fcfcfd;
}
.empty-wrap{
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:.35rem;
}
.empty-icon{
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display:flex;
    align-items:center;
    justify-content:center;
    background: rgba(0,0,0,.05);
    color:#222;
    font-size: 1.2rem;
}

/* Mini detalle arriba */
.mini-dot{
    width:10px;
    height:10px;
    border-radius:50%;
    display:inline-block;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    let table = $('#asset-history-table');

    if (table.find('tbody tr').not(':has(td[colspan])').length > 0) {
        table.DataTable({
            responsive: true,
            autoWidth: false,
            destroy: true,
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                paginate: { next:"&rsaquo;", previous:"&lsaquo;" },
                emptyTable: "No hay datos",
            },
            pageLength: 10,
        });
    }
});
</script>
@stop
