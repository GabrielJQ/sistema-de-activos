@extends('layouts.admin')

@section('title', 'Técnicos por Unidad')

@section('content')
    <div class="container-fluid">

        {{-- Mensajes --}}


        {{-- Título --}}
        <h1 class="view-title mb-2 text-guinda fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-tools"></i> Técnicos por Unidad
        </h1>

        <p class="text-muted mb-4">
            @if(hasRole('super_admin'))
                Visualización global de técnicos por unidad. Puedes asignar o modificar técnicos en cualquier unidad.
            @else
                Visualización del técnico asignado a tu unidad. Puedes asignar o modificarlo.
            @endif
        </p>

        {{-- LIVEWIRE COMPONENT --}}
        @livewire('unit-technician-index')

    </div>
@endsection

@section('css')
    <style>
        /* COLOR CORPORATIVO */
        .text-guinda {
            color: #611232 !important;
        }

        .bg-guinda {
            background-color: #611232 !important;
        }

        /* ALERT SUAVE */
        .shadow-soft {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }

        /* BOTONES (mantienen color en hover) */
        .btn-actions-new,
        .btn-guinda-outline {
            border-radius: .55rem;
            font-weight: 500;
            background-color: #611232 !important;
            color: #fff !important;
            border: 1px solid #611232 !important;
            transition: .25s ease-in-out;
        }

        /* Hover: permanece guinda, solo se oscurece ligeramente */
        .btn-actions-new:hover,
        .btn-guinda-outline:hover {
            background-color: #4b0f27 !important;
            border-color: #4b0f27 !important;
            color: #fff !important;
        }

        /* TABLA */
        .modern-table th,
        .modern-table td {
            padding: .85rem 1rem !important;
            font-size: .9rem;
        }

        /* Hover tabla: mantiene texto blanco para contraste */
        .modern-table tbody tr:hover {
            background-color: #611232 !important;
            color: #fff !important;
        }



        /* BADGE */
        .badge-soft {
            border-radius: .45rem;
            font-size: .8rem;
        }

        /* BOTÓN ACCIÓN (mantiene color en hover) */
        .modern-btn {
            border-radius: .45rem !important;
            transition: .25s ease;
            background-color: #611232 !important;
            border-color: #611232 !important;
            color: #fff !important;
        }

        .modern-btn:hover {
            background-color: #4b0f27 !important;
            border-color: #4b0f27 !important;
            color: #fff !important;
            transform: translateY(-1px);
        }

        /* MODAL */
        .modal-body-soft {
            background: #faf7f9 !important;
        }

        .modern-select {
            border-radius: .45rem !important;
        }

        /* TARJETA */
        .table-card {
            border-radius: 1rem !important;
        }

        /* RESPONSIVE */
        @media (max-width: 576px) {

            .btn-actions-new,
            .btn-guinda-outline {
                width: 100%;
            }
        }
    </style>
@endsection

@push('js')
    <script>
        // Auto ocultar la alerta de éxito de session('success') si hubiera un backend redirect
        $(document).ready(function () {
            setTimeout(() => {
                $('.alert-success, .alert-danger').fadeOut(500);
            }, 5000);
        });
    </script>
@endpush