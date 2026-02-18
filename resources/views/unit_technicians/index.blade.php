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

        {{-- GRID DE CARDS --}}
        <div class="row g-4">

            @foreach($regions as $region)
                @foreach($region->units as $unit)

                    @php
                        $technician = $unit->technician?->employee;
                    @endphp

                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">

                        <div class="card technician-card shadow-sm border-0 h-100">

                            {{-- HEADER --}}
                            <div class="card-header bg-guinda text-white rounded-top">
                                <div class="fw-semibold">{{ $unit->uninom }}</div>
                                <small class="opacity-75">{{ $region->regnom }}</small>
                            </div>

                            {{-- BODY --}}
                            <div class="card-body">

                                {{-- Técnico --}}
                                <div class="mb-2">
                                    <span class="fw-semibold">Técnico:</span><br>

                                    @if($technician)
                                        <i class="fas fa-user-check text-success me-1"></i>
                                        {{ $technician->full_name }}
                                    @else
                                        <span class="text-danger fw-semibold">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Sin técnico asignado
                                        </span>
                                    @endif
                                </div>

                                {{-- Estado --}}
                                <div>
                                    @if($technician)
                                        <span class="badge bg-success">Asignado</span>
                                    @else
                                        <span class="badge bg-danger">Pendiente</span>
                                    @endif
                                </div>
                            </div>

                            {{-- FOOTER --}}
                            <div class="card-footer bg-light border-0 text-end">
                                <button class="btn btn-sm btn-guinda-outline modern-btn" data-bs-toggle="modal"
                                    data-bs-target="#assignTechnicianModal" data-region="{{ $region->id }}"
                                    data-unit="{{ $unit->id }}" data-unit-name="{{ $unit->uninom }}">
                                    <i class="fas fa-edit me-1"></i>
                                    {{ $technician ? 'Cambiar' : 'Asignar' }}
                                </button>
                            </div>

                        </div>
                    </div>

                @endforeach
            @endforeach

        </div>

        {{-- MODAL --}}
        @include('unit_technicians.modal_assign')

    </div>
@endsection

@section('css')
    <style>
        /* === Select2 estilo institucional === */
        .select2-container--default .select2-selection--single {
            height: 42px;
            border-radius: .5rem;
            padding: .4rem .75rem;
            border: 1px solid #ced4da;
        }

        .select2-selection__rendered {
            line-height: 28px !important;
        }

        .select2-selection__arrow {
            height: 40px !important;
        }

        .select2-container--open .select2-selection--single {
            border-color: #611232;
            box-shadow: 0 0 6px rgba(97, 18, 50, .25);
        }

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
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {

            const table = $('#techniciansTable');

            if (!$.fn.DataTable.isDataTable(table)) {
                table.DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    order: [[0, 'asc'], [1, 'asc']], // Región → Unidad
                    language: {
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ registros",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        infoEmpty: "No hay registros",
                        emptyTable: "No hay técnicos configurados",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "›",
                            previous: "‹"
                        }
                    }
                });
            }

            // Auto ocultar alertas
            setTimeout(() => {
                $('.alert-success, .alert-danger').fadeOut(500);
            }, 5000);
        });
        document.getElementById('assignTechnicianModal')
            .addEventListener('show.bs.modal', function (event) {

                const button = event.relatedTarget;
                const unitId = button.dataset.unit;

                document.getElementById('modal_region_id').value = button.dataset.region;
                document.getElementById('modal_unit_id').value = unitId;
                document.getElementById('modal_unit_name').value = button.dataset.unitName;

                const select = $('#employeeSelect');
                select.empty().append('<option value="">Cargando empleados...</option>');

                $.get(`{{ url('unit-technicians/employees') }}/${unitId}`, function (data) {
                    select.empty().append('<option value="">Seleccione un empleado</option>');
                    data.forEach(emp => {
                        select.append(new Option(emp.text, emp.id));
                    });
                    select.trigger('change');
                });
            });

        $(document).ready(function () {
            $('#employeeSelect').select2({
                dropdownParent: $('#assignTechnicianModal'),
                width: '100%',
                placeholder: 'Buscar empleado...',
            });
        });
    </script>
@endpush