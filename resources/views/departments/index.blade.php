@extends('layouts.admin')

@section('title', 'Directorio de Departamentos')

@section('content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
        <h1 class="view-title fw-bold text-guinda d-flex align-items-center gap-2 mb-0">
            <i class="fas fa-building"></i> Directorio de Departamentos
        </h1>

        <div class="d-flex gap-2 flex-wrap">
            @if(!hasrole(['super_admin']))
                <a href="{{ route('departments.create') }}" class="btn btn-guinda px-3 py-2">
                    <i class="fas fa-plus me-1"></i> Nuevo Departamento
                </a>
            @endif

            @if(hasRole(['super_admin']))
                <a href="{{ route('departments.import') }}" class="btn btn-guinda-outline px-3 py-2">
                    <i class="fas fa-file-import me-1"></i> Importar Regiones/Unidades/Departamentos
                </a>
            @endif
        </div>
    </div>

    {{-- ================= FILTROS ================= --}}
    @if(!hasrole(['super_admin']))
    <div class="card shadow-soft rounded-4 border-0 mb-4">
        <div class="card-body">
            <form id="filterForm" action="{{ route('departments.index') }}" method="GET"
                  class="d-flex flex-wrap gap-2 align-items-center">

                <input type="text"
                       name="search"
                       id="searchInput"
                       class="form-control flex-grow-1"
                       placeholder="Buscar por nombre, unidad o dirección…"
                       value="{{ request('search') }}"
                       style="max-width: 340px;">

                <select name="tipo" id="tipoSelect" class="form-select" style="max-width: 200px;">
                    <option value="">Todos los tipos</option>
                    <option value="Oficina" {{ request('tipo')=='Oficina'?'selected':'' }}>🏢 Oficina</option>
                    <option value="Almacen" {{ request('tipo')=='Almacen'?'selected':'' }}>🏬 Almacén</option>
                    <option value="Otro" {{ request('tipo')=='Otro'?'selected':'' }}>📦 Otro</option>
                </select>

                <button type="submit" class="btn btn-secondary px-4">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    {{-- ================= LISTADO ================= --}}
    <div id="departmentsList" class="row g-3">
        @forelse($departments as $dept)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dept-card h-100 border-0 shadow-soft rounded-4">
                    <div class="card-body d-flex flex-column justify-content-between">

                        <div>
                            <h5 class="fw-bold mb-1">{{ $dept->areanom }}</h5>

                            <span class="badge badge-soft mb-2">
                                {{ $dept->tipo }}
                            </span>

                            <p class="text-muted mb-1">
                                <i class="fas fa-sitemap me-1"></i>
                                {{ $dept->unit->uninom ?? '-' }}
                            </p>

                            <p class="text-muted mb-0 small">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $dept->address->calle ?? '-' }},
                                {{ $dept->address->colonia ?? '-' }},
                                CP {{ $dept->address->cp ?? '-' }}
                            </p>
                        </div>

                        <div class="mt-3 d-flex justify-content-end gap-2 flex-wrap">
                            <a href="{{ route('departments.edit', $dept->id) }}"
                               class="btn btn-guinda btn-sm px-3">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>

                            <button type="button"
                                    class="btn btn-outline-danger btn-sm px-3"
                                    data-confirm-delete
                                    data-name="{{ $dept->areanom }}"
                                    data-text="¿Deseas eliminar este departamento?"
                                    data-action="{{ route('departments.destroy', $dept->id) }}">
                                <i class="fas fa-trash me-1"></i> Eliminar
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="fs-5 text-muted mb-0">No se encontraron departamentos</p>
            </div>
        @endforelse
    </div>

    {{-- ================= PAGINACIÓN ================= --}}
    @if($departments instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="d-flex justify-content-center mt-4" id="paginationContainer">
            {{ $departments->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    @endif
    @endif

    {{-- ================= PANEL ORGANIZACIONAL ================= --}}
    @if(hasrole(['super_admin']))
        {{-- PANEL SIN CAMBIOS FUNCIONALES, SOLO ESTÉTICOS --}}
        {{-- (Se conserva tu estructura y JS) --}}
        <div class="card shadow-lg rounded-4 border-0 mb-4 org-panel">
            <div class="card-header org-header" data-bs-toggle="collapse" data-bs-target="#orgMini">
                <h5 class="fw-bold text-guinda mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-sitemap"></i> Organización (Resumen)
                </h5>
            </div>

            <div id="orgMini" class="collapse show">

                <div class="card-body org-body">

                    {{-- Filtros por niveles (Región / Unidad / Departamento) --}}
                    <div class="d-flex justify-content-start mb-3">
                        
                        <div class="d-flex gap-2 w-100" style="max-width: 80%;">

                            <select id="filterRegion" class="form-select org-filter-select">
                                <option value="">Todas las regiones</option>
                                @foreach($organizacion as $region)
                                    <option value="{{ strtolower($region->regnom) }}">{{ $region->regnom }}</option>
                                @endforeach
                            </select>

                            <select id="filterUnit" class="form-select org-filter-select">
                                <option value="">Todas las unidades</option>
                                @foreach($organizacion as $region)
                                    @foreach($region->units as $unit)
                                        <option value="{{ strtolower($unit->uninom) }}">{{ $unit->uninom }}</option>
                                    @endforeach
                                @endforeach
                            </select>

                            <select id="filterDept" class="form-select org-filter-select">
                                <option value="">Todos los deptos</option>
                                @foreach($organizacion as $region)
                                    @foreach($region->units as $unit)
                                        @foreach($unit->departments as $dept)
                                            <option value="{{ strtolower($dept->areanom) }}">{{ $dept->areanom }}</option>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </select>

                            <button type="button" class="btn btn-secondary small-btn" onclick="resetFilters()">
                                <i class="fas fa-eraser me-1"></i>
                            </button>

                        </div>
                    </div>

                    {{-- ÁRBOL ORGANIZACIONAL --}}
                    <div class="org-tree mt-3">

                        @foreach($organizacion as $region)
                            <div class="org-region" data-region="{{ strtolower($region->regnom) }}">

                                {{-- Región --}}
                                <div class="org-title-region region-toggle d-flex justify-content-between align-items-center"
                                    data-name="{{ strtolower($region->regnom) }}">

                                    <div>
                                        🗺️ <strong>{{ $region->regcve }} – {{ $region->regnom }}</strong>
                                    </div>

                                    <span class="region-arrow text-muted">▶</span>
                                </div>

                                {{-- Contenido de región --}}
                                <div class="region-content d-none">

                                    @foreach($region->units as $unit)
                                        <div class="org-unit" data-unit="{{ strtolower($unit->uninom) }}">

                                            {{-- Unidad --}}
                                            <div class="org-unit-header org-item" data-name="{{ strtolower($unit->uninom) }}">
                                                <div class="d-flex align-items-center gap-2">
                                                    📍 <span class="fw-semibold">{{ $unit->unicve }} – {{ $unit->uninom }}</span>
                                                    <small class="text-muted">({{ $unit->departments->count() }} dep)</small>
                                                </div>
                                                <span class="unit-mini-toggle">▶</span>
                                            </div>

                                            {{-- Departamentos --}}
                                            <div class="org-dept-list d-none">

                                                @foreach($unit->departments as $dept)
                                                    <div class="org-dept"
                                                        data-dept="{{ strtolower($dept->areanom) }}">
                                                        <strong>{{ $dept->areacve }}</strong> – {{ $dept->areanom }}

                                                        @if($dept->address)
                                                            <div class="text-muted small mt-1">
                                                                💼 {{ $dept->address->calle ?? '-' }},
                                                                CP {{ $dept->address->cp ?? '-' }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@stop

@section('css')
<style>
:root {
    --guinda: #611232;
    --guinda-dark: #4b0f27;
}

/* Colores */
.text-guinda { color: var(--guinda) !important; }
.bg-guinda { background: var(--guinda) !important; }

/* Título */
.view-title {
    font-size: clamp(1.5rem, 2vw, 2rem);
}

/* Cards */
.shadow-soft {
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
}
.dept-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(97,18,50,.2);
}

/* Badge */
.badge-soft {
    background: #f3e7eb;
    color: var(--guinda);
    border-radius: .5rem;
    padding: .25rem .6rem;
    font-size: .75rem;
}

/* Botones */
.btn-guinda {
    background: var(--guinda);
    color: #fff;
    border-radius: .55rem;
    border: 1px solid var(--guinda);
}
.btn-guinda:hover {
    background: var(--guinda-dark);
}

.btn-guinda-outline {
    background: #fff;
    color: var(--guinda);
    border: 1px solid var(--guinda);
    border-radius: .55rem;
}
.btn-guinda-outline:hover {
    background: var(--guinda-dark);
    color: #fff;
}

/* Filtros */
#filterForm .form-control,
#filterForm .form-select {
    border-radius: 50px;
}

/* Responsive */
@media (max-width: 576px) {
    .btn-guinda,
    .btn-guinda-outline {
        width: 100%;
    }
}

/* --- Tarjetas base --- */
.card { 
    transition:none !important; 
    margin-top:1px; 
    box-shadow:0 4px 10px rgba(0,0,0,0.08); 
}

/* --- Scroll horizontal (si se usa organización horizontal) --- */
#orgHorizontal::-webkit-scrollbar { height: 8px; }
#orgHorizontal::-webkit-scrollbar-thumb {
    background: #bbb;
    border-radius: 5px;
}

.unit-toggle { cursor:pointer; }
.org-column { background:#fff; transition:.2s; }
.org-column:hover { transform: translateY(-2px); }

/* --- Scroll vertical (panel mini) --- */
#orgMini::-webkit-scrollbar { width: 6px; }
#orgMini::-webkit-scrollbar-thumb {
    background: #aaa;
    border-radius: 3px;
}

.unit-mini-toggle { user-select: none; }

/* --- Región --- */
.region-toggle {
    cursor: pointer;
    padding: .5rem;
    border-radius: .5rem;
    transition: .2s;
}
.region-toggle:hover { background: #f7ecef; }

.region-arrow {
    font-size: 1.2rem;
    user-select: none;
}

.region-content { margin-top: .5rem; }

/* --- Inputs pequeños --- */
.small-input {
    border-radius: 20px;
    padding: .35rem .8rem;
    font-size: .85rem;
    height: 35px;
}

.small-btn {
    font-size: .8rem;
    height: 35px;
}

/* --- Panel Organización --- */
.org-panel { border: 1px solid #eee; }
.org-header { 
    background: #faf6f7; 
    cursor: pointer; 
    padding: 1rem !important; 
}
.org-body { max-height: 850px; overflow-y: auto; }

/* --- Árbol Organización --- */
.org-tree { padding-left: 5px; }

.org-region {
    background:#fff;
    border-radius:.6rem;
    padding:1rem;
    border:1px solid #e8dbe0;
    margin-bottom:1rem;
}

/* --- Unidades --- */
.org-unit { 
    border-left:3px solid #e2c9cf; 
    padding-left:.8rem; 
    margin-bottom:1rem; 
}

.org-unit-header {
    background:#fafafa;
    padding:.45rem .7rem;
    border-radius:.4rem;
    cursor:pointer;
    display:flex;
    justify-content:space-between;
    transition:.2s;
}
.org-unit-header:hover { background:#f3eded; }

.unit-mini-toggle {
    font-size:1.1rem;
    cursor: pointer;
}

/* --- Departamentos --- */
.org-dept {
    padding:.35rem .6rem;
    margin-bottom:.35rem;
    border:1px solid #eee;
    border-radius:.4rem;
    background:#fff;
    transition:.2s;
}
.org-dept:hover {
    background:#f9f3f5;
    border-color:#d8b9c5;
}

</style>
@stop

@section('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ================================
       ELEMENTOS GENERALES
    ==================================*/

    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');
    const departmentsList = document.getElementById('departmentsList');
    const paginationContainer = document.getElementById('paginationContainer');

    const filterRegion = document.getElementById("filterRegion");
    const filterUnit   = document.getElementById("filterUnit");
    const filterDept   = document.getElementById("filterDept");

    let timer;


    /* ================================
       FUNCIÓN AJAX PRINCIPAL
    ==================================*/

    function fetchDepartments(url = null) {

        if (!url) {
            url = filterForm.action + "?" + new URLSearchParams(new FormData(filterForm)).toString();
        }

        fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, "text/html");

                const newList = doc.getElementById('departmentsList');
                if (newList) departmentsList.innerHTML = newList.innerHTML;

                const newPagination = doc.getElementById('paginationContainer');
                if (newPagination) paginationContainer.innerHTML = newPagination.innerHTML;

                window.history.pushState({}, '', url);
            })
            .catch(err => console.error(err));
    }


    /* ================================
       BÚSQUEDA EN TIEMPO REAL
    ==================================*/

    if (searchInput) {
        searchInput.addEventListener('keyup', () => {
            clearTimeout(timer);
            timer = setTimeout(() => fetchDepartments(), 500);
        });
    }


    /* ================================
       FORMULARIO DE FILTRO
    ==================================*/

    if (filterForm) {
        filterForm.addEventListener('submit', e => {
            e.preventDefault();
            fetchDepartments();
        });
    }


    /* ================================
       CLICK DELEGADO GLOBAL
       - Paginación
       - Toggle región
       - Toggle unidad
    ==================================*/

    document.addEventListener("click", function(e) {

        /* ---- PAGINACIÓN ---- */
        const link = e.target.closest('.pagination a');
        if (link) {
            e.preventDefault();
            return fetchDepartments(link.href);
        }

        /* ===================================================
        TOGGLE REGIÓN (mismo comportamiento que UNIDAD)
        =================================================== */
        const regionToggle = e.target.closest(".region-toggle");
        if (regionToggle) {
            const content = regionToggle.nextElementSibling;
            const arrow   = regionToggle.querySelector(".region-arrow");

            content.classList.toggle("d-none");
            arrow.textContent = content.classList.contains("d-none") ? "▶" : "▼";

            return;
        }

        /* ===================================================
        TOGGLE UNIDAD (MISMO estilo que región)
        =================================================== */
        const unitHeader = e.target.closest(".org-unit-header");
        if (unitHeader) {

            const list  = unitHeader.nextElementSibling; // Lista de departamentos
            const arrow = unitHeader.querySelector(".unit-mini-toggle");

            list.classList.toggle("d-none");
            arrow.textContent = list.classList.contains("d-none") ? "▶" : "▼";

            return;
        }

    });


    /* ===================================================
       FILTROS DINÁMICOS REGION → UNIDAD → DEPARTAMENTO
    =================================================== */

    // Capturar selects
    const regionSelect = filterRegion;
    const unitSelect   = filterUnit;
    const deptSelect   = filterDept;

    // Cargar TODA la estructura organizacional desde Blade
    const allUnits = @json($estructura);

    /* --- Función: Recargar unidades según región --- */
    function updateUnits() {
        const regionVal = regionSelect.value.toLowerCase();

        unitSelect.innerHTML = `<option value="">Todas las unidades</option>`;
        deptSelect.innerHTML = `<option value="">Todos los deptos</option>`;

        allUnits.forEach(r => {
            if (!regionVal || r.region === regionVal) {
                r.units.forEach(u => {
                    unitSelect.innerHTML += `<option value="${u.name}">${u.label}</option>`;
                });
            }
        });

        filterTree();
    }

    /* --- Función: Recargar departamentos según unidad --- */
    function updateDepartments() {
        const unitVal = unitSelect.value.toLowerCase();

        deptSelect.innerHTML = `<option value="">Todos los deptos</option>`;

        allUnits.forEach(r => {
            r.units.forEach(u => {
                if (!unitVal || u.name === unitVal) {
                    u.departments.forEach(d => {
                        deptSelect.innerHTML += `<option value="${d.name}">${d.label}</option>`;
                    });
                }
            });
        });

        filterTree();
    }

    /* ===================================================
       BOTÓN LIMPIAR (RESET REAL)
    =================================================== */

    window.resetFilters = function() {
        regionSelect.value = "";
        updateUnits();
        unitSelect.value = "";
        updateDepartments();
        deptSelect.value = "";

        document.querySelectorAll(".org-region").forEach(r => r.style.display = "block");
        document.querySelectorAll(".org-unit").forEach(u => u.style.display = "block");
        document.querySelectorAll(".org-dept").forEach(d => d.style.display = "block");
    };


    /* ================================
       FILTRO POR NIVELES (EXISTENTE)
    ==================================*/

    function filterTree() {

        const regionVal = filterRegion?.value.toLowerCase() || "";
        const unitVal   = filterUnit?.value.toLowerCase() || "";
        const deptVal   = filterDept?.value.toLowerCase() || "";

        document.querySelectorAll(".org-region").forEach(region => {

            const regionName = region.dataset.region;
            const showRegion = !regionVal || regionName.includes(regionVal);
            region.style.display = showRegion ? "block" : "none";

            region.querySelectorAll(".org-unit").forEach(unit => {

                const unitName = unit.dataset.unit;
                const showUnit = (!unitVal || unitName.includes(unitVal)) && showRegion;
                unit.style.display = showUnit ? "block" : "none";

                unit.querySelectorAll(".org-dept").forEach(dept => {

                    const deptName = dept.dataset.dept;
                    const showDept = (!deptVal || deptName.includes(deptVal)) && showUnit;
                    dept.style.display = showDept ? "block" : "none";
                });
            });
        });
    }

    if (filterRegion) filterRegion.addEventListener("change", updateUnits);
    if (filterUnit)   filterUnit.addEventListener("change", updateDepartments);

    if (filterRegion) filterRegion.addEventListener("input", filterTree);
    if (filterUnit)   filterUnit.addEventListener("input", filterTree);
    if (filterDept)   filterDept.addEventListener("input", filterTree);


    /* ================================
       POPSTATE (navegación de historial)
    ==================================*/

    window.addEventListener('popstate', () => fetchDepartments(window.location.href));

});

</script>
@stop

