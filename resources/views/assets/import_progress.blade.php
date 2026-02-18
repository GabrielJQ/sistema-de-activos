@extends('layouts.admin')

@section('title', 'Progreso de Importación')

@section('content')
<div class="d-flex justify-content-center py-4">
    <div class="card shadow-sm border-0 rounded-4 w-100" style="max-width: 800px;">

        <div class="card-header bg-guinda text-white rounded-top-4 py-3 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-circle bg-white text-guinda">
                    <i class="fas fa-spinner fa-spin" id="statusIcon"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Procesando Importación</h5>
                    <small class="opacity-75" id="filenameDisplay">
                        {{ $task->filename }}
                    </small>
                </div>
            </div>
        </div>

        <div class="card-body p-5 text-center" style="background-color: #fafafa;">

            <div id="processingArea">
                <h4 class="mb-4 fw-bold text-dark" id="statusText">Iniciando procesamiento...</h4>

                <div class="progress mb-3" style="height: 25px; border-radius: 12px; background-color: #e9ecef;">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-guinda"
                        role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>

                <p class="text-muted mb-5" id="progressStats">
                    Preparando registros...
                </p>

                <div class="d-flex justify-content-center gap-4 mb-4">
                    <div class="stat-box p-3 rounded-3 bg-white shadow-sm" style="min-width: 120px;">
                        <small class="text-muted d-block mb-1">Total</small>
                        <span class="fs-4 fw-bold text-dark" id="totalRows">0</span>
                    </div>
                    <div class="stat-box p-3 rounded-3 bg-white shadow-sm" style="min-width: 120px;">
                        <small class="text-muted d-block mb-1">Procesados</small>
                        <span class="fs-4 fw-bold text-guinda" id="processedRows">0</span>
                    </div>
                </div>

                <button id="cancelBtn" class="btn btn-outline-danger rounded-pill px-4">
                    <i class="fas fa-stop me-2"></i> Cancelar Importación
                </button>
            </div>

            @php
                $isEmployee = str_contains(strtolower($task->filename), 'empleado');
                $indexRoute = $isEmployee ? route('employees.index') : route('assets.index');
                $importRoute = $isEmployee ? route('employees.showImport') : route('assets.showImport');
                $itemName = $isEmployee ? 'Empleados' : 'Activos';
            @endphp

            <div id="completedArea" style="display: none;">
                <div class="mb-4">
                    <div class="icon-circle bg-success text-white mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="fas fa-check"></i>
                    </div>
                    <h3 class="fw-bold text-success">¡Importación Completada!</h3>
                    <p class="text-muted">Todos los registros han sido procesados correctamente.</p>
                </div>
                <a href="{{ $indexRoute }}" class="btn btn-guinda btn-lg px-5 rounded-pill">
                    <i class="fas fa-th-list me-2"></i> Ver {{ $itemName }} Importados
                </a>
            </div>

            <div id="completedWithErrorsArea" style="display: none;">
                <div class="mb-4">
                    <div class="icon-circle bg-warning text-white mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="fw-bold text-warning">Importación Finalizada con Observaciones</h3>
                    <p class="text-muted">El proceso terminó, pero algunas filas no pudieron importarse.</p>
                </div>

                <div class="text-start mb-4">
                    <div class="alert alert-warning" role="alert">
                        <h5 class="alert-heading fs-6 fw-bold"><i class="fas fa-list-ul me-2"></i>Filas no importadas:
                        </h5>
                        <hr>
                        <ul id="errorList" class="mb-0 small" style="max-height: 200px; overflow-y: auto;">
                            <!-- List items will be injected via JS -->
                        </ul>
                    </div>
                </div>

                <a href="{{ $indexRoute }}" class="btn btn-guinda btn-lg px-5 rounded-pill">
                    <i class="fas fa-th-list me-2"></i> Ver Importados
                </a>
            </div>

            <div id="failedArea" style="display: none;">
                <div class="mb-4">
                    <div class="icon-circle bg-danger text-white mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="fas fa-times"></i>
                    </div>
                    <h3 class="fw-bold text-danger">Error Crítico</h3>
                    <p class="text-muted" id="errorMessage">Ocurrió un problema insteperado.</p>
                </div>
                <a href="{{ $importRoute }}" class="btn btn-secondary btn-lg px-5 rounded-pill">
                    <i class="fas fa-undo me-2"></i> Reintentar
                </a>
            </div>

            <div id="canceledArea" style="display: none;">
                <div class="mb-4">
                    <div class="icon-circle bg-secondary text-white mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="fas fa-stop-circle"></i>
                    </div>
                    <h3 class="fw-bold text-secondary">Importación Cancelada</h3>
                    <p class="text-muted">El proceso fue detenido por el usuario.</p>
                </div>
                <a href="{{ $indexRoute }}" class="btn btn-guinda btn-lg px-5 rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Volver
                </a>
            </div>

            <div id="longProcessArea" style="display: none;">
                <div class="mb-4">
                    <div class="icon-circle bg-info text-white mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <h3 class="fw-bold text-info">El proceso está tardando más de lo esperado</h3>
                    <p class="text-muted">No te preocupes, la importación continúa en segundo plano. <br>
                        Puedes esperar aquí o verificar el estado manualmente.</p>
                </div>
                <button id="checkStatusBtn" class="btn btn-info text-white btn-lg px-5 rounded-pill mb-3">
                    <i class="fas fa-sync-alt me-2"></i> Verificar Estado Ahora
                </button>
                <br>
                <a href="{{ $indexRoute }}" class="btn btn-outline-secondary btn-sm px-4 rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Volver al listado
                </a>
            </div>

        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .bg-guinda {
        background-color: #611232 !important;
    }

    .text-guinda {
        color: #611232 !important;
    }

    .icon-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .stat-box {
        border: 1px solid #eee;
    }

    .progress-bar-animated {
        transition: width 0.5s ease-in-out;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        const taskId = "{{ $task->id }}";
        const statusUrl = "{{ route('assets.import.status', $task->id) }}";
        const cancelUrl = "{{ route('assets.import.cancel', $task->id) }}";
        let pollInterval;
        let errorCount = 0;
        const maxErrors = 10; // Tolerancia a fallos de red

        function updateStatus() {
            $.ajax({
                url: statusUrl,
                method: 'GET',
                success: function (data) {
                    errorCount = 0; // Reset error counter
                    const percentage = data.percentage || 0;
                    $('#progressBar').css('width', percentage + '%').text(percentage + '%');
                    $('#totalRows').text(data.total_rows);
                    $('#processedRows').text(data.processed_rows);

                    if (data.status === 'processing' || data.status === 'pending') {
                        $('#statusText').text(data.status === 'pending' ? 'Esperando inicio...' : 'Procesando datos...');
                        $('#progressStats').text(`Procesando fila ${data.processed_rows} de ${data.total_rows}`);
                    } else if (data.status === 'completed') {
                        clearInterval(pollInterval);
                        localStorage.setItem('import_status', 'inactive'); // Detener poller global
                        $('#processingArea').fadeOut(300, function () {
                            $('#completedArea').fadeIn();
                            $('#statusIcon').removeClass('fa-spinner fa-spin').addClass('fa-check');
                        });
                    } else if (data.status === 'completed_with_errors') {
                        clearInterval(pollInterval);
                        localStorage.setItem('import_status', 'inactive');
                        $('#processingArea').fadeOut(300, function () {
                            $('#completedWithErrorsArea').fadeIn();
                            $('#statusIcon').removeClass('fa-spinner fa-spin').addClass('fa-exclamation-triangle');

                            // Populate errors
                            const list = $('#errorList');
                            list.empty();
                            if (data.errors && Array.isArray(data.errors)) {
                                data.errors.forEach(err => {
                                    list.append(`<li class="small">${err}</li>`);
                                });
                            }
                        });
                    } else if (data.status === 'failed') {
                        clearInterval(pollInterval);
                        localStorage.setItem('import_status', 'inactive');
                        $('#processingArea').fadeOut(300, function () {
                            $('#failedArea').fadeIn();
                            $('#statusIcon').removeClass('fa-spinner fa-spin').addClass('fa-times');
                            if (data.errors && data.errors.message) {
                                $('#errorMessage').text(data.errors.message);
                            }
                        });
                    } else if (data.status === 'canceled') {
                        clearInterval(pollInterval);
                        localStorage.setItem('import_status', 'inactive');
                        $('#processingArea').fadeOut(300, function () {
                            $('#canceledArea').fadeIn();
                            $('#statusIcon').removeClass('fa-spinner fa-spin').addClass('fa-stop-circle');
                        });
                    }
                },
                error: function () {
                    errorCount++;
                    if (errorCount > maxErrors) {
                        showLongProcessWarning();
                    }
                }
            });
        }

        function showLongProcessWarning() {
            clearInterval(pollInterval);
            $('#processingArea').fadeOut(300, function () {
                $('#longProcessArea').fadeIn();
            });
        }

        $('#checkStatusBtn').click(function () {
            $('#longProcessArea').fadeOut(300, function () {
                $('#processingArea').fadeIn();
                errorCount = 0;
                updateStatus();
                pollInterval = setInterval(updateStatus, 2000);
            });
        });

        $('#cancelBtn').click(function () {
            if (confirm('¿Estás seguro de que deseas cancelar la importación?')) {
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Cancelando...');
                $.ajax({
                    url: cancelUrl,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function () {
                        // El polling detectará el cambio de estado
                    },
                    error: function (xhr) {
                        alert('Error al cancelar: ' + (xhr.responseJSON?.error || 'Error desconocido'));
                        $('#cancelBtn').prop('disabled', false).html('<i class="fas fa-stop me-2"></i> Cancelar Importación');
                    }
                });
            }
        });

        // Start polling every 2 seconds
        pollInterval = setInterval(updateStatus, 2000);
        updateStatus(); // Initial call

        // Safety timeout (60 mins) - Mostrar advertencia en lugar de morir silenciosamente
        setTimeout(showLongProcessWarning, 3600000);
    });
</script>
@stop