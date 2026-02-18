@extends('layouts.admin')

@section('title', 'Editar Activo Informático')

@section('content')
<form action="{{ route('assets.update', $asset->id) }}" method="POST">
    @method('PUT')
    @csrf

    <div class="container-fluid py-4 d-flex justify-content-center">
        <div class="card border-0 shadow-soft rounded-4 w-100" style="max-width: 1000px;">

            {{-- Header --}}
            <div class="card-header bg-guinda text-white rounded-top-4 py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-circle bg-white text-guinda">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Editar Activo Informático</h5>
                        <small class="opacity-75">
                            Actualiza la información general, estado y propiedad del activo
                        </small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 bg-light-soft">

                {{-- ================= INFORMACIÓN DEL ACTIVO ================= --}}
                <div class="section-block mb-4">
                    <h6 class="section-title">
                        <i class="fas fa-info-circle"></i> Información del Activo
                    </h6>

                    <div class="row g-3">
                        <x-input-field
                            label="Tag / DICO"
                            name="tag"
                            icon="barcode"
                            placeholder="Ej: A123"
                            :value="old('tag', $asset->tag)"
                        />

                        <x-input-field
                            label="Número de Serie"
                            name="serie"
                            icon="sort-numeric-up"
                            placeholder="Ej: S456"
                            :value="old('serie', $asset->serie)"
                        />

                        <x-input-field
                            label="Marca"
                            name="marca"
                            icon="industry"
                            placeholder="Ej: HP"
                            :value="old('marca', $asset->marca)"
                        />

                        <x-input-field
                            label="Modelo"
                            name="modelo"
                            icon="laptop"
                            placeholder="Ej: ProBook 450"
                            :value="old('modelo', $asset->modelo)"
                        />
                    </div>
                </div>

                {{-- ================= PROVEEDOR Y TIPO ================= --}}
                <div class="section-block mb-4">
                    <h6 class="section-title">
                        <i class="fas fa-truck"></i> Proveedor y Tipo de Equipo
                    </h6>

                    <div class="row g-3">
                        <x-select-field
                            label="Proveedor"
                            name="supplier_id"
                            icon="truck"
                            :options="$suppliers->pluck('prvnombre','id')->toArray()"
                            :selected="old('supplier_id', $asset->supplier_id)"
                        />

                        <x-select-field
                            label="Tipo de Equipo"
                            name="device_type_id"
                            icon="desktop"
                            :options="$deviceTypes->pluck('equipo','id')->toArray()"
                            :selected="old('device_type_id', $asset->device_type_id)"
                        />

                        {{-- DIRECCION IP --}}
                        <div class="col-md-6 d-none" id="ip_address_container">
                            <x-input-field
                                label="Dirección IP"
                                name="ip_address"
                                id="ip_address"
                                icon="network-wired"
                                placeholder="Ej: 192.168.1.50"
                                :value="old('ip_address', $asset->networkInterface?->ip_address)"
                            />
                        </div>
                    </div>
                </div>

                {{-- ================= ESTADO Y PROPIEDAD ================= --}}
                <div class="section-block mb-4">
                    <h6 class="section-title">
                        <i class="fas fa-toggle-on"></i> Estado y Propiedad
                    </h6>

                    <div class="row g-3">

                        @php
                            $currentState = $asset->estado ?? 'OPERACION';
                            $ultimoEstado = session('estado_previo_'.$asset->id) ?? null;

                            $estados = [
                                'OPERACION'   => 'OPERACION',
                                'GARANTIA'    => 'GARANTIA',
                                'SINIESTRO'   => 'SINIESTRO',
                                'RESGUARDADO' => 'RESGUARDADO',
                                'DANADO'      => 'DANADO',
                                'BAJA'        => 'BAJA',
                                'OTRO'        => 'OTRO'
                            ];

                            if ($currentState === 'OPERACION') unset($estados['RESGUARDADO']);
                            if ($currentState === 'RESGUARDADO') unset($estados['OPERACION']);
                            if ($currentState === 'GARANTIA') $estados = ['OPERACION' => 'OPERACION'];
                            if ($currentState === 'SINIESTRO') $estados = ['SINIESTRO' => 'SINIESTRO'];
                            if ($currentState === 'BAJA') $estados = ['BAJA' => 'BAJA'];

                            if ($currentState === 'DANADO') {
                                if ($ultimoEstado === 'OPERACION') {
                                    $estados = ['DANADO' => 'DANADO', 'OPERACION' => 'OPERACION'];
                                }
                                if ($ultimoEstado === 'RESGUARDADO') {
                                    $estados = ['DANADO' => 'DANADO', 'RESGUARDADO' => 'RESGUARDADO'];
                                }
                            }
                        @endphp

                        <x-select-field
                            label="Estado"
                            name="estado"
                            id="estado"
                            icon="info"
                            :options="$estados"
                            :selected="$currentState"
                        />

                        <x-select-field
                            label="Propiedad"
                            name="propiedad"
                            id="propiedad"
                            icon="hand-holding"
                            :options="[
                                'ALIMENTACION PARA EL BIENESTAR'=>'ALIMENTACION PARA EL BIENESTAR',
                                'ARRENDADO'=>'ARRENDADO',
                                'PARTICULAR'=>'PARTICULAR',
                                'OTRO'=>'OTRO'
                            ]"
                            :selected="old('propiedad', $asset->propiedad)"
                        />
                    </div>
                </div>

                {{-- ================= ACCIONES ================= --}}
                <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                    <button type="submit" class="btn btn-guinda px-5">
                        <i class="fas fa-save me-1"></i> Guardar Cambios
                    </button>

                    <a href="{{ route('assets.group', $asset->tag) }}" class="btn btn-outline-secondary px-5">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>

            </div>
        </div>
    </div>
