@extends('layouts.admin')

@section('title', 'Generación de QR')

@section('content')
<div class="container-fluid py-3">

    {{-- Encabezado --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
        <div>
            <h1 class="fw-bold text-guinda mb-1">
                <i class="fas fa-qrcode me-2"></i>Códigos QR para Impresión
            </h1>
            <div class="text-muted small">
                Generación y descarga de códigos QR por activo y por TAG
            </div>
             <div class="d-flex justify-content-start gap-2 flex-wrap me-auto">
                <button class="btn btn-guinda-outline px-4 py-2" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimir QR
                </button>

                <a href="{{ route('asset_assignments.index') }}" class="btn btn-secondary px-4 py-2">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

    </div>

    <style>
        /* === Cards QR === */
        .qr-card {
            width: 320px;
            border: 1px solid #e1e1e1;
            padding: 10px;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,.05);
            transition: transform .2s ease;
        }

        .qr-card:hover {
            transform: translateY(-2px);
        }

        .qr-pair {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 18px;
            margin: 6px 0;
        }

        /* === Colores guinda === */
        .text-guinda{color:#611232}
        .bg-guinda{ background-color: #611232 !important; }

        .btn-guinda-outline {
            border: 1px solid #611232;
            color: #611232;
            background: #fff;
            border-radius: 0.6rem;
            transition: all .25s ease;
        }
        .btn-guinda-outline:hover {
            background-color: #611232;
            color: #fff;
        }

        .btn-secondary {
            border-radius: 0.6rem;
        }

        /* === Encabezado empleado === */
        .employee-header {
            background: #fafafa;
            border-left: 5px solid #611232;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        /* === Impresión === */
        @media print {
            /* Ocultar botones y headers */
            .btn,
            .employee-header,
            .view-title,
            h1 {
                display: none !important;
            }

            /* Contenedor de QR → GRID */
            .d-flex.flex-wrap {
                display: grid !important;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 8px;
                align-items: start;
                justify-items: center;
            }

            /* Card QR */
            .qr-card {
                width: 100%;
                max-width: 300px;
                page-break-inside: avoid;
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #000;
                margin: 0;
                padding: 8px;
            }

            /* Evitar cortes feos */
            hr {
                display: none;
            }

            /* Ajustes generales */
            body {
                margin: 0;
                padding: 0;
            }
        }

    </style>

    {{-- Loop principal --}}
    @foreach($groupedAssets as $employeeId => $assetsGroup)
        @php
            $employee = $assetsGroup->first()->currentAssignment->employee;
            $assetsByTag = $assetsGroup->groupBy('tag');
        @endphp

        {{-- Encabezado empleado --}}
        <div class="employee-header">
            <div class="fw-semibold">
                <i class="fas fa-user me-1 text-guinda"></i>
                {{ $employee->full_name }}
            </div>
            <div class="small text-muted">
                <i class="fas fa-building me-1"></i>
                {{ $employee->department->areanom ?? 'Sin departamento' }}
            </div>
        </div>

        <div class="d-flex flex-wrap mb-5 gap-2">

            @foreach($assetsByTag as $tag => $assets)
                @foreach($assets as $asset)
                    <div class="qr-card d-flex flex-column align-items-center text-center">

                        {{-- Título --}}
                        <div class="mb-2 small">
                            <strong>ALIMENTACIÓN PARA EL BIENESTAR</strong><br>
                            <span class="badge px-3 py-2 rounded-pill mt-1" style="color: #000000; font-size: 0.9rem; background-color: #e1e1e1;">
                                <i class="fas fa-tag me-1"></i>{{ strtoupper($tag) }} - {{ $asset->deviceType->equipo ?? 'N/A' }}
                            </span>
                        </div>

                        {{-- QR --}}
                        <div class="qr-pair">
                            {{-- QR por TAG --}}
                            <div>
                                {!! QrCode::size(100)->generate(
                                    route('assets.qrTagView', ['tag' => $tag])
                                ) !!}
                            </div>

                            {{-- QR individual --}}
                            <div>
                                {!! QrCode::size(100)->generate(
                                    "ALIMENTACION PARA EL BIENESTAR\n".
                                    "TAG: {$asset->tag}\n".
                                    "Tipo: {$asset->deviceType->equipo}\n".
                                    "Marca: {$asset->marca}\n".
                                    "Modelo: {$asset->modelo}\n".
                                    "Serie: {$asset->serie}"
                                ) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach

        </div>

        <hr class="my-4">
    @endforeach
</div>
@stop
