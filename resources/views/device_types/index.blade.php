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

    {{-- Filtro --}}
    <div class="mb-4">
        <form id="filterForm" action="{{ route('device_types.index') }}" method="GET" class="w-100">
            <div class="input-group modern-search" style="max-width:380px;">
                <span class="input-group-text bg-transparent border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text"
                       id="searchInput"
                       name="search"
                       class="form-control border-start-0 shadow-none"
                       placeholder="Buscar por nombre o descripción..."
                       value="{{ request('search') }}">
            </div>
        </form>
    </div>

    {{-- Cards --}}
    <div id="deviceTypesList" class="row g-4">

        @forelse($deviceTypes as $type)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card modern-card h-100 border-0">

                    <div class="card-body d-flex flex-column justify-content-between">

                        {{-- Info --}}
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $type->image_path ? asset($type->image_path) : asset('images/dispositivos/default.png') }}"
                                 alt="{{ $type->equipo }}"
                                 class="device-img">

                            <div class="overflow-hidden">
                                <h5 class="fw-bold mb-1 text-truncate">{{ $type->equipo }}</h5>
                                <p class="text-muted small mb-0 text-truncate">
                                    {{ $type->descripcion }}
                                </p>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="d-flex justify-content-end gap-2 mt-4 flex-wrap">
                            <a href="{{ route('device_types.edit', $type->id) }}"
                               class="btn btn-guinda btn-sm px-3 py-2 shadow-sm d-flex align-items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </a>

                            <button type="button"
                                    class="btn btn-outline-danger btn-sm px-3 py-2 shadow-sm d-flex align-items-center gap-1"
                                    data-confirm-delete
                                    data-name="{{ $type->equipo }}"
                                    data-text="¿Deseas eliminar el tipo de dispositivo?"
                                    data-action="{{ route('device_types.destroy', $type->id) }}">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="fs-5 text-muted">No se encontraron tipos de dispositivos</p>
                <a href="{{ route('device_types.create') }}"
                   class="btn btn-guinda px-4 py-2 shadow-sm">
                    <i class="fas fa-plus me-1"></i> Crear uno nuevo
                </a>
            </div>
        @endforelse

    </div>

    {{-- Paginación --}}
    @if($deviceTypes instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="d-flex justify-content-center mt-4" id="paginationContainer">
            {{ $deviceTypes->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    @endif

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
<script>
/* JS INTACTO */
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');
    const deviceTypesList = document.getElementById('deviceTypesList');
    const paginationContainer = document.getElementById('paginationContainer');
    let timer;

    function fetchDeviceTypes(url = null) {
        if (!url) {
            url = filterForm.action + "?" + new URLSearchParams(new FormData(filterForm)).toString();
        }

        fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, "text/html");

                const newList = doc.getElementById('deviceTypesList');
                if (newList) deviceTypesList.innerHTML = newList.innerHTML;

                const newPagination = doc.getElementById('paginationContainer');
                if (newPagination) paginationContainer.innerHTML = newPagination.innerHTML;

                window.history.pushState({}, '', url);
            });
    }

    searchInput.addEventListener('keyup', () => {
        clearTimeout(timer);
        timer = setTimeout(() => fetchDeviceTypes(), 500);
    });

    filterForm.addEventListener('submit', e => {
        e.preventDefault();
        fetchDeviceTypes();
    });

    document.addEventListener('click', e => {
        const link = e.target.closest('.pagination a');
        if (!link) return;
        e.preventDefault();
        fetchDeviceTypes(link.href);
    });

    window.addEventListener('popstate', () => fetchDeviceTypes(window.location.href));
});
</script>
@stop
