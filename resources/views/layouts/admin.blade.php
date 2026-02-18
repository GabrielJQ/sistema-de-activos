@extends('adminlte::page')

{{-- Título y clases del body --}}
@section('title', $title ?? 'Sistema')
@section('body_class', 'sidebar-mini layout-fixed')

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

            // Poller de Estado Global de Importación
            const checkImportStatus = () => {
                // Solo si el usuario está autenticado y no estamos ya en la página de progreso (para no duplicar)
                if (!window.location.href.includes('assets/import') || window.location.href.includes('assets/import/active')) {
                    $.ajax({
                        url: '{{ route("assets.import.checkActive") }}',
                        method: 'GET',
                        success: function (response) {
                            if (response.active) {
                                showGlobalImportIndicator(response);
                            } else {
                                $('#globalImportIndicator').remove();
                            }
                        },
                        error: function () { console.log('Error checking import status'); }
                    });
                }
            };

            function showGlobalImportIndicator(data) {
                let indicator = $('#globalImportIndicator');
                if (indicator.length === 0) {
                    indicator = $(`
                                <div id="globalImportIndicator" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
                                    <div class="toast show text-white bg-guinda" role="alert" aria-live="assertive" aria-atomic="true">
                                        <div class="toast-header text-guinda">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            <strong class="me-auto">Importando...</strong>
                                            <small>${data.progress}%</small>
                                        </div>
                                        <div class="toast-body bg-white text-dark">
                                            Procesando ${data.filename}
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-guinda" role="progressbar" style="width: ${data.progress}%"></div>
                                            </div>
                                            <div class="mt-2 text-end">
                                                <a href="{{ route('assets.showImport') }}" class="btn btn-xs btn-guinda">Ver Detalle</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                    $('body').append(indicator);
                } else {
                    // Update content
                    indicator.find('small').text(data.progress + '%');
                    indicator.find('.progress-bar').css('width', data.progress + '%');
                }
            }

            // Poller de Estado Global de Importación (Basado en Eventos - Zero Overhead)
            let globalPollInterval = null;

            const stopPolling = () => {
                if (globalPollInterval) {
                    clearTimeout(globalPollInterval);
                    globalPollInterval = null;
                }
                $('#globalImportIndicator').remove();
            };

            const startPolling = () => {
                if (globalPollInterval) return; // Ya está corriendo

                const poll = () => {
                    // Solo si NO estamos en la página de progreso principal
                    if (!window.location.href.includes('assets/import') || window.location.href.includes('assets/import/active')) {
                        $.ajax({
                            url: '{{ route("assets.import.checkActive") }}',
                            method: 'GET',
                            success: function (response) {
                                if (response.active) {
                                    showGlobalImportIndicator(response);
                                    globalPollInterval = setTimeout(poll, 5000); // Activo: Sigue consultando
                                } else {
                                    stopPolling(); // Inactivo: A dormir
                                    localStorage.setItem('import_status', 'inactive'); // Sincronizar estado
                                }
                            },
                            error: function () {
                                // Error de red: reintentar en 15s por si acaso, o morir?
                                // Mejor reintentar lento por si vuelve la red.
                                globalPollInterval = setTimeout(poll, 15000);
                            }
                        });
                    } else {
                        // Estamos en la página de progreso, el poller global descansa
                        // pero chequea cada 10s por si cambiamos de página sin recarga completa (raro en este app pero posible)
                        globalPollInterval = setTimeout(poll, 10000);
                    }
                };
                poll();
            };

            // 1. Chequeo inicial al cargar página
            if (localStorage.getItem('import_status') === 'active') {
                startPolling();
            }

            // 2. Escuchar cambios en otras pestañas
            window.addEventListener('storage', (event) => {
                if (event.key === 'import_status') {
                    if (event.newValue === 'active') {
                        startPolling();
                    } else {
                        stopPolling();
                    }
                }
            });

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