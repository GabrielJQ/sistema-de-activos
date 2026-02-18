@extends('layouts.admin')

@section('title', 'Activos del Tag ' . $tag)

@section('content')
<div class="container-fluid py-3">

    {{-- Encabezado --}}
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="mb-1 fw-bold text-guinda">
                <i class="fas fa-user-circle me-2"></i>{{ $employee->full_name }}
            </h1>
            <div class="text-muted small">
                Consulta de activos por TAG
                <span class="mx-2 d-none d-md-inline">•</span>
                <span class="badge bg-guinda px-3 py-2 rounded-pill">
                    <i class="fas fa-tag me-1"></i> {{ $tag }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- Card: Información del empleado --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 rounded-top-4">
                    <h6 class="mb-0 fw-bold text-uppercase text-muted">
                        <i class="fas fa-id-badge me-2 text-guinda"></i>Información del empleado
                    </h6>
                </div>

                <div class="card-body">
                    <div class="d-flex flex-column gap-3">

                        <div class="p-3 rounded-4 bg-light employee-info-block">
                            <div class="small text-muted mb-1">Expediente</div>
                            <div class="fw-semibold">
                                <i class="fas fa-hashtag me-2 text-muted"></i>{{ $employee->expediente }}
                            </div>
                        </div>

                        <div class="p-3 rounded-4 bg-light employee-info-block">
                            <div class="small text-muted mb-1">Puesto</div>
                            <div class="fw-semibold">
                                <i class="fas fa-briefcase me-2 text-muted"></i>{{ $employee->puesto }}
                            </div>
                        </div>

                        <div class="p-3 rounded-4 bg-light employee-info-block">
                            <div class="small text-muted mb-1">Departamento</div>
                            <div class="fw-semibold">
                                <i class="fas fa-building me-2 text-muted"></i>{{ $employee->department->areanom ?? 'N/A' }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Lista de activos --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 rounded-top-4">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="fas fa-laptop me-2 text-guinda"></i>Activos con TAG {{ $tag }}
                            </h5>
                            <div class="text-muted small">
                                Series y tipo de equipo asociados al TAG.
                            </div>
                        </div>

                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                            <i class="fas fa-list-ul me-1 text-muted"></i>
                            Total: <span class="fw-semibold">{{ $assets->count() }}</span>
                        </span>
                    </div>
                </div>

                <div class="card-body pt-0">

                    {{-- Estado vacío --}}
                    @if($assets->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                            <div class="fw-semibold">No hay activos asociados</div>
                            <div class="text-muted small">Revisa el TAG o las asignaciones.</div>
                        </div>
                    @else
                        <ul class="list-group list-group-flush mt-3">
                            @foreach($assets as $asset)
                                <li class="list-group-item py-3 d-flex justify-content-between gap-3">

                                    <div class="d-flex gap-3">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-light border"
                                              style="width:40px;height:40px;">
                                            <i class="fas fa-microchip text-muted"></i>
                                        </span>

                                        <div>
                                            <div class="fw-semibold">{{ $asset->serie }}</div>
                                            <div class="text-muted small">
                                                {{ $asset->deviceType->equipo ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>

                                </li>
                            @endforeach
                        </ul>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>
@stop

@section('css')
{{-- CSS RESPONSIVE --}}
    <style>
        .text-guinda { color: #611232; }
        .bg-guinda { background-color: #611232 !important; }

        .bg-guinda-subtle {
            background: rgba(97, 18, 50, 0.08);
        }

        .border-guinda {
            border-color: rgba(97, 18, 50, 0.35) !important;
        }

        /* Ajustes móviles */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.3rem;
            }

            .card-header {
                padding: 1rem !important;
            }

            .card-body {
                padding: 1rem !important;
            }

            .employee-info-block {
                padding: .75rem !important;
            }

            .list-group-item {
                flex-direction: column;
                align-items: flex-start !important;
                gap: .75rem;
            }

            .list-group-item .text-end {
                width: 100%;
                text-align: left !important;
            }

            .badge {
                font-size: .75rem;
            }
        }
    </style>
@endsection