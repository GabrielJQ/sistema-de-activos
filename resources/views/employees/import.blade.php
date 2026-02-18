@extends('layouts.admin')

@section('title', 'Importar Empleados')

@section('content')
<div class="d-flex justify-content-center py-4">
    <div class="card shadow-sm border-0 rounded-4 w-100" style="max-width: 950px;">

        <div class="card-header bg-guinda text-white rounded-top-4 py-3 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-circle bg-white text-guinda">
                    <i class="fas fa-file-import"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Importar Empleados</h5>
                    <small class="opacity-75">
                        Carga masiva de Empleados mediante archivo CSV o Excel
                    </small>
                </div>
            </div>
        </div>

        <div class="card-body p-4" style="background-color: #fafafa;">

            {{-- Botón volver --}}
            <div class="mb-3">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary px-4 py-2">
                    <i class="fas fa-arrow-left me-1"></i> Volver al listado
                </a>
            </div>

            {{-- Instrucciones --}}
            <p>Para importar empleados correctamente, descarga la plantilla y completa los campos requeridos. Solo se
                aceptan archivos CSV o Excel (.csv, .xls, .xlsx). Sigue las instrucciones detalladas para evitar errores
                durante la importación.</p>
            <div class="mb-4 d-flex gap-2 flex-wrap">
                <a href="{{ route('employees.template.download') }}" class="btn btn-guinda">
                    <i class="fas fa-download me-1"></i> Descargar Plantilla
                </a>
                <a href="{{ route('employees.instructions.pdf') }}" target="_blank" class="btn btn-secondary">
                    <i class="fas fa-info-circle me-1"></i> Descargar Manual de Instrucciones
                </a>
            </div>

            {{-- Mensajes de sesión --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Errores de supervisores desde PHP --}}
            @if(session('supervisorErrors') && count(session('supervisorErrors')) > 0)
                <div class="col-12 mb-3">
                    <p class="fw-semibold text-danger">Errores detectados en supervisores:</p>
                    <ul class="list-group">
                        @foreach(session('supervisorErrors') as $error)
                            <li class="list-group-item list-group-item-danger">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulario --}}
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="row g-3">

                    {{-- Archivo --}}
                    <div class="col-12">
                        <label for="file" class="form-label fw-semibold">Archivo CSV o Excel <span
                                class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-file-upload"></i></span>
                            <input type="file" name="file" id="file" class="form-control" accept=".csv, .xls, .xlsx"
                                required>
                        </div>
                        @error('file')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Mensaje dinámico --}}
                    <div class="col-12 mb-3">
                        <div id="fileMessage" style="display:none;" class="alert mt-1"></div>
                    </div>

                    {{-- Vista previa con pestañas --}}
                    <div class="col-12" id="previewContainer" style="display:none;">
                        <ul class="nav nav-tabs mb-3" id="previewTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="valid-tab" data-bs-toggle="tab"
                                    data-bs-target="#valid" type="button" role="tab">Registros válidos</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="invalid-tab" data-bs-toggle="tab" data-bs-target="#invalid"
                                    type="button" role="tab">Registros con errores</button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="valid" role="tabpanel">
                                <div class="table-responsive" style="max-height:300px; overflow:auto;">
                                    <table class="table table-sm table-bordered" id="validTable">
                                        <thead class="table-light"></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="invalid" role="tabpanel">
                                <div class="table-responsive" style="max-height:300px; overflow:auto;">
                                    <table class="table table-sm table-bordered table-danger" id="invalidTable">
                                        <thead class="table-light"></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Resumen de importación --}}
                    <div class="col-12 mb-3" id="importSummary" style="display:none;">
                        <p class="fw-semibold">Resumen de la importación:</p>
                        <div id="summaryContent" class="alert alert-info"></div>
                        <div class="d-flex justify-content-end mt-2">
                            <button type="button" id="confirmImport" class="btn btn-guinda me-2" style="display:none;"
                                data-confirm-import
                                data-text="Se importarán únicamente los empleados válidos. ¿Deseas continuar?"
                                data-on-confirm="submitEmployeeImport">
                                <i class="fas fa-check me-1"></i> Continuar con importación
                            </button>

                            <button type="button" id="cancelImport" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .bg-guinda {
        background-color: #611232 !important;
    }

    .form-control,
    .form-select {
        border-radius: 0.6rem !important;
        border: 1.8px solid #ccc !important;
        padding: 0.55rem 0.75rem !important;
        font-size: 0.95rem;
        transition: all 0.25s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #611232 !important;
        box-shadow: 0 0 0 0.2rem rgba(97, 18, 50, 0.2);
    }

    .input-group-text {
        border-radius: 0.6rem 0 0 0.6rem;
        border: 1.8px solid #ccc;
        background-color: #f9f9f9;
        color: #555;
    }

    .input-group:focus-within .input-group-text {
        border-color: #611232;
    }

    .btn-guinda {
        background-color: #611232;
        color: #fff;
        border-radius: 0.5rem;
        border: 1px solid #611232;
        transition: all 0.3s ease;
    }

    .btn-guinda:hover {
        background-color: #8c1f48;
        border-color: #8c1f48;
        box-shadow: 0 4px 10px rgba(140, 31, 72, 0.3);
        transform: translateY(-1px);
        color: #fff;
    }

    .btn-secondary {
        border-radius: 0.5rem;
        padding: 0.55rem 1.2rem;
    }

    @media (max-width: 576px) {
        .input-group {
            flex-wrap: wrap;
        }

        .input-group-text {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script>
    $(document).ready(function () {

        const requiredColumns = ['nombre', 'apellido_pat', 'apellido_mat', 'unidad', 'departamento', 'tipo'];

        function showMessage(message, type = 'info') {
            const fileMessage = $('#fileMessage');

            const closeBtn = '<button type="button" class="btn-close float-end" onclick="$(\'#fileMessage\').hide();"></button>';

            fileMessage
                .removeClass('alert-success alert-danger alert-info')
                .addClass('alert alert-' + type + ' alert-dismissible')
                .html(closeBtn + message)
                .show();
        }


        function previewFile(file) {
            const validTable = $('#validTable');
            const invalidTable = $('#invalidTable');
            const previewContainer = $('#previewContainer');
            const importSummary = $('#importSummary');
            const summaryContent = $('#summaryContent');
            const confirmImport = $('#confirmImport');

            validTable.find('thead').empty(); validTable.find('tbody').empty();
            invalidTable.find('thead').empty(); invalidTable.find('tbody').empty();
            importSummary.hide(); confirmImport.hide();

            const reader = new FileReader();
            const ext = file.name.split('.').pop().toLowerCase();

            reader.onload = function (e) {
                let headers = [], dataRows = [];

                if (ext === 'csv') {
                    const rows = e.target.result.split(/\r?\n/).filter(r => r.trim() !== '');
                    headers = rows[0].split(',').map(h => h.trim());
                    dataRows = rows.slice(1).map(r => r.split(',')).filter(r => r.some(c => c.trim() !== ''));
                } else {
                    const workbook = XLSX.read(e.target.result, { type: 'binary' });
                    const sheet = workbook.Sheets[workbook.SheetNames[0]];
                    const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });
                    headers = jsonData[0].map(h => h ? h.toString().trim() : '');
                    dataRows = jsonData.slice(1).filter(r => r.some(c => c && c.toString().trim() !== ''));
                }

                const missingColumns = requiredColumns.filter(col => !headers.includes(col));
                if (missingColumns.length) {
                    showMessage('Faltan columnas obligatorias: ' + missingColumns.join(', '), 'danger');
                    $('#file').val(''); previewContainer.hide(); return;
                }

                let validRows = [], rowIssues = [];
                let nombres = new Set();
                let supervisorCount = {}; // contar supervisores
                let supervisorErrors = [];

                // Contar supervisores para duplicados
                dataRows.forEach((row) => {
                    const rowData = {};
                    headers.forEach((h, i) => rowData[h] = row[i] ? row[i].toString().trim() : '');
                    const supName = (rowData['supervisor'] || '').toUpperCase();
                    if (supName) supervisorCount[supName] = (supervisorCount[supName] || 0) + 1;
                });

                dataRows.forEach((row, idx) => {
                    const rowData = {};
                    headers.forEach((h, i) => rowData[h] = row[i] ? row[i].toString().trim() : '');
                    let issues = [];

                    const fullName = `${rowData.nombre} ${rowData.apellido_pat} ${rowData.apellido_mat}`.trim();
                    if (!fullName) issues.push('Nombre completo vacío');
                    else if (nombres.has(fullName)) issues.push('Nombre repetido');
                    else nombres.add(fullName);

                    // Validación de supervisores
                    const supervisorName = (rowData['supervisor'] || '').toUpperCase();
                    if (supervisorName) {
                        const allowed = ['ADMINISTRACION Y FINANZAS', 'SUBGERENCIA DE OPERACIONES', 'GERENCIA DE SUCURSAL', 'TECNICO INFORMATICA'];
                        if (!allowed.includes(supervisorName)) {
                            supervisorErrors.push(`Fila ${idx + 2}: Supervisor "${supervisorName}" no permitido`);
                        }
                        if (supervisorCount[supervisorName] > 1) {
                            supervisorErrors.push(`Fila ${idx + 2}: Supervisor "${supervisorName}" duplicado`);
                        }
                    }

                    if (issues.length) rowIssues.push({ row: idx + 2, issues, rowData });
                    else validRows.push(rowData);
                });

                // Mostrar tablas
                validTable.find('thead').append('<tr>' + headers.map(h => `<th>${h}</th>`).join('') + '</tr>');
                validRows.slice(0, 5).forEach(r => validTable.find('tbody').append('<tr>' + headers.map(h => `<td>${r[h] || ''}</td>`).join('') + '</tr>'));

                invalidTable.find('thead').append('<tr>' + headers.map(h => `<th>${h}</th>`).join('') + '<th>Problema</th></tr>');
                rowIssues.forEach(r => invalidTable.find('tbody').append('<tr>' + headers.map(h => `<td>${r.rowData[h] || ''}</td>`).join('') + `<td>${r.issues.join(', ')}</td></tr>`));

                // Resumen
                let summaryHtml = '';
                if (rowIssues.length) {
                    summaryHtml += `<strong>Algunos registros no se importarán:</strong><br>`;
                    rowIssues.forEach(r => summaryHtml += `Fila ${r.row}: ${r.issues.join(', ')}<br>`);
                }
                if (supervisorErrors.length) {
                    summaryHtml += `<br><strong>Errores detectados en supervisores:</strong><br>`;
                    supervisorErrors.forEach(e => summaryHtml += `- ${e}<br>`);
                }

                summaryHtml += `<br>${validRows.length} registros listos para importar.`;
                summaryContent.html(summaryHtml);
                importSummary.show();

                if (validRows.length > 0) confirmImport.show();
                $('#importForm').data('validRows', validRows);
            };

            if (ext === 'csv') reader.readAsText(file);
            else reader.readAsBinaryString(file);
        }

        $('#file').on('change', function () { const file = this.files[0]; if (file) previewFile(file); });
        window.submitEmployeeImport = function () {
            document.getElementById('importForm').submit();
        };

        $('#cancelImport').on('click', function () {
            $('#file').val('');
            $('#previewContainer').hide();
            $('#importSummary').hide();
            showMessage('Importación cancelada.', 'info');
        });

    });

</script>
@stop