@extends('layouts.admin') 

@section('title', 'Registrar Activo Informático')

@section('content')
<form action="{{ route('assets.store') }}" method="POST">
    @csrf

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">

                {{-- Card principal --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                    {{-- Cabecera --}}
                    <div class="card-header bg-guinda text-white py-3 px-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <div class="me-3 d-flex align-items-center justify-content-center rounded-3"
                                     style="width:42px;height:42px;background:rgba(255,255,255,.15);">
                                    <i class="fas fa-desktop"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5 mb-0">Registrar Nuevo Activo Informático</div>
                                    <small class="opacity-75">Completa los datos para dar de alta o reemplazar un activo</small>
                                </div>
                            </div>

                            <a href="{{ route('assets.index') }}"
                               class="btn btn-light btn-sm fw-semibold shadow-sm d-inline-flex align-items-center px-3">
                                <i class="fas fa-arrow-left me-2"></i> Volver
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-4">

                        {{-- Nota informativa --}}
                        <div class="alert alert-light border rounded-4 d-flex align-items-start gap-3 mb-4"
                             style="background:#fafafa;">
                            <div class="text-guinda fs-5 mt-1">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold mb-1">Recomendación</div>
                                <div class="text-muted small mb-0">
                                    Asegúrate de capturar correctamente <strong>TAG</strong> y <strong>Número de Serie</strong>.
                                    Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                                </div>
                            </div>
                        </div>

                        {{-- MODO DE REGISTRO --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <label for="modo_registro" class="form-label fw-semibold mb-0">
                                    <i class="fas fa-exchange-alt me-1 text-guinda"></i> Tipo de Registro <span class="text-danger">*</span>
                                </label>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i> Controlado por sistema
                                </small>
                            </div>

                            <select
                                id="modo_registro"
                                name="modo_registro"
                                class="form-select"
                                required
                            >
                                <option value="ALTA" {{ old('modo_registro','ALTA') === 'ALTA' ? 'selected' : '' }}>
                                    Alta de activo nuevo
                                </option>

                                <option value="REEMPLAZO" {{ old('modo_registro') === 'REEMPLAZO' ? 'selected' : '' }}>
                                    Reemplazo de activo existente (por TAG y Tipo)
                                </option>
                            </select>

                            <div class="mt-2 small text-muted d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                El reemplazo dará de baja automáticamente el activo anterior.
                            </div>
                        </div>

                        {{-- Sección: Información del Activo --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-info-circle me-2 text-guinda"></i> Información del Activo
                            </h6>
                            <span class="badge rounded-pill bg-light text-muted border">
                                Datos generales
                            </span>
                        </div>

                        <div class="row g-3 mb-4">

                            {{-- TAG --}}
                            <div class="col-md-6">
                                <label for="tag" class="form-label fw-semibold">
                                    <i class="fas fa-barcode me-1 text-guinda"></i> Tag / DICO <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="tag" 
                                    name="tag" 
                                    class="form-control @error('tag') is-invalid @enderror"
                                    value="{{ old('tag') }}" 
                                    placeholder="Ej: A123"
                                    required
                                >
                                @error('tag')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- SERIE --}}
                            <div class="col-md-6">
                                <label for="serie" class="form-label fw-semibold">
                                    <i class="fas fa-sort-numeric-up me-1 text-guinda"></i> Número de Serie <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="serie" 
                                    name="serie" 
                                    class="form-control @error('serie') is-invalid @enderror"
                                    value="{{ old('serie') }}" 
                                    placeholder="Ej: S456"
                                    required
                                >
                                @error('serie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- MARCA --}}
                            <div class="col-md-6">
                                <label for="marca" class="form-label fw-semibold">
                                    <i class="fas fa-industry me-1 text-guinda"></i> Marca
                                </label>
                                <input 
                                    type="text" 
                                    id="marca" 
                                    name="marca" 
                                    class="form-control @error('marca') is-invalid @enderror"
                                    value="{{ old('marca') }}" 
                                    placeholder="Ej: HP"
                                >
                                @error('marca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- MODELO --}}
                            <div class="col-md-6">
                                <label for="modelo" class="form-label fw-semibold">
                                    <i class="fas fa-laptop me-1 text-guinda"></i> Modelo
                                </label>
                                <input 
                                    type="text" 
                                    id="modelo" 
                                    name="modelo" 
                                    class="form-control @error('modelo') is-invalid @enderror"
                                    value="{{ old('modelo') }}" 
                                    placeholder="Ej: ProBook 450"
                                >
                                @error('modelo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>{{-- row --}}

                        {{-- Sección: Proveedor y Tipo --}}
                        <div class="rounded-4 border p-3 p-md-4 mb-4" style="background:#fcfcfc;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <h6 class="fw-bold mb-0">
                                    <i class="fas fa-truck me-2 text-guinda"></i> Proveedor y Tipo de Equipo
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-asterisk me-1"></i> Campos obligatorios
                                </small>
                            </div>

                            <div class="row g-3">

                                {{-- PROVEEDOR --}}
                                <div class="col-md-6">
                                    <label for="supplier_id" class="form-label fw-semibold">
                                        <i class="fas fa-truck me-1 text-guinda"></i> Proveedor <span class="text-danger">*</span>
                                    </label>
                                    <select 
                                        id="supplier_id" 
                                        name="supplier_id" 
                                        class="form-select @error('supplier_id') is-invalid @enderror"
                                        required
                                    >
                                        <option value="">-- Selecciona un proveedor --</option>
                                        @foreach ($suppliers as $sup)
                                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                                {{ $sup->prvnombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- TIPO DE EQUIPO --}}
                                <div class="col-md-6">
                                    <label for="device_type_id" class="form-label fw-semibold">
                                        <i class="fas fa-desktop me-1 text-guinda"></i> Tipo de Equipo <span class="text-danger">*</span>
                                    </label>
                                    <select 
                                        id="device_type_id" 
                                        name="device_type_id" 
                                        class="form-select @error('device_type_id') is-invalid @enderror"
                                        required
                                    >
                                        <option value="">-- Selecciona un tipo --</option>
                                        @foreach ($deviceTypes as $type)
                                            <option value="{{ $type->id }}" data-requires-ip="{{ $type->requires_ip }}" {{ old('device_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->equipo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('device_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- DIRECCION IP --}}
                                <div class="col-md-6 d-none" id="ip_address_container">
                                    <label for="ip_address" class="form-label fw-semibold">
                                        <i class="fas fa-network-wired me-1 text-guinda"></i> Dirección IP <span class="text-danger">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="ip_address" 
                                        name="ip_address" 
                                        class="form-control @error('ip_address') is-invalid @enderror"
                                        value="{{ old('ip_address') }}" 
                                        placeholder="Ej: 192.168.1.50"
                                    >
                                    @error('ip_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>{{-- row --}}
                        </div>

                        {{-- Sección: Estado y Propiedad --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-toggle-on me-2 text-guinda"></i> Estado y Propiedad
                            </h6>
                            <span class="badge rounded-pill bg-light text-muted border">
                                Configuración
                            </span>
                        </div>

                        <div class="row g-3 mb-4">

                            {{-- ESTADO --}}
                            <div class="col-md-6">
                                <label for="estado" class="form-label fw-semibold">
                                    <i class="fas fa-info me-1 text-guinda"></i> Estado <span class="text-danger">*</span>
                                </label>
                                <select 
                                    id="estado" 
                                    name="estado" 
                                    class="form-select @error('estado') is-invalid @enderror"
                                    required
                                >
                                    <option value="RESGUARDADO" {{ old('estado') == 'RESGUARDADO' ? 'selected' : '' }}>RESGUARDADO</option>
                                    <option value="OTRO" {{ old('estado') == 'OTRO' ? 'selected' : '' }}>OTRO</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- PROPIEDAD --}}
                            <div class="col-md-6">
                                <label for="propiedad" class="form-label fw-semibold">
                                    <i class="fas fa-hand-holding me-1 text-guinda"></i> Propiedad <span class="text-danger">*</span>
                                </label>
                                <select 
                                    id="propiedad" 
                                    name="propiedad" 
                                    class="form-select @error('propiedad') is-invalid @enderror"
                                    required
                                >
                                    <option value="ALIMENTACION PARA EL BIENESTAR" 
                                        {{ old('propiedad') == 'ALIMENTACION PARA EL BIENESTAR' ? 'selected' : '' }}>
                                        ALIMENTACION PARA EL BIENESTAR
                                    </option>

                                    <option value="ARRENDADO" 
                                        {{ old('propiedad','ARRENDADO') == 'ARRENDADO' ? 'selected' : '' }}>
                                        ARRENDADO
                                    </option>

                                    <option value="PARTICULAR" {{ old('propiedad') == 'PARTICULAR' ? 'selected' : '' }}>
                                        PARTICULAR
                                    </option>

                                    <option value="OTRO" {{ old('propiedad') == 'OTRO' ? 'selected' : '' }}>
                                        OTRO
                                    </option>
                                </select>

                                @error('propiedad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>{{-- row --}}

                        {{-- Acciones --}}
                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 gap-sm-3 pt-2">
                            <button type="submit" class="btn btn-guinda px-4 px-md-5 py-2 shadow-sm d-inline-flex align-items-center justify-content-center">
                                <i class="fas fa-save me-2"></i> Guardar
                            </button>

                            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary px-4 px-md-5 py-2 shadow-sm d-inline-flex align-items-center justify-content-center">
                                <i class="fas fa-times me-2"></i> Cancelar
                            </a>
                        </div>

                    </div>{{-- card-body --}}
                </div>{{-- card --}}

            </div>
        </div>
    </div>

</form>
@endsection


@section('css')
    <link rel="stylesheet" href="{{ asset('css/assets.css') }}">
    <style>
        /* Solo visual (no afecta lógica) */
        .text-guinda { color: #611232 !important; }
        .bg-guinda { background: #611232 !important; }
        .form-label { margin-bottom: .35rem; }
        .form-control, .form-select { border-radius: .75rem; }
        .card { border-radius: 1.25rem; }
        .btn { border-radius: .75rem; }
    </style>
@stop


@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const tag = document.getElementById('tag');
    const serie = document.getElementById('serie');
    const supplier = document.getElementById('supplier_id');
    const deviceType = document.getElementById('device_type_id');
    const propiedad = document.getElementById('propiedad');
    const estado = document.getElementById('estado');
    const ipContainer = document.getElementById('ip_address_container');
    const ipInput = document.getElementById('ip_address');

    // Función para mostrar/ocultar IP
    function toggleIpField() {
        const selectedOption = deviceType.options[deviceType.selectedIndex];
        const requiresIp = selectedOption ? selectedOption.getAttribute('data-requires-ip') == '1' : false;
        const text = selectedOption ? selectedOption.text.toLowerCase() : '';

        if (requiresIp || text.includes('impresora')) {
            ipContainer.classList.remove('d-none');
            ipInput.setAttribute('required', 'required');
        } else {
            ipContainer.classList.add('d-none');
            ipInput.removeAttribute('required');
            ipInput.value = ''; // Limpiar valor si se oculta
        }
    }

    // Listener para cambio de tipo
    deviceType.addEventListener('change', toggleIpField);
    
    // Ejecutar al inicio (por si hay old data)
    toggleIpField();

    // ❗ Estado por defecto RESGUARDADO
    if (!estado.value) {
        estado.value = 'RESGUARDADO';
    }

    // Cambiar propiedad según proveedor
    supplier.addEventListener('change', function() {
        const proveedor = supplier.options[supplier.selectedIndex].text;
        propiedad.value = (proveedor === "ALIMENTACION PARA EL BIENESTAR")
            ? 'ALIMENTACION PARA EL BIENESTAR'
            : 'ARRENDADO';
    });

    // Validación visual con SweetAlert
    form.addEventListener('submit', function(e) {

        let missing = [];

        if (!tag.value.trim())   missing.push('TAG / DICO');
        if (!serie.value.trim()) missing.push('Número de Serie');
        if (!supplier.value)     missing.push('Proveedor');
        if (!deviceType.value)   missing.push('Tipo de Equipo');

        if (missing.length > 0) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Campos obligatorios',
                html: 'Debes completar:<br><strong>' + missing.join('<br>') + '</strong>',
                confirmButtonColor: '#611232'
            });
        }
    });
});
</script>
@endsection
