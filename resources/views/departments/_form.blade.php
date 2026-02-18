@php
    $isEdit = isset($department);
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

<h6 class="section-title"><i class="fas fa-building me-2"></i> Información del Departamento</h6>

<div class="row g-4">
    {{-- Clave --}}
    <div class="col-12 col-md-6">
        <label for="areacve" class="form-label fw-semibold">Clave del Departamento</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="fas fa-key"></i></span>
            <input type="text" name="areacve" id="areacve" class="form-control @error('areacve') is-invalid @enderror"
                   value="{{ old('areacve', $department->areacve ?? '') }}"
                   placeholder="Ej. DEP-001">
            @error('areacve')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Nombre --}}
    <div class="col-12 col-md-6">
        <label for="areanom" class="form-label fw-semibold">Nombre del Departamento</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="fas fa-building"></i></span>
            <input type="text" name="areanom" id="areanom" class="form-control @error('areanom') is-invalid @enderror"
                   value="{{ old('areanom', $department->areanom ?? '') }}"
                   placeholder="Ej. Recursos Humanos">
            @error('areanom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Tipo --}}
    <div class="col-12 col-md-6">
        <label for="tipo" class="form-label fw-semibold">Tipo</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="fas fa-layer-group"></i></span>
            <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror">
                <option value="">-- Seleccione tipo --</option>
                <option value="Oficina" {{ (old('tipo', $department->tipo ?? '') == 'Oficina') ? 'selected' : '' }}>🏢 Oficina</option>
                <option value="Almacen" {{ (old('tipo', $department->tipo ?? '') == 'Almacen') ? 'selected' : '' }}>🏬 Almacén</option>
                <option value="Otro" {{ (old('tipo', $department->tipo ?? '') == 'Otro') ? 'selected' : '' }}>📦 Otro</option>
            </select>
            @error('tipo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Unidad --}}
    <div class="col-12 col-md-6">
        <label for="unit_id" class="form-label fw-semibold">Unidad</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="fas fa-sitemap"></i></span>
            <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                <option value="">-- Seleccione unidad --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ (old('unit_id', $department->unit_id ?? '') == $unit->id) ? 'selected' : '' }}>
                        {{ $unit->uninom }}
                    </option>
                @endforeach
            </select>
            @error('unit_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Dirección --}}
    <div class="card shadow-sm border-0 rounded-4 mt-4">
        <div class="card-header bg-guinda text-white fw-semibold fs-5 rounded-top">
            <i class="fas fa-map-marker-alt me-2"></i> Dirección del Departamento
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Dirección existente --}}
                <div class="col-12">
                    <label for="address_id" class="form-label fw-semibold">Seleccionar Dirección Existente</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-map"></i></span>
                        <select name="address_id" id="address_id" class="form-select">
                            <option value="">-- Nueva Dirección --</option>
                            @foreach($addresses ?? [] as $address)
                                <option value="{{ $address->id }}"
                                    {{ (old('address_id', $department->address_id ?? '') == $address->id) ? 'selected' : '' }}>
                                    {{ $address->calle ?? '' }} {{ $address->colonia ?? '' }},
                                    {{ $address->municipio ?? '' }} - {{ $address->estado ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted">Si no existe, capture los campos siguientes para registrar una nueva dirección.</small>
                </div>

                {{-- Campos nueva dirección --}}
                <div id="new-address-fields" class="row g-3 mt-2">
                    <div class="col-12 col-md-6">
                        <label for="calle" class="form-label fw-semibold">Calle</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-road"></i></span>
                            <input type="text" name="calle" id="calle" class="form-control"
                                   value="{{ old('calle', $department->address->calle ?? '') }}"
                                   placeholder="Ej. Av. Reforma">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="colonia" class="form-label fw-semibold">Colonia</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-city"></i></span>
                            <input type="text" name="colonia" id="colonia" class="form-control"
                                   value="{{ old('colonia', $department->address->colonia ?? '') }}"
                                   placeholder="Ej. Centro">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="cp" class="form-label fw-semibold">Código Postal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-mail-bulk"></i></span>
                            <input type="text" name="cp" id="cp" class="form-control"
                                   value="{{ old('cp', $department->address->cp ?? '') }}"
                                   placeholder="Ej. 06000">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="municipio" class="form-label fw-semibold">Municipio</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-landmark"></i></span>
                            <input type="text" name="municipio" id="municipio" class="form-control"
                                   value="{{ old('municipio', $department->address->municipio ?? '') }}"
                                   placeholder="Ej. Cuauhtémoc">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="estado" class="form-label fw-semibold">Estado</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-flag"></i></span>
                            <input type="text" name="estado" id="estado" class="form-control"
                                   value="{{ old('estado', $department->address->estado ?? '') }}"
                                   placeholder="Ej. CDMX">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="ciudad" class="form-label fw-semibold">Ciudad</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-building"></i></span>
                            <input type="text" name="ciudad" id="ciudad" class="form-control"
                                   value="{{ old('ciudad', $department->address->ciudad ?? '') }}"
                                   placeholder="Ej. Ciudad de México">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Botones --}}
