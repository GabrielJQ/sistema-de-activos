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

    {{-- Livewire Component for History --}}
    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @livewire('history-index')
        </div>
    </div>

</div>
@stop

@section('css')
<style>
/* ... (reusing existing styles) ... */
.search-group {
    background: #fff;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,.08);
    overflow: hidden;
}
.search-group:focus-within {
    border-color: #611232;
    box-shadow: 0 0 0 3px rgba(97,18,50,.1);
}
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
    // No JS initialization needed for Livewire tables
</script>
@stop
