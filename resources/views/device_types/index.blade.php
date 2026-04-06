@extends('layouts.admin')

@section('title', 'Catálogo de Tipos de Dispositivos')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="mb-4">
        <h1 class="view-title d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-list-alt text-guinda"></i>
            Catálogo de Tipos de Dispositivos
        </h1>

        <a href="{{ route('device_types.create') }}"
           class="btn btn-guinda fw-semibold px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2">
            <i class="fas fa-plus"></i> Nuevo Tipo de Dispositivo
        </a>
    </div>

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

    {{-- Livewire Component --}}
    @livewire('device-type-index')

</div>
@stop

@section('css')
<style>
/* ============================= */
/* PALETA */
/* ============================= */
.text-guinda { color:#611232!important; }

/* ============================= */
/* TITULO */
/* ============================= */
.view-title {
    font-size: clamp(1.6rem, 2vw, 2rem);
    font-weight: 700;
    color:#611232;
}

/* ============================= */
/* BOTONES */
/* ============================= */
.btn-guinda {
    background:#611232;
    color:#fff;
    border-radius:.55rem;
    border:1px solid #611232;
    transition:.25s;
}
.btn-guinda:hover {
    background:#4b0f27;
    border-color:#4b0f27;
    color:#fff;
}

.btn-outline-danger {
    border-radius:.55rem;
    border:1px solid #ccc;
    color:#555;
    background:#fff;
}
.btn-outline-danger:hover {
    background:#dc3545;
    color:#fff;
}

/* ============================= */
/* SEARCH */
/* ============================= */
.modern-search .form-control {
    border-radius:50px!important;
    padding:.65rem 1rem;
    border:1.7px solid #d0cdd1!important;
}
.modern-search .form-control:focus {
    border-color:#611232!important;
    box-shadow:0 0 8px rgba(97,18,50,.25);
}

/* ============================= */
/* CARDS */
/* ============================= */
.modern-card {
    border-radius:1rem!important;
    box-shadow:0 4px 12px rgba(0,0,0,.08);
    transition:.25s ease;
}
.modern-card:hover {
    transform:translateY(-4px);
    box-shadow:0 8px 22px rgba(0,0,0,.12);
}

.device-img {
    width:90px;
    height:90px;
    object-fit:contain;
    border-radius:.6rem;
    background:#f8f8f8;
    padding:6px;
}

/* ============================= */
/* PAGINACIÓN */
/* ============================= */
.pagination a {
    border-radius:.5rem!important;
}
</style>
@stop

@section('js')
@stop