<div class="d-flex justify-content-end mt-4 gap-3 flex-wrap">
    <button type="submit" class="btn btn-guinda fw-semibold px-5 py-2">
        <i class="fas fa-save me-2"></i> {{ $isEdit ? 'Actualizar' : 'Crear' }}
    </button>
    <a href="{{ route('departments.index') }}" class="btn btn-secondary fw-semibold px-5 py-2">
        <i class="fas fa-times me-2"></i> Cancelar
    </a>
</div>

{{-- CSS --}}
@push('css')
<style>
.section-title { font-weight: 600; color: #611232; margin-bottom: 1rem; font-size: 1.15rem; }

.form-control, .form-select {
    border-radius: 0.5rem !important;
    border: 2px solid #b0b0b0 !important;
    padding: 0.55rem 0.75rem !important;
    transition: all 0.3s ease;
}
.form-control:focus, .form-select:focus {
    border-color: #611232 !important;
    box-shadow: 0 0 0 0.2rem rgba(97,18,50,0.25);
}

.input-group-text {
    border-radius: 0.5rem 0 0 0.5rem;
    border: 2px solid #b0b0b0;
    background-color: #f8f9fa;
}
.input-group:focus-within .input-group-text {
    border-color: #611232;
}

.btn-guinda {
    background-color: #611232 !important;
    color: #fff !important;
    border-radius: 0.5rem;
    border: 1px solid #611232;
    transition: 0.3s;
    font-weight: 600;
}
.btn-guinda:hover {
    background-color: #490e23 !important;
    border-color: #490e23 !important;
    color: #fff !important;
}
.bg-guinda {
    background-color: #611232 !important;
    color: #fff !important;
}

@media (max-width: 576px) {
    .input-group { flex-wrap: wrap; }
    .input-group-text { width: 100%; justify-content: flex-start; }
}
</style>
@endpush

{{-- JS --}}
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAddress = document.getElementById('address_id');
    const newAddressFields = document.getElementById('new-address-fields');
    const form = document.querySelector('form');

    function toggleNewAddressFields() {
        if(selectAddress && newAddressFields) {
            newAddressFields.style.display = (selectAddress.value === '') ? 'flex' : 'none';
        }
    }

    if(selectAddress) {
        selectAddress.addEventListener('change', toggleNewAddressFields);
        toggleNewAddressFields();
    }

    // Validación JS
    if(form) {
        form.addEventListener('submit', function(e) {
            let missingFields = [];
            const areacve = document.getElementById('areacve');
            const areanom = document.getElementById('areanom');
            const tipo = document.getElementById('tipo');
            const unit_id = document.getElementById('unit_id');

            if (areacve && !areacve.value.trim()) missingFields.push('Clave del Departamento');
            if (areanom && !areanom.value.trim()) missingFields.push('Nombre del Departamento');
            if (tipo && !tipo.value) missingFields.push('Tipo');
            if (unit_id && !unit_id.value) missingFields.push('Unidad');

            if (missingFields.length > 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incompletos',
                    html: 'Debes completar los siguientes campos:<br><b>' + missingFields.join('<br>') + '</b>',
                    confirmButtonColor: '#611232'
                });
            }
        });
    }
});
</script>
@endpush
