@extends('layouts.admin')

@section('title', 'Catálogo de Proveedores')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h1 class="view-title fw-bold text-guinda d-flex align-items-center gap-2 mb-0">
        <i class="fas fa-handshake"></i> Catálogo de Proveedores
    </h1>

    @if(hasRole(['super_admin','admin']))
        <a href="{{ route('suppliers.create') }}" class="btn btn-guinda px-4 py-2 shadow-sm">
            <i class="fas fa-plus me-1"></i> Nuevo Proveedor
        </a>
    @endif
</div>
@stop

@section('content')
<div class="container-fluid mt-3">

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

    {{-- Grid de proveedores --}}
    <div class="row g-4">
        @forelse($suppliers as $supplier)
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card supplier-card h-100 border-0 rounded-4 shadow-soft">

                    <div class="card-body d-flex flex-column">

                        {{-- Logo + Estado --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="supplier-logo-wrapper">
                                <img
                                    src="{{ $supplier->logo_path ? asset($supplier->logo_path) : asset('images/logos/default-logo.png') }}"
                                    alt="{{ $supplier->prvnombre }}"
                                    class="supplier-logo">
                            </div>

                            <span class="badge status-badge {{ $supplier->prvstatus ? 'bg-success-soft' : 'bg-secondary-soft' }}">
                                {{ $supplier->prvstatus ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        {{-- Información --}}
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-2 text-truncate">
                                {{ $supplier->prvnombre }}
                            </h5>

                            <p class="text-muted mb-1 small">
                                <i class="fas fa-file-contract me-1"></i>
                                <strong>Contrato:</strong> {{ $supplier->contrato ?? '-' }}
                            </p>

                            <p class="text-muted mb-1 small">
                                <i class="fas fa-phone me-1"></i>
                                <strong>Teléfono:</strong> {{ $supplier->telefono ?? '-' }}
                            </p>

                            <p class="text-muted mb-0 small text-truncate">
                                <i class="fas fa-link me-1"></i>
                                <strong>Enlace:</strong> {{ $supplier->enlace ?? '-' }}
                            </p>
                        </div>

                        {{-- Acciones --}}
                        @if(hasRole(['super_admin','admin']))
                        <div class="d-flex justify-content-end gap-2 mt-4 flex-wrap">
                            <a href="{{ route('suppliers.edit', $supplier) }}"
                               class="btn btn-guinda btn-sm px-3">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>

                            <button type="button"
                                class="btn btn-outline-danger btn-sm px-3"
                                data-confirm-delete
                                data-name="{{ $supplier->prvnombre }}"
                                data-text="¿Deseas eliminar este proveedor?"
                                data-action="{{ route('suppliers.destroy', $supplier) }}">
                                <i class="fas fa-trash me-1"></i> Eliminar
                            </button>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            {{-- Estado vacío --}}
            <div class="col-12">
                <div class="empty-state text-center py-5 rounded-4 shadow-soft bg-white">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="fs-5 text-muted mb-2">No se encontraron proveedores</p>
                    @if(hasRole(['super_admin','admin']))
                        <a href="{{ route('suppliers.create') }}" class="btn btn-guinda">
                            <i class="fas fa-plus me-1"></i> Crear proveedor
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($suppliers instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="d-flex justify-content-center mt-4" id="paginationContainer">
            {{ $suppliers->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    @endif

</div>
@stop

@section('css')
<style>
/* ===================== */
/* PALETA CORPORATIVA */
/* ===================== */
:root {
    --guinda: #611232;
    --guinda-dark: #4b0f27;
}

.text-guinda { color: var(--guinda) !important; }

/* ===================== */
/* TÍTULO */
/* ===================== */
.view-title {
    font-size: clamp(1.6rem, 2vw, 2.1rem);
}

/* ===================== */
/* BOTONES */
/* ===================== */
.btn-guinda {
    background-color: var(--guinda);
    color: #fff;
    border-radius: .55rem;
    border: 1px solid var(--guinda);
    transition: .25s ease;
}
.btn-guinda:hover {
    background-color: var(--guinda-dark);
    border-color: var(--guinda-dark);
    color: #fff;
}

.btn-outline-danger {
    border-radius: .55rem;
    transition: .25s ease;
}

/* ===================== */
/* CARDS */
/* ===================== */
.shadow-soft {
    box-shadow: 0 4px 14px rgba(0,0,0,.08);
}

.supplier-card {
    transition: transform .25s ease, box-shadow .25s ease;
}
.supplier-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(97,18,50,.18);
}

/* ===================== */
/* LOGO */
/* ===================== */
.supplier-logo-wrapper {
    width: 100px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.supplier-logo {
    max-height: 60px;
    max-width: 100%;
    object-fit: contain;
}

/* ===================== */
/* BADGES */
/* ===================== */
.status-badge {
    font-size: 1rem;
    padding: .35rem .6rem;
    border-radius: .5rem;
}
.bg-success-soft {
    background-color: #e6f4ea;
    color: #0f5132;
}
.bg-secondary-soft {
    background-color: #e9ecef;
    color: #6c757d;
}

/* ===================== */
/* EMPTY STATE */
/* ===================== */
.empty-state {
    background: #fff;
}

/* ===================== */
/* RESPONSIVE */
/* ===================== */
@media (max-width: 576px) {
    .btn-guinda,
    .btn-outline-danger {
        width: 100%;
    }
}
</style>
@stop
