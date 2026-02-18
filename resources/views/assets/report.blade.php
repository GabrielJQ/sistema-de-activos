@extends('layouts.admin')

@section('title', 'Reporte de Daño')

@section('content')
<div class="d-flex justify-content-center align-items-start py-4">
    <div class="card shadow border-0 rounded-4 w-100" style="max-width: 950px;">

        {{-- Cabecera con botones al frente --}}
        <div class="d-flex justify-content-between align-items-center card-header bg-guinda text-white fw-semibold fs-5 rounded-top py-3">
            <div>
                <i class="fas fa-desktop me-2"></i> Levantar Reporte de Daño
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary px-3 py-1 shadow-sm">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                <button type="submit" form="report-form" class="btn btn-guinda px-3 py-1 shadow-sm">
                    <i class="fas fa-file-pdf me-1"></i> Generar PDF
                </button>
                <button type="button" class="btn btn-copiar px-3 py-1 shadow-sm" title="Copiar al portapapeles">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>

        {{-- Cuerpo del formulario --}}


        <div class="card-body px-4 py-4">
            <form id="report-form" action="{{ route('assets.submitReport', $asset) }}" method="POST" enctype="multipart/form-data" target="_blank">
                @csrf

                {{-- Información del Equipo --}}
                <h6 class="section-title mt-2">
                    <i class="fas fa-info-circle me-2"></i> Información del Equipo
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="serie" class="form-label fw-semibold">N° Serie</label>
                        <input type="text" id="serie" class="form-control" value="{{ $asset->serie }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="ip" class="form-label fw-semibold">I.P.</label>
                        <input type="text" name="ip" id="ip" class="form-control" value="{{ $asset->ip }}">
                    </div>
                    <div class="col-md-6">
                        <label for="resguardo" class="form-label fw-semibold">N° Resguardo</label>
                        <input type="text" name="resguardo" id="resguardo" class="form-control" value="{{ $asset->resguardo }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Marca</label>
                        <input type="text" class="form-control" value="{{ $asset->marca }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Modelo</label>
                        <input type="text" class="form-control" value="{{ $asset->modelo }}" readonly>
                    </div>
                </div>

                {{-- Información del Usuario --}}
                <h6 class="section-title mt-2">
                    <i class="fas fa-user me-2"></i> Información del Usuario
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Usuario</label>
                        <input type="text" class="form-control" value="{{ $employee->full_name ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Correo</label>
                        <input type="text" class="form-control" value="{{ $employee->email ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Dirección o Ubicación</label>
                        <input type="text" class="form-control" value="{{ $employee->direccion ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Área o Departamento</label>
                        <input type="text" class="form-control" value="{{ $employee->department->areanom ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cargo o Puesto</label>
                        <input type="text" class="form-control" value="{{ $employee->puesto ?? 'N/A' }}" readonly>
                    </div>
                </div>

                {{-- Imagen del daño --}}
                <div class="mb-4">
                    <label for="damage_images" class="form-label fw-semibold">Fotografía(s) del Daño (opcional)</label>
                    <input type="file" name="damage_images[]" id="damage_images" class="form-control" accept="image/*" multiple>
                    <small class="text-muted">Formatos permitidos: JPG, PNG, JPEG (máx. 4MB por imagen)</small>
                </div>

                {{-- Solicitud o Falla --}}
                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Solicitud o Falla</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required placeholder="Describa la falla o solicitud"></textarea>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* === COLORES PRINCIPALES === */
