@extends('layouts.admin')

@section('title', 'Asignaciones de Activos')

@section('content')
    <div class="container py-4">

        {{-- ENCABEZADO --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="fw-bold mb-1 text-guinda d-flex align-items-center gap-2">
                    <span class="icon-circle bg-guinda text-white">
                        <i class="fas fa-laptop"></i>
                    </span>
                    Asignaciones de {{ $employee->full_name }}
                </h1>
                <small class="text-muted">Activos actualmente asignados al empleado</small>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('asset_assignments.index') }}" class="btn btn-secondary btn-sm px-3">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>

                @if(hasRole(['super_admin', 'admin', 'collaborator']))
                    <a href="{{ route('asset_assignments.create', ['employee_id' => $employee->id]) }}"
                        class="btn btn-actions-new btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Nueva asignación
                    </a>
                @endif

                @if(hasRole(['super_admin', 'admin']))
                    <button class="btn btn-info btn-sm px-3" data-bs-toggle="modal" data-bs-target="#printModal">
                        <i class="fas fa-print me-1"></i> Imprimir resguardo
                    </button>
                @endif
            </div>
        </div>

        {{-- AGRUPACIÓN --}}
        @php
            // $groupedAssignments viene del controlador
            $groupCount = $groupedAssignments->count();
            $isSidebar = $groupCount > 10;

            // Definición global de equipos principales para ordenamiento y selección
            $mainDeviceTypes = [
                'Equipo All In One',
                'Equipo Escritorio',
                'Escritorio Avanzada',
                'Laptop de Avanzada',
                'Laptop de Intermedia',
            ];
        @endphp

        {{-- NAVEGACIÓN + CONTENIDO: Sidebar si >10, Tabs si <=10 --}} {{-- Mensaje dinámico (estilo Import) --}} <div
            class="mb-3">
            <div id="fileMessage" style="display:none;" class="alert mt-1"></div>
    </div>

    <form action="{{ route('asset_assignments.bulkReturn') }}" method="POST" id="bulkForm">
        @csrf

        @if($isSidebar)

            {{-- MISMA FILA: izquierda lista, derecha cards --}}
            <div class="row g-3 align-items-start">

                {{-- Sidebar --}}
                <div class="col-12 col-lg-2">
                    <div class="group-sidebar-card h-100">
                        <div class="group-sidebar-header">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="groupSearch" class="form-control" placeholder="Buscar por TAG...">
                            </div>
                            <small class="text-muted d-block mt-2">{{ $groupCount }} equipos de computo</small>
                        </div>

                        <div class="list-group group-sidebar-list" id="groupList" role="tablist">
                            @php $i = 0; @endphp
                            @foreach($groupedAssignments as $tag => $assignments)
                                <button type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center group-item {{ $i === 0 ? 'active' : '' }}"
                                    data-bs-toggle="tab" data-bs-target="#content-{{ $i }}" role="tab"
                                    aria-controls="content-{{ $i }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                                    <span class="text-truncate me-2">{{ $tag }}</span>
                                    <span class="badge group-badge">{{ $assignments->count() }}</span>
                                </button>
                                @php $i++; @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
                {{-- Contenido (cards) --}}
                <div class="col-12 col-lg-9">
                    {{-- Cards normales (<=10) --}} <div class="tab-content">
                        @php $i = 0; @endphp
                        @foreach($groupedAssignments as $tag => $assignments)

                            @php
                                // Orden estable: principal primero usando la lista global
                                $sortedAssignments = $assignments
                                    ->sortBy(function ($x) use ($mainDeviceTypes) {
                                        $type = optional($x->asset->deviceType)->equipo;
                                        return in_array($type, $mainDeviceTypes) ? 0 : 1;
                                    })
                                    ->values();

                                $groupIds = $sortedAssignments->pluck('id')->implode(',');

                                $mainAssignment = $sortedAssignments->first();
                                $supplierName = optional(optional($mainAssignment->asset)->supplier)->prvnombre ?? '';
                            @endphp

                            <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="content-{{ $i }}">
                                <div class="row g-3">

                                    @foreach($sortedAssignments as $index => $a)
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <div class="card assignment-card shadow-soft p-3
                                                                {{ $a->assignment_type === 'temporal' ? 'temporal-card' : 'permanente-card' }}"
                                                data-tag="{{ optional($a->asset)->tag ?: 'Sin tag' }}">

                                                <div class="form-check position-absolute top-0 end-0 m-2">
                                                    @if(hasRole(['super_admin', 'admin', 'collaborator']))
                                                        @if($index === 0)
                                                            {{--Principal REAL (equipo principal) --}}
                                                            <input class="form-check-input principal-checkbox" type="checkbox"
                                                                name="assignments[]" value="{{ $groupIds }}"
                                                                data-supplier="{{ $supplierName }}">
                                                        @else
                                                            <input class="form-check-input row-checkbox" type="checkbox" disabled>
                                                        @endif
                                                    @endif
                                                </div>

                                                <div class="card-body p-2 small">
                                                    <p><strong>TAG:</strong> {{ optional($a->asset)->tag ?: '-' }}</p>
                                                    <p><strong>Tipo:</strong> {{ optional($a->asset->deviceType)->equipo ?? '-' }}</p>
                                                    <p><strong>Serie:</strong> {{ optional($a->asset)->serie ?? '-' }}</p>
                                                    <p><strong>Asignado:</strong> {{ $a->assigned_at->format('d/m/Y') }}</p>

                                                    <p>
                                                        <strong>Asignación:</strong> {{ ucfirst($a->assignment_type) }}
                                                        @if($a->assignment_type === 'temporal')
                                                            <span class="badge bg-warning text-dark ms-1">Temporal</span>
                                                        @endif
                                                    </p>

                                                    @if($a->assignment_type === 'temporal' && $a->temporaryAssignment?->temporary_holder)
                                                        <p class="text-warning fw-semibold">
                                                            <i class="fas fa-user-clock me-1"></i>
                                                            {{ $a->temporaryAssignment->temporary_holder }}
                                                        </p>
                                                    @endif

                                                    @if($a->observations)
                                                        <p class="text-muted"><strong>Obs:</strong> {{ $a->observations }}</p>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>

                            @php $i++; @endphp
                        @endforeach
                </div>

            </div>



            </div>

        @else

            {{-- Tabs arriba (<=10) --}} <ul class="nav nav-tabs modern-tabs mb-4" role="tablist">
                @php $i = 0; @endphp
                @foreach($groupedAssignments as $tag => $assignments)
                    <li class="nav-item">
                        <button type="button" class="nav-link custom-tab {{ $i === 0 ? 'active' : '' }}" data-bs-toggle="tab"
                            data-bs-target="#content-{{ $i }}" role="tab" aria-controls="content-{{ $i }}"
                            aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                            {{ $tag }}
                            <span class="badge bg-secondary ms-1">{{ $assignments->count() }}</span>
                        </button>
                    </li>
                    @php $i++; @endphp
                @endforeach
                </ul>

                {{-- Cards normales --}}
                <div class="tab-content">
                    @php $i = 0; @endphp
                    @foreach($groupedAssignments as $tag => $assignments)
                        <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="content-{{ $i }}">
                            <div class="d-flex flex-wrap gap-3">

                                @php
                                    // Aplicar el mismo ordenamiento que en la vista sidebar
                                    $sortedAssignments = $assignments->sortBy(function ($x) use ($mainDeviceTypes) {
                                        $type = optional($x->asset->deviceType)->equipo;
                                        return in_array($type, $mainDeviceTypes) ? 0 : 1;
                                    })->values();

                                    $groupIds = $sortedAssignments->pluck('id')->implode(',');
                                @endphp

                                @foreach($sortedAssignments as $index => $a)
                                    <div class="card assignment-card shadow-soft p-3
                                                    {{ $a->assignment_type === 'temporal' ? 'temporal-card' : 'permanente-card' }}"
                                        data-tag="{{ optional($a->asset)->tag ?: 'Sin tag' }}">

                                        <div class="form-check position-absolute top-0 end-0 m-2">
                                            @if(hasRole(['super_admin', 'admin', 'collaborator']))
                                                @if($index === 0)
                                                    <input class="form-check-input principal-checkbox" type="checkbox" name="assignments[]"
                                                        value="{{ $groupIds }}">
                                                @else
                                                    <input class="form-check-input row-checkbox" type="checkbox" disabled>
                                                @endif
                                            @endif
                                        </div>

                                        <div class="card-body p-2 small">
                                            <p><strong>TAG:</strong> {{ optional($a->asset)->tag ?: '-' }}</p>
                                            <p><strong>Tipo:</strong> {{ optional($a->asset->deviceType)->equipo ?? '-' }}</p>
                                            <p><strong>Serie:</strong> {{ optional($a->asset)->serie ?? '-' }}</p>
                                            <p><strong>Asignado:</strong> {{ $a->assigned_at->format('d/m/Y') }}</p>

                                            <p>
                                                <strong>Asignación:</strong> {{ ucfirst($a->assignment_type) }}
                                                @if($a->assignment_type === 'temporal')
                                                    <span class="badge bg-warning text-dark ms-1">Temporal</span>
                                                @endif
                                            </p>

                                            @if($a->assignment_type === 'temporal' && $a->temporaryAssignment?->temporary_holder)
                                                <p class="text-warning fw-semibold">
                                                    <i class="fas fa-user-clock me-1"></i>
                                                    {{ $a->temporaryAssignment->temporary_holder }}
                                                </p>
                                            @endif

                                            @if($a->observations)
                                                <p class="text-muted"><strong>Obs:</strong> {{ $a->observations }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        @php $i++; @endphp
                    @endforeach
                </div>

        @endif

            {{-- OBSERVACIONES --}}
            <div id="observacionesContainer" class="mt-4 p-4 rounded-4 bg-warning-light shadow-soft" style="display:none;">
                <button type="submit" class="btn btn-danger mb-3">
                    <i class="fas fa-trash me-1"></i> Quitar asignaciones
                </button>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Motivo de devolución</label>
                    <select id="observacion_tipo" name="observacion_tipo" class="form-select">
                        <option value="">-- Selecciona --</option>
                        <option value="Traslado a otra área o departamento">Traslado</option>
                        <option value="Sustitución por nuevo equipo o reasignación">Sustitución</option>
                        <option value="Fin de relación laboral o desvinculación">Fin de relación</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div id="observacion_personalizada_container" style="display:none;">
                    <label class="form-label fw-semibold">Especifica el motivo</label>
                    <textarea id="observacion_personalizada" name="observacion_personalizada" class="form-control"
                        rows="3"></textarea>
                </div>
            </div>
    </form>
    </div>

    {{-- MODAL IMPRIMIR --}}
    <div class="modal fade" id="printModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-guinda text-white">
                    <h5 class="modal-title">Seleccionar activos para imprimir resguardo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3" style="max-width:420px;">
                        <div class="input-group input-group-sm shadow-sm">
                            <span class="input-group-text bg-light text-muted">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="modalSearch" class="form-control"
                                placeholder="Buscar tag, equipo o serie...">
                        </div>
                    </div>

                    <form id="printForm" method="GET" action="{{ route('asset_assignments.generateReceipt') }}">
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                        <div class="form-check mb-3 d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" id="selectAllGroups">
                            <label class="form-check-label fw-bold mb-0" for="selectAllGroups">Seleccionar todos</label>
                        </div>

                        <table class="table table-bordered align-middle">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>Etiqueta / Grupo</th>
                                    <th>Activos Incluidos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedAssignments as $groupKey => $assignments)

                                    @php
                                        $ids = $assignments->pluck('id')->implode(',');

                                        // Buscar el principal dentro del grupo usando la lista global
                                        $mainAssignment = $assignments->first(function ($x) use ($mainDeviceTypes) {
                                            $type = optional(optional($x->asset)->deviceType)->equipo;
                                            return in_array($type, $mainDeviceTypes);
                                        }) ?? $assignments->first();

                                        $supplierName = optional(optional($mainAssignment->asset)->supplier)->prvnombre ?? '';
                                    @endphp

                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="resguardo-checkbox" name="assignments[]"
                                                value="{{ $ids }}" data-supplier="{{ $supplierName }}">
                                        </td>
                                        <td><strong>{{ $groupKey }}</strong></td>
                                        <td>
                                            <ul class="mb-0">
                                                @foreach($assignments as $a)
                                                    <li>{{ $a->asset->deviceType->equipo ?? 'N/A' }} - {{ $a->asset->serie ?? '' }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" form="printForm" id="btnGenerarPDF">Imprimir seleccionados</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    /* === Colores institucionales === */
    .text-guinda {
        color: #611232 !important;
    }

    .bg-guinda {
        background-color: #611232 !important;
    }

    /* === Botones === */
    .btn-actions-new {
        background-color: #611232;
        color: #fff;
        border-radius: .5rem;
        border: 1px solid #611232;
        transition: .3s ease;
    }

    .btn-actions-new:hover {
        background-color: #7b2046;
        color: #fff;
        box-shadow: 0 0 8px rgba(97, 18, 50, .3);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: #fff;
        border-radius: .5rem;
        transition: .3s ease;
    }

    .btn-secondary:hover {
        background-color: #808890;
        color: #fff;
        box-shadow: 0 0 8px rgba(108, 117, 125, .3);
    }

    .btn-info {
        background-color: #0d6efd;
        color: #fff;
        border-radius: .5rem;
        border: 1px solid #0d6efd;
        transition: .3s ease;
    }

    .btn-info:hover {
        background-color: #1d7dfc;
        color: #fff;
        box-shadow: 0 0 8px rgba(13, 110, 253, .3);
    }

    /* === Tarjetas === */
    .assignment-card {
        border-radius: .8rem;
        transition: all .25s ease;
    }

    .permanente-card {
        border: 1px solid rgba(97, 18, 50, .25);
    }

    .permanente-card:hover {
        border-color: #611232;
        box-shadow: 0 4px 10px rgba(97, 18, 50, .25);
        transform: translateY(-2px);
    }

    .temporal-card {
        border: 1px solid rgba(255, 193, 7, .4);
    }

    .temporal-card:hover {
        border-color: #ffc107;
        box-shadow: 0 4px 10px rgba(255, 193, 7, .3);
        transform: translateY(-2px);
    }

    /* === Misc === */
    .bg-warning-light {
        background-color: #fff3cd !important;
    }

    .row-checkbox {
        width: 1.6rem;
        height: 1.6rem;
    }

    .nav-tabs.overflow-auto {
        overflow-x: auto;
    }

    #searchInput {
        padding-left: 2rem;
    }

    /* === Checkboxs más grandes === */
    .principal-checkbox {
        width: 1.8rem;
        height: 1.8rem;
        cursor: pointer;
        border: 2px solid #611232;
    }

    .resguardo-checkbox {
        width: 1.7rem;
        height: 1.7rem;
        cursor: pointer;
    }

    /* === Modal === */
    .modal-header .btn-close {
        filter: invert(1);
    }

    #btnGenerarPDF {
        background-color: #611232;
        color: #fff;
        border-radius: .5rem;
        border: 1px solid #611232;
        padding: .5rem 1.2rem;
        font-weight: 600;
        transition: all .3s ease;
    }

    #btnGenerarPDF:hover {
        background-color: #7b2046;
        box-shadow: 0 4px 10px rgba(97, 18, 50, .3);
        transform: translateY(-2px);
    }

    /* Tabla */
    .table-bordered th,
    .table-bordered td {
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f8f0f3;
    }

    /* =========================
   TABS (<=10) - estilo chips con wrap (estado final)
   ========================= */
    .nav-tabs.modern-tabs {
        border: 0 !important;
        gap: .45rem;
        padding: .35rem .35rem .55rem;
        background: #fff;
        border-radius: .85rem;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .06);

        flex-wrap: wrap !important;
        overflow-x: hidden !important;
        overflow-y: auto;
        max-height: 92px;
        /* ~2 filas (ajustable) */
        align-content: flex-start;
    }

    .nav-tabs.modern-tabs .nav-item {
        flex: 0 0 auto;
    }

    .nav-tabs .nav-link.custom-tab {
        border: 1px solid rgba(97, 18, 50, .18) !important;
        border-radius: 999px !important;
        padding: .40rem .70rem !important;
        background: #fff !important;
        color: #611232 !important;
        font-weight: 600;
        font-size: .82rem;
        line-height: 1;
        white-space: nowrap;
        transition: all .18s ease;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
    }

    .nav-tabs .nav-link.custom-tab:hover {
        border-color: rgba(97, 18, 50, .45) !important;
        box-shadow: 0 8px 18px rgba(97, 18, 50, .10);
        transform: translateY(-1px);
    }

    .nav-tabs .nav-link.custom-tab.active {
        background: #611232 !important;
        color: #fff !important;
        border-color: #611232 !important;
        box-shadow: 0 10px 22px rgba(97, 18, 50, .22);
    }

    .nav-tabs .nav-link.custom-tab .badge {
        background: rgba(97, 18, 50, .12) !important;
        color: #611232 !important;
        border-radius: 999px;
        font-weight: 700;
        padding: .18rem .45rem;
    }

    .nav-tabs .nav-link.custom-tab.active .badge {
        background: rgba(255, 255, 255, .20) !important;
        color: #fff !important;
    }

    /* Scrollbar vertical de tabs (cuando supera 2 filas) */
    .nav-tabs.modern-tabs::-webkit-scrollbar {
        width: 7px;
    }

    .nav-tabs.modern-tabs::-webkit-scrollbar-track {
        background: rgba(97, 18, 50, .06);
        border-radius: 999px;
    }

    .nav-tabs.modern-tabs::-webkit-scrollbar-thumb {
        background: rgba(97, 18, 50, .22);
        border-radius: 999px;
    }

    .nav-tabs.modern-tabs::-webkit-scrollbar-thumb:hover {
        background: rgba(97, 18, 50, .35);
    }

    /* =========================
   SIDEBAR DE GRUPOS (>10)
   ========================= */
    .group-sidebar-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
        border: 1px solid rgba(97, 18, 50, .10);
        overflow: visible !important;
        position: relative;
    }

    .group-sidebar-header {
        padding: .85rem .85rem .65rem;
        border-bottom: 1px solid rgba(0, 0, 0, .06);

        position: sticky;
        top: 0;
        z-index: 10 !important;
        background: #fff;
    }

    .group-sidebar-header,
    .group-sidebar-header * {
        pointer-events: auto !important;
    }

    #groupSearch {
        position: relative;
        z-index: 9999 !important;
        pointer-events: auto !important;
    }

    .group-sidebar-list {
        max-height: 50vh;
        overflow: auto;
        position: relative;
        z-index: 1;
    }

    /* scrollbar sidebar */
    .group-sidebar-list::-webkit-scrollbar {
        width: 7px;
    }

    .group-sidebar-list::-webkit-scrollbar-track {
        background: rgba(97, 18, 50, .06);
        border-radius: 999px;
    }

    .group-sidebar-list::-webkit-scrollbar-thumb {
        background: rgba(97, 18, 50, .22);
        border-radius: 999px;
    }

    .group-sidebar-list::-webkit-scrollbar-thumb:hover {
        background: rgba(97, 18, 50, .35);
    }

    .group-item {
        border: 0;
        border-bottom: 1px solid rgba(0, 0, 0, .05);
        padding: .65rem .85rem;
        font-weight: 600;
        color: #611232;
    }

    .group-item:hover {
        background: rgba(97, 18, 50, .05);
    }

    .group-item.active {
        background: #611232 !important;
        color: #fff !important;
    }

    .group-badge {
        background: rgba(97, 18, 50, .12);
        color: #611232;
        font-weight: 800;
        border-radius: 999px;
        padding: .22rem .55rem;
    }

    .group-item.active .group-badge {
        background: rgba(255, 255, 255, .20);
        color: #fff;
    }

    /* Cards con tamaño estable dentro de flex-wrap */
    .assignment-card {
        flex: 0 0 320px;
        /* base fija */
        width: 320px;
        max-width: 100%;
        min-width: 300px;
        /* evita que se aplasten */
    }

    /* En pantallas chicas, que se vayan a 1 columna */
    @media (max-width: 576px) {
        .assignment-card {
            flex: 1 1 100%;
            width: 100%;
            min-width: 0;
        }
    }

    /* Opcional: textos largos no rompen el layout */
    .assignment-card p {
        margin-bottom: .35rem;
        word-break: break-word;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {

        // =====================================
        // GROUP SEARCH: NO BLOQUEA + SÍ FILTRA
        // =====================================
        (function () {
            const gs = document.getElementById('groupSearch');
            if (!gs) return;

            // 1) Blindaje: si el foco está en el input, nadie más se roba el teclado (CAPTURE)
            const protectKeys = (e) => {
                if (document.activeElement === gs) {
                    e.stopImmediatePropagation(); // <- clave
                    e.stopPropagation();
                    // NO preventDefault, para que sí escriba normal
                }
            };

            ['keydown', 'keypress', 'keyup'].forEach(evt => {
                document.addEventListener(evt, protectKeys, true); // capture = true
            });

            // 2) Evitar Enter (no submit)
            gs.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') e.preventDefault();
            });

            // 3) Filtrado real
            gs.addEventListener('input', () => {
                const q = gs.value.toLowerCase().trim();
                const items = document.querySelectorAll('#groupList .group-item');

                items.forEach(btn => {
                    const tagSpan = btn.querySelector('span.text-truncate');
                    const text = (tagSpan ? tagSpan.textContent : btn.textContent).toLowerCase().trim();
                    btn.style.display = text.includes(q) ? '' : 'none';
                });

                // Si el activo quedó oculto, activa el primer visible
                const activeVisible = document.querySelector('#groupList .group-item.active:not([style*="display: none"])');
                if (!activeVisible) {
                    const firstVisible = Array.from(items).find(b => b.style.display !== 'none');
                    if (firstVisible) firstVisible.click();
                }
            });
        })();

        $('#searchInput').on('input', function () {
            const search = $(this).val().toLowerCase().trim();

            $('.tab-pane').each(function () {
                let anyVisible = false;

                $(this).find('.assignment-card').each(function () {
                    const match = $(this).text().toLowerCase().includes(search);
                    $(this).toggle(match);
                    if (match) anyVisible = true;
                });


            });
        });

        $('#searchInput').on('keypress', e => e.which === 13 && e.preventDefault());


        $(document).on('change', '.principal-checkbox', function () {
            const tabPane = $(this).closest('.tab-pane');
            const isChecked = $(this).prop('checked');

            // Marcar/desmarcar los del grupo (solo visual)
            tabPane.find('.row-checkbox').prop('checked', isChecked);

            // Mostrar/ocultar observaciones
            const anyChecked = $('.principal-checkbox:checked').length > 0;
            if (anyChecked) $('#observacionesContainer').slideDown();
            else $('#observacionesContainer').slideUp();
        });

        $('#observacion_tipo').on('change', function () {
            if ($(this).val() === 'Otro') {
                $('#observacion_personalizada_container').slideDown();
            } else {
                $('#observacion_personalizada_container').slideUp();
                $('#observacion_personalizada').val('');
            }
        });

        $('#selectAllGroups').on('change', function () {
            $('.resguardo-checkbox').prop('checked', $(this).prop('checked'));
        });

        $(document).on('change', '.resguardo-checkbox', function () {
            const total = $('.resguardo-checkbox').length;
            const checked = $('.resguardo-checkbox:checked').length;
            $('#selectAllGroups').prop('checked', total === checked);
        });


        $('#btnGenerarPDF').on('click', function (e) {
            e.preventDefault();

            const seleccionados = $('.resguardo-checkbox:checked').toArray();

            if (seleccionados.length === 0) {
                alert('Por favor selecciona al menos un resguardo para imprimir.');
                return;
            }

            // Normaliza nombre (quita acentos básico en JS)
            const normalize = (s) => (s || '')
                .toString()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // quita acentos
                .toUpperCase()
                .replace(/\s+/g, ' ')
                .trim();

            const isAlimentacion = (name) => {
                const n = normalize(name);
                return n.includes('ALIMENTACION PARA EL BIENESTAR')
                    || n.includes('ALIMENTACION P EL BIENESTAR')
                    || n.includes('ALIMENTACION PARA BIENESTAR')
                    || n.includes('DICONSA');
            };

            seleccionados.forEach(cb => {
                const groupIds = cb.value;
                const supplier = cb.dataset.supplier || '';

                if (isAlimentacion(supplier)) {
                    const url = `{{ route('asset_assignments.generateReceipt') }}?assignments=${encodeURIComponent(groupIds)}`;
                    window.open(url, '_blank');
                } else {
                    const url = `{{ route('asset_assignments.prepareReceipt') }}?assignments=${encodeURIComponent(groupIds)}`;
                    window.open(url, '_blank');
                }
            });

            $('#printModal').modal('hide');
        });


        $('#bulkForm').on('submit', function (e) {
            e.preventDefault();

            const seleccionados = $('.principal-checkbox:checked').toArray();
            if (seleccionados.length === 0) return;

            // ids vienen como "1,2,3" por grupo; armamos uno solo
            const ids = seleccionados
                .map(el => (el.value || '').split(','))
                .flat()
                .map(x => x.trim())
                .filter(Boolean);

            // motivo
            const tipo = ($('#observacion_tipo').val() || '').trim();
            const custom = ($('#observacion_personalizada').val() || '').trim();
            const motivo = (tipo === 'Otro') ? custom : tipo;

            if (!motivo) {
                showMessage('Debes seleccionar un motivo de devolución (o especificar "Otro") para continuar.', 'danger');

                // opcional: enfocar el select para que el usuario lo vea rápido
                $('#observacion_tipo').focus();

                return;
            }

            // redirigir a PREPARE BAJA
            const url = `{{ route('asset_assignments.prepareBaja') }}`
                + `?employee_id={{ $employee->id }}`
                + `&ids=${encodeURIComponent(ids.join(','))}`
                + `&motivo=${encodeURIComponent(motivo)}`;

            window.location.href = url;
        });

        $('#modalSearch').on('input', function () {
            const s = $(this).val().toLowerCase().trim();
            $('#printModal tbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().includes(s));
            });
        });

        $('#tagFilter').on('change', function () {
            const selected = $(this).val();
            $('.assignment-card').each(function () {
                const tag = $(this).data('tag');
                $(this).toggle(!selected || tag === selected);
            });
        });

        let fileMsgTimer = null;

        function showMessage(message, type = 'info') {
            const fileMessage = $('#fileMessage');

            const closeBtn = `
            <button type="button" class="btn-close float-end" onclick="$('#fileMessage').hide();"></button>
        `;

            fileMessage
                .stop(true, true) // corta animaciones previas
                .removeClass('alert-success alert-danger alert-info alert-warning')
                .addClass('alert alert-' + type + ' alert-dismissible')
                .html(closeBtn + message)
                .show();

            // reinicia timer si ya había uno
            if (fileMsgTimer) clearTimeout(fileMsgTimer);

            // ocultar después de 5 segundos
            fileMsgTimer = setTimeout(() => {
                fileMessage.fadeOut(250);
            }, 5000);
        }

    });
</script>
@stop