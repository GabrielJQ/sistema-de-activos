@php
    $isEdit = isset($supplier);
@endphp

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <strong><i class="fas fa-exclamation-triangle me-2"></i> Por favor corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Título de sección --}}
<h6 class="section-title d-flex align-items-center gap-2">
    <i class="fas fa-building"></i> Información del Proveedor
</h6>

<div class="card border-0 shadow-soft rounded-4 mb-4">
    <div class="card-body">

        <div class="row g-4">

            {{-- Nombre del proveedor --}}
            <div class="col-12">
                <label for="prvnombre" class="form-label fw-semibold">
                    Nombre del proveedor
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted">
                        <i class="fas fa-user-tie"></i>
                    </span>
                    <input type="text"
                           name="prvnombre"
                           id="prvnombre"
                           class="form-control @error('prvnombre') is-invalid @enderror"
                           value="{{ old('prvnombre', $supplier->prvnombre ?? '') }}"
                           placeholder="Ej. Proveedor XYZ">
                    @error('prvnombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Contrato --}}
            <div class="col-12 col-md-6">
                <label for="contrato" class="form-label fw-semibold">Contrato</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted">
                        <i class="fas fa-file-contract"></i>
                    </span>
                    <input type="text"
                           name="contrato"
                           id="contrato"
                           class="form-control @error('contrato') is-invalid @enderror"
                           value="{{ old('contrato', $supplier->contrato ?? '') }}"
                           placeholder="Ej. CONTR-2024-01">
                    @error('contrato')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Teléfono --}}
            <div class="col-12 col-md-6">
                <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted">
                        <i class="fas fa-phone"></i>
                    </span>
                    <input type="text"
                           name="telefono"
                           id="telefono"
                           class="form-control @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono', $supplier->telefono ?? '') }}"
                           placeholder="Ej. 555-123-4567">
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Enlace --}}
            <div class="col-12 col-md-6">
                <label for="enlace" class="form-label fw-semibold">Enlace</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted">
                        <i class="fas fa-link"></i>
                    </span>
                    <input type="url"
                           name="enlace"
                           id="enlace"
                           class="form-control @error('enlace') is-invalid @enderror"
                           value="{{ old('enlace', $supplier->enlace ?? '') }}"
                           placeholder="https://www.proveedor.com">
                    @error('enlace')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Estatus --}}
            <div class="col-12 col-md-6">
                <label for="prvstatus" class="form-label fw-semibold">Estatus</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted">
                        <i class="fas fa-toggle-on"></i>
                    </span>
                    <select name="prvstatus" id="prvstatus" class="form-select">
                        <option value="1" {{ (old('prvstatus', $supplier->prvstatus ?? 1) ? 'selected' : '') }}>
                            Activo
                        </option>
                        <option value="0" {{ (!old('prvstatus', $supplier->prvstatus ?? 1) ? 'selected' : '') }}>
                            Inactivo
                        </option>
                    </select>
                </div>
            </div>

            {{-- Logo --}}
            <div class="col-12 col-md-6">
                <label for="logo" class="form-label fw-semibold">Logo (opcional)</label>
                <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror">
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if($isEdit && $supplier->logo)
                    <img src="{{ asset($supplier->logo) }}"
                         alt="Logo"
                         class="img-fluid mt-3 supplier-logo-preview"
                         style="max-height:60px;">
                @endif
            </div>

        </div>

        {{-- Botones --}}
        <div class="d-flex justify-content-end mt-5 gap-3 flex-wrap">
            <button type="submit" class="btn btn-guinda px-5 py-2 shadow-sm">
                <i class="fas fa-save me-2"></i>
                {{ $isEdit ? 'Actualizar' : 'Crear' }}
            </button>

            <a href="{{ route('suppliers.index') }}"
               class="btn btn-outline-secondary px-5 py-2 shadow-sm">
                <i class="fas fa-times me-2"></i> Cancelar
            </a>
        </div>

    </div>
</div>

{{-- CSS específico para formulario (MISMO PATRÓN QUE YA TE FUNCIONA) --}}
@push('css')
<style>
/* ============================= */
/* PALETA CORPORATIVA */
/* ============================= */
.bg-guinda { background-color:#611232 !important; color:#fff; }

.section-title {
    font-weight:600;
    color:#611232;
    font-size:1.15rem;
    margin-bottom:1rem;
    border-left:4px solid #611232;
    padding-left:.6rem;
}

/* ============================= */
/* CARD */
/* ============================= */
.shadow-soft {
    box-shadow:0 4px 14px rgba(0,0,0,.08);
}

/* ============================= */
/* INPUTS */
/* ============================= */
.form-control,
.form-select {
    border-radius:.55rem !important;
    border:2px solid #b0b0b0 !important;
    padding:.55rem .75rem !important;
    transition:.25s ease;
}

.form-control:focus,
.form-select:focus {
    border-color:#611232 !important;
    box-shadow:0 0 0 .2rem rgba(97,18,50,.22);
}

.input-group-text {
    border-radius:.55rem 0 0 .55rem;
    border:2px solid #b0b0b0;
    background:#f8f9fa;
    color:#666;
}

.input-group:focus-within .input-group-text {
    border-color:#611232;
}

/* ============================= */
/* BOTONES */
/* ============================= */
.btn-guinda {
    background-color:#611232 !important;
    color:#fff !important;
    border-radius:.55rem;
    border:1px solid #611232;
    font-weight:600;
    transition:.25s ease;
}

.btn-guinda:hover {
    background-color:#490e23 !important;
    border-color:#490e23 !important;
}

.btn-outline-secondary {
    border-radius:.55rem;
}

/* ============================= */
/* RESPONSIVE */
/* ============================= */
@media (max-width:576px){
    .input-group{ flex-wrap:wrap; }
    .input-group-text{
        width:100%;
        border-radius:.55rem .55rem 0 0;
    }
}
</style>
@endpush

{{-- JS para validación de logo --}}
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validacion imagen
    document.getElementById('logo')?.addEventListener('change', function(e) {
        const allowedTypes = ['image/jpeg','image/png','image/jpg','image/gif','image/webp'];
        const file = e.target.files[0];
        if (file && !allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo no válido',
                text: 'Solo se permiten imágenes (jpeg, png, jpg, gif, webp).',
                confirmButtonColor: '#611232'
            });
            e.target.value = ''; // limpiar input
        }
    });

    // Validacion required fields (solo nombre por ahora)
    const form = document.querySelector('form');
    if(form) {
        form.addEventListener('submit', function(e) {
            const prvnombre = document.getElementById('prvnombre');
            if (prvnombre && !prvnombre.value.trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incompletos',
                    html: 'El campo <b>Nombre del proveedor</b> es obligatorio.',
                    confirmButtonColor: '#611232'
                });
            }
        });
    }
});
</script>
@endpush