.bg-guinda { background-color: #611232 !important; }
.text-guinda { color: #611232 !important; }

/* === SECCIONES === */
.section-title {
    font-weight: 600;
    color: #611232;
    font-size: 1.15rem;
    border-left: 4px solid #611232;
    padding-left: 0.6rem;
    margin-bottom: 1rem;
}

/* === INPUTS === */
.form-control {
    border-radius: 0.6rem !important;
    border: 1.5px solid #ccc !important;
    padding: 0.5rem 0.75rem !important;
    background-color: #fff;
    transition: all 0.25s ease;
}
.form-control:focus {
    border-color: #611232 !important;
    box-shadow: 0 0 0 0.2rem rgba(97,18,50,0.2);
}

/* === BOTONES === */
.btn-guinda {
    background-color: #8c1f48;
    color: #fff;
    border-radius: 0.5rem;
    border: 1px solid #611232;
    transition: all 0.25s ease;
}
.btn-guinda:hover {
    background-color: #a32a5c;
    border-color: #611232;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(163,42,92,0.3);
}

.btn-outline-secondary {
    border-radius: 0.5rem;
    border: 1px solid #aaa;
    background-color: #fff;
    color: #555;
    transition: all 0.25s ease;
}
.btn-outline-secondary:hover {
    background-color: #f0f0f0;
    border-color: #999;
    color: #000;
}

.btn-copiar {
    background-color: #6c757d; 
    color: #fff;
    border-radius: 0.5rem;
    border: 1px solid #5a6268;
    transition: all 0.25s ease;
}
.btn-copiar:hover {
    background-color: #5a6268;
    border-color: #4e555b;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(90,85,88,0.3);
}

.card { border-radius: 1rem !important; }
.card-body { background-color: #fafafa; border-radius: 0 0 1rem 1rem; }
</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnCopiar = document.querySelector('.btn-copiar');
    const descriptionField = document.getElementById('description');
    const ipField = document.getElementById('ip');
    const resguardoField = document.getElementById('resguardo');

    btnCopiar.addEventListener('click', function() {
        let ip = ipField ? ipField.value.trim() : 'N/A';
        let resguardo = resguardoField ? resguardoField.value.trim() : 'N/A';
        let description = descriptionField ? descriptionField.value.trim() : '';

        let htmlContent = '';

        // Datos del Equipo
        htmlContent += `
        <div class="card">
            <div class="card-header" style="background-color:#666666; color:#fff; font-weight:bold; padding:4px 6px; font-size:11px;">Datos del Equipo</div>
            <div class="card-body" style="padding:4px 6px;">
                <table style="width:100%; border-collapse:collapse; margin-bottom:6px;">
                    <tr><th style="border:1px solid #999; padding:4px 6px;">N° Serie</th><td style="border:1px solid #999; padding:4px 6px;">{{ $asset->serie ?? 'N/A' }}</td></tr>
                    <tr><th style="border:1px solid #999; padding:4px 6px;">I.P.</th><td style="border:1px solid #999; padding:4px 6px;">${ip}</td></tr>
                    <tr><th style="border:1px solid #999; padding:4px 6px;">N° Resguardo</th><td style="border:1px solid #999; padding:4px 6px;">${resguardo}</td></tr>
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Marca</th><td style="border:1px solid #999; padding:4px 6px;">{{ $asset->marca ?? 'N/A' }}</td></tr>
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Modelo</th><td style="border:1px solid #999; padding:4px 6px;">{{ $asset->modelo ?? 'N/A' }}</td></tr>
                </table>
            </div>
        </div>
        `;

        // Datos del Usuario
        htmlContent += `
        <div class="card">
            <div class="card-header" style="background-color:#666666; color:#fff; font-weight:bold; padding:4px 6px; font-size:11px;">Datos del Usuario</div>
            <div class="card-body" style="padding:4px 6px;">
                <table style="width:100%; border-collapse:collapse; margin-bottom:6px;">
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Usuario</th><td style="border:1px solid #999; padding:4px 6px;">{{ $employee->full_name ?? 'N/A' }}</td></tr>
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Correo</th><td style="border:1px solid #999; padding:4px 6px;">{{ $employee->email ?? 'N/A' }}</td></tr>
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Centro de Trabajo</th><td style="border:1px solid #999; padding:4px 6px;">ALIMENTACIÓN PARA EL BIENESTAR, S.A. DE C.V. REGIONAL OAXACA</td></tr>
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Dirección o Ubicación</th><td style="border:1px solid #999; padding:4px 6px;">{{ $employee->direccion ?? 'N/A' }}</td></tr>
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Área o Departamento</th><td style="border:1px solid #999; padding:4px 6px;">{{ $employee->department->areanom ?? 'N/A' }}</td></tr>
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Cargo o Puesto</th><td style="border:1px solid #999; padding:4px 6px;">{{ $employee->puesto ?? 'N/A' }}</td></tr>
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Teléfono</th><td style="border:1px solid #999; padding:4px 6px;">{{ $employee->telefono ?? 'N/A' }}</td></tr> 
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Extensión</th><td style="border:1px solid #999; padding:4px 6px;">{{ $employee->extension ?? 'N/A' }}</td></tr> 
                    <tr><th style="border:1px solid #999; padding:4px 6px;">Horario</th><td style="border:1px solid #999; padding:4px 6px;">8:00 am a 4:00 pm</td></tr>
                </table>
            </div>
        </div>
        `;

        // Solicitud o Falla
        if(description){
            htmlContent += `
            <div class="card">
                <div class="card-header" style="background-color:#666666; color:#fff; font-weight:bold; padding:4px 6px; font-size:11px;">Solicitud o Falla</div>
                <div class="card-body" style="padding:4px 6px;">
                    <p>${description}</p>
                </div>
            </div>
            `;
        }

        // Copiar
        navigator.clipboard.write([
            new ClipboardItem({
                "text/html": new Blob([htmlContent], { type: "text/html" }),
                "text/plain": new Blob([htmlContent.replace(/<[^>]+>/g, '')], { type: "text/plain" })
            })
        ]).then(() => {
            // mensaje corto tipo toast
            const msg = document.createElement('div');
            msg.innerText = 'Contenido copiado';
            msg.style.position = 'fixed';
            msg.style.bottom = '20px';
            msg.style.right = '20px';
            msg.style.backgroundColor = '#333';
            msg.style.color = '#fff';
            msg.style.padding = '8px 12px';
            msg.style.borderRadius = '5px';
            msg.style.zIndex = '9999';
            msg.style.opacity = '0.9';
            document.body.appendChild(msg);
            setTimeout(() => msg.remove(), 2000);
        }).catch(err => console.error('Error al copiar:', err));
    });
});
</script>
@stop
