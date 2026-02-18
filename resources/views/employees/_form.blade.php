@csrf
<div class="d-flex justify-content-center align-items-start py-4">
    <div class="card border-0 shadow-lg rounded-4 w-100" style="max-width: 980px;">

        {{-- Cabecera --}}
        <div class="card-header bg-guinda text-white fw-semibold fs-5 text-center rounded-top-4 py-3">
            <i class="fas fa-user me-2"></i>
            {{ isset($employee) ? 'Editar Empleado' : 'Registrar Nuevo Empleado' }}
        </div>

        <div class="card-body px-4 px-md-5 py-4">

            {{-- ================= INFORMACIÓN PERSONAL ================= --}}
            <div class="mb-4">
                <h6 class="section-title">
                    <i class="fas fa-user"></i> Información del Personal
                </h6>

                <div class="row g-3">
                    <x-input-field label="Expediente" name="expediente" icon="id-card"
                        placeholder="Ej: 12345"
                        :value="old('expediente', $employee->expediente ?? '')"/>

                    <x-input-field label="Nombre" name="nombre" icon="user"
                        placeholder="Ej: Juan"
                        :value="old('nombre', $employee->nombre ?? '')"/>

                    <x-input-field label="Apellido Paterno" name="apellido_pat" icon="user-tag"
                        placeholder="Ej: Pérez"
                        :value="old('apellido_pat', $employee->apellido_pat ?? '')"/>

                    <x-input-field label="Apellido Materno" name="apellido_mat" icon="user-tag"
                        placeholder="Ej: López"
                        :value="old('apellido_mat', $employee->apellido_mat ?? '')"/>

                    <x-input-field label="CURP" name="curp" icon="id-badge"
                        placeholder="Ej: PELJ800101HDFRRN05"
                        :value="old('curp', $employee->curp ?? '')"/>

                    <x-input-field label="Correo Electrónico" name="email" icon="envelope"
                        placeholder="Ej: juan.perez@email.com"
                        :value="old('email', $employee->email ?? '')"/>
                </div>
            </div>

            {{-- ================= DEPARTAMENTO Y PUESTO ================= --}}
            <div class="mb-4 section-box">
                <h6 class="section-title">
                    <i class="fas fa-building"></i> Departamento y Puesto
                </h6>

                <div class="row g-3">
                    <x-select-field
                        label="Departamento"
                        name="department_id"
                        icon="building"
                        :options="$departments->pluck('areanom','id')->toArray()"
                        :selected="old('department_id', $employee->department_id ?? '')"/>

                    <x-input-field label="Puesto" name="puesto" icon="briefcase"
                        placeholder="Ej: Analista de Sistemas"
                        :value="old('puesto', $employee->puesto ?? '')"/>
                </div>
            </div>

            {{-- ================= ESTATUS ================= --}}
            <div class="mb-4">
                <h6 class="section-title">
                    <i class="fas fa-id-badge"></i> Estatus del Personal
                </h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="tipo" class="form-label fw-semibold">Tipo</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-users-cog"></i>
                            </span>
                            <select name="tipo" id="tipo" class="form-select">
                                <option value="">-- Seleccionar tipo --</option>
                                @foreach(['Sindicalizado','Confianza','Eventual','Honorarios','Otro'] as $tipo)
                                    <option value="{{ $tipo }}"
                                        {{ old('tipo', $employee->tipo ?? '') === $tipo ? 'selected' : '' }}>
                                        {{ $tipo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <x-select-field
                        label="Estado"
                        name="status"
                        icon="toggle-on"
                        :options="['Activo'=>'Activo','Inactivo'=>'Inactivo']"
                        :selected="old('status', $employee->status ?? '')"/>
                </div>
            </div>

            {{-- ================= CONTACTO ================= --}}
            <div class="mb-4 section-box">
                <h6 class="section-title">
                    <i class="fas fa-phone"></i> Contacto
                </h6>

                <div class="row g-3">
                    <x-input-field label="Teléfono" name="telefono" icon="phone"
                        placeholder="Ej: 555-123-4567"
                        :value="old('telefono', $employee->telefono ?? '')"/>

                    <x-input-field label="Extensión" name="extension" icon="phone-square"
                        placeholder="Ej: 123"
                        :value="old('extension', $employee->extension ?? '')"/>
                </div>
            </div>

            {{-- ================= BOTONES ================= --}}
            <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                <button type="submit" class="btn btn-guinda px-5 py-2">
                    <i class="fas fa-save me-1"></i> Guardar
                </button>

                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary px-5 py-2">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
            </div>

        </div>
    </div>
</div>

@section('css')
<style>
:root {
    --guinda: #611232;
    --guinda-dark: #8c1f48;
}

/* Colores */
.bg-guinda { background-color: var(--guinda) !important; }
.text-guinda { color: var(--guinda) !important; }

/* Secciones */
.section-title {
    font-weight: 600;
    color: var(--guinda);
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
    border-left: 4px solid var(--guinda);
    padding-left: .6rem;
    margin-bottom: 1.2rem;
}

/* Inputs */
.form-control, .form-select {
    border-radius: .6rem !important;
    border: 1.6px solid #d0d0d0 !important;
    padding: .55rem .75rem !important;
    transition: .25s ease;
}
.form-control:focus, .form-select:focus {
    border-color: var(--guinda) !important;
    box-shadow: 0 0 0 .2rem rgba(97,18,50,.2);
}

/* Input group */
.input-group-text {
    border-radius: .6rem 0 0 .6rem;
    background: #f4f4f4;
}

/* Botones */
.btn-guinda {
    background: var(--guinda);
    color: #fff;
    border-radius: .55rem;
    border: 1px solid var(--guinda);
    transition: .3s ease;
}
.btn-guinda:hover {
    background: var(--guinda-dark);
    box-shadow: 0 4px 12px rgba(97,18,50,.35);
    transform: translateY(-1px);
}

.btn-outline-secondary {
    border-radius: .55rem;
    transition: .3s ease;
}

/* Card */
.card {
    border-radius: 1rem !important;
}
.card-body {
    background: #fafafa;
}

/* Responsive */
@media (max-width: 576px) {
    .btn-guinda,
    .btn-outline-secondary {
        width: 100%;
    }
}

</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const expediente = document.getElementById('expediente');
    const nombre = document.getElementById('nombre');
    const departamento = document.getElementById('department_id');

    form.addEventListener('submit', function(e) {
        let missingFields = [];
        if (!expediente.value.trim()) missingFields.push('Expediente');
        if (!nombre.value.trim()) missingFields.push('Nombre');
        if (!departamento.value) missingFields.push('Departamento');

        if (missingFields.length > 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Debes completar: ' + missingFields.join(', '),
                confirmButtonColor: '#611232'
            });
        }
    });
});
</script>
@endsection