</form>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/assets.css') }}">
<style>
/* Paleta */
.bg-guinda { background-color:#611232!important; }
.text-guinda { color:#611232!important; }
.bg-light-soft { background:#fafafa; }

/* Sombras */
.shadow-soft { box-shadow:0 6px 16px rgba(0,0,0,.08); }

/* Header icon */
.icon-circle {
    width:42px;
    height:42px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    font-size:1.1rem;
}

/* Secciones */
.section-block {
    background:#fff;
    border-radius:1rem;
    padding:1.25rem;
    box-shadow:0 4px 12px rgba(0,0,0,.06);
}
.section-title {
    font-weight:600;
    color:#611232;
    font-size:1.1rem;
    margin-bottom:1rem;
    display:flex;
    align-items:center;
    gap:.5rem;
    border-left:4px solid #611232;
    padding-left:.6rem;
}

/* Botones */
.btn-guinda {
    background:#611232;
    color:#fff;
    border:1px solid #611232;
    font-weight:600;
}
.btn-guinda:hover {
    background:#4b0f27;
    border-color:#4b0f27;
}
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

    // Map of device types to requires_ip
    const deviceTypesMap = @json($deviceTypes->pluck('requires_ip', 'id'));

    function toggleIpField() {
        const selectedId = deviceType.value;
        const requiresIp = deviceTypesMap[selectedId] == 1;
        const selectedOption = deviceType.options[deviceType.selectedIndex];
        const text = selectedOption ? selectedOption.text.toLowerCase() : '';

        if (requiresIp || text.includes('impresora')) {
            ipContainer.classList.remove('d-none');
            // Add required attribute if visible? 
            // The x-input-field might not pass attributes easily to the inner input via JS if we target the wrapper.
            // But let's assume ipInput is the input element itself (id="ip_address").
            if(ipInput) ipInput.setAttribute('required', 'required');
        } else {
            ipContainer.classList.add('d-none');
            if(ipInput) {
                ipInput.removeAttribute('required');
                ipInput.value = ''; 
            }
        }
    }

    deviceType.addEventListener('change', toggleIpField);
    toggleIpField();

    // Si no hay estado, establecer OPERACION (solo en crear)
    if (!estado.value) {
        estado.value = 'OPERACION';
    }

    // Cambiar propiedad según proveedor
    supplier.addEventListener('change', function() {
        const proveedor = supplier.options[supplier.selectedIndex].text;
        if (proveedor === "ALIMENTACION PARA EL BIENESTAR") {
            propiedad.value = 'ALIMENTACION PARA EL BIENESTAR';
        } else {
            propiedad.value = 'ARRENDADO';
        }
    });

    // Validación del formulario
    form.addEventListener('submit', function(e) {
        if (!tag.value.trim()) {
            let missing = [];

            if (!serie.value.trim()) missing.push('Número de Serie');
            if (!supplier.value) missing.push('Proveedor');
            if (!deviceType.value) missing.push('Tipo de Equipo');

            if (missing.length > 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incompletos',
                    text: 'Debes completar: ' + missing.join(', '),
                    confirmButtonColor: '#611232'
                });
            }
        }
    });
});
</script>
@endsection
