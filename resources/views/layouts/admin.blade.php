@extends('adminlte::page')

{{-- Título y clases del body --}}
@section('title', $title ?? 'Sistema')
@section('body_class', 'sidebar-mini layout-fixed')

@if(session()->has('smiab_access_token'))
    @push('meta')
        <meta name="smiab-token" content="{{ session('smiab_access_token') }}">
    @endpush
@endif

{{-- CSS adicional --}}
@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/panelPrincipal.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endpush

{{-- JS adicional --}}
@push('js')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/confirm-delete.js') }}"></script>
    <script src="{{ asset('js/confirm-import.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <script>
        $(document).ready(function () {


            // Función para inicializar DataTable solo si hay filas reales

            // Función para inicializar DataTable solo si hay filas reales

            // Función para inicializar DataTable solo si hay filas reales
            function initDataTable(table) {
                if (!$.fn.DataTable.isDataTable(table)) {
                    // Verificar si la tabla tiene al menos una fila real (sin colspan)
                    let hasRealRows = $(table).find('tbody tr').not(':has(td[colspan])').length > 0;

                    if (hasRealRows) {
                        $(table).DataTable({
                            responsive: true,
                            autoWidth: false,
                            destroy: true,
                            language: {
                                search: "Buscar:",
                                lengthMenu: "Mostrar _MENU_ registros",
                                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                                paginate: {
                                    first: "Primero",
                                    last: "Último",
                                    next: "&rsaquo;",
                                    previous: "&lsaquo;"
                                },
                                emptyTable: "No hay datos",
                            },
                            pageLength: 10,
                            lengthMenu: [10, 25, 50, 100]
                        });
                    } else {
                        // Si no hay datos, opcionalmente ocultar encabezado de DataTables
                        $(table).addClass('no-datatable');
                    }
                }
            }

            // Inicializar tablas visibles al cargar la página
            $('.tab-pane.active .datatable').each(function () {
                initDataTable(this);
            });

            // Inicializar tablas al cambiar pestaña
            $('#assetTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                let target = $(e.target).data('bs-target'); // #assignable o #inactive
                $(target).find('.datatable').each(function () {
                    initDataTable(this);
                    // Ajustar columnas si ya está inicializada
                    if ($.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable().columns.adjust();
                    }
                });
            });

            // Inicializar tooltips de Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Eliminar preloader de AdminLTE
            $('.preloader').remove();

            // Evitar que se active preloader en paginación
            $(document).on('click', '#paginationContainer a', function () {
                $('.preloader').remove();
            });

        });
    </script>
@endpush


{{-- Encabezado --}}
@section('content_header')
@yield('content_header')
@stop

{{-- Contenido --}}
@section('content')
@yield('content')
@stop

@section('footer')
<x-confirm-delete />
<x-confirm-import />
@stop