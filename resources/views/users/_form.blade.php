@csrf
<div class="d-flex justify-content-center align-items-start py-4">
    <div class="card border-0 rounded-4 w-100 modern-card shadow-sm" style="max-width: 920px;">

        {{-- Cabecera --}}
        <div class="card-header bg-guinda text-white fw-bold fs-5 text-center rounded-top py-3">
            <div class="d-flex align-items-center justify-content-center gap-2">
                <span class="header-icon">
                    <i class="fas fa-user-shield"></i>
                </span>
                <span>{{ isset($user) ? 'Editar Usuario' : 'Nuevo Usuario' }}</span>
            </div>
        </div>

        <div class="card-body px-4 px-md-5 py-4">

            {{-- Información del Usuario --}}
            <div class="section-block">
                <h6 class="section-title mt-1">
                    <i class="fas fa-user me-2"></i>Información del Usuario
                </h6>

                <div class="row g-4 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre</label>
                        <div class="input-group modern-input">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Ej: Juan Pérez"
                                   value="{{ old('name', $user->name ?? '') }}" required>
                        </div>
                        <div class="form-hint">Nombre completo del usuario.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Correo Electrónico</label>
                        <div class="input-group modern-input">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control"
                                   placeholder="usuario@email.com"
                                   value="{{ old('email', $user->email ?? '') }}" required>
                        </div>
                        <div class="form-hint">Este correo se usará para el acceso al sistema.</div>
                    </div>
                </div>
            </div>

            {{-- Rol de Usuario --}}
            <div class="section-block">
                <h6 class="section-title">
                    <i class="fas fa-user-tag me-2"></i>Rol del Usuario
                </h6>

                <div class="row g-4 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Seleccionar Rol</label>
                        <select name="role" class="form-select modern-select" required>
                            <option value="">-- Seleccione un rol --</option>
                            <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="collaborator" {{ old('role', $user->role ?? '') == 'collaborator' ? 'selected' : '' }}>Colaborador</option>
                            <option value="visitor" {{ old('role', $user->role ?? '') == 'visitor' ? 'selected' : '' }}>Visitante</option>
                        </select>
                        <div class="form-hint">Define el nivel de acceso del usuario.</div>
                    </div>
                </div>
            </div>

            {{-- Región y Unidad --}}
            @if(auth()->user()->isSuperAdmin())
                <div class="section-block">
                    <h6 class="section-title">
                        <i class="fas fa-map me-2"></i>Ubicación del Usuario
                    </h6>

                    <div class="row g-4 mb-3">

                        {{-- Región --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Región</label>
                            <select name="region_id" id="region_id" class="form-select modern-select" required>
                                <option value="">-- Seleccione región --</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}"
                                        {{ old('region_id', $user->region_id ?? '') == $region->id ? 'selected' : '' }}>
                                        {{ $region->regnom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">Selecciona la región para filtrar unidades.</div>
                        </div>

                        {{-- Unidad --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unidad</label>
                            <select name="unit_id" id="unit_id" class="form-select modern-select" required>
                                <option value="">-- Seleccione unidad --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        data-region="{{ $unit->region_id }}"
                                        {{ old('unit_id', $user->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->uninom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">Solo se mostrarán unidades de la región elegida.</div>
                        </div>
                    </div>

                    {{-- Script filtro — NO ALTERADO --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const regionSelect = document.getElementById('region_id');
                            const unitSelect = document.getElementById('unit_id');

                            function filterUnits() {
                                const region = regionSelect.value;

                                [...unitSelect.options].forEach(opt => {
                                    if (!opt.value) return;
                                    opt.style.display = opt.dataset.region == region ? 'block' : 'none';
                                });
                            }

                            regionSelect.addEventListener('change', filterUnits);
                            filterUnits();
                        });
                    </script>
                </div>
            @endif

            {{-- Seguridad --}}
            <div class="section-block">
                <div class="security-head">
                    <h6 class="section-title mb-0">
                        <i class="fas fa-lock me-2"></i>Seguridad
                    </h6>
                    <span class="security-chip">
                        <i class="fas fa-shield-alt me-1"></i> Contraseña
                    </span>
                </div>

                <div class="row g-4 mb-2 mt-2">

                    {{-- Password --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contraseña</label>
                        <div class="input-group modern-input">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" id="password" name="password" class="form-control"
                                   placeholder="{{ isset($user) ? 'Dejar vacío para mantener actual' : 'Crea una contraseña' }}"
                                   {{ isset($user) ? '' : 'required' }}>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-hint">
                            {{ isset($user) ? 'Si no deseas cambiarla, deja este campo vacío.' : 'Usa una combinación de letras, números y símbolos.' }}
                        </div>
                    </div>

                    {{-- Confirm --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirmar Contraseña</label>
                        <div class="input-group modern-input">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                                   placeholder="{{ isset($user) ? 'Dejar vacío para mantener actual' : 'Confirmar contraseña' }}"
                                   {{ isset($user) ? '' : 'required' }}>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-hint">Repite la contraseña para evitar errores.</div>
                    </div>

                    {{-- Strength meter (mismo ID y lógica) --}}
                    <div class="col-12">
                        <div class="strength-wrap">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted fw-semibold">Fortaleza</small>
                                <small id="passwordStrengthText" class="text-muted"></small>
                            </div>
                            <div class="progress" style="height: 7px;">
                                <div id="passwordStrengthBar" class="progress-bar"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Botones --}}
            <div class="d-flex flex-column flex-sm-row justify-content-end mt-4 gap-2 gap-sm-3">
                <button type="submit" class="btn btn-guinda px-5 py-2 shadow-sm">
                    <i class="fas fa-save me-1"></i> Guardar
                </button>

                <a href="{{ route('users.index') }}" class="btn btn-secondary px-5 py-2 shadow-sm">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
            </div>

        </div>
    </div>
</div>

@section('css')
<style>
:root{
    --guinda:#611232;
    --guinda-2:#4a0f25;
    --border:#c1bfc0;
    --soft:#f6f7f9;
}

/* CARD */
.modern-card{
    border-radius: 1.2rem !important;
    background:#fff;
}

/* Header icon */
.header-icon{
    width: 38px;
    height: 38px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,.18);
}

/* TITULOS */
.section-block{
    padding: 1.15rem 1.15rem;
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 1rem;
    background: #fff;
    margin-bottom: 1rem;
}

.section-title{
    font-weight: 800;
    color: var(--guinda);
    font-size: 1.05rem;
    margin-bottom: 1rem;
    padding-bottom: .45rem;
    border-bottom: 2px solid rgba(0,0,0,.08);
}

/* Hints */
.form-hint{
    font-size: .78rem;
    color: #6c757d;
    margin-top: .4rem;
}

/* INPUTS */
.modern-input .form-control,
.modern-select{
    border-radius: 0.65rem !important;
    padding: 0.7rem 0.85rem !important;
    border: 1.6px solid var(--border) !important;
    background-color: #fff;
    transition: .2s ease-in-out;
}

.modern-input .form-control:focus,
.modern-select:focus{
    border-color: var(--guinda) !important;
    box-shadow: 0 0 0 .18rem rgba(97,18,50,.18);
}

/* INPUT GROUP ICONS */
.input-group-text{
    background-color: #f4f2f2;
    border: 1.6px solid var(--border);
    border-right: none;
    border-radius: .65rem 0 0 .65rem;
}

/* TOGGLE PASSWORD */
.toggle-password{
    border: 1.6px solid var(--border);
    border-left: none;
    background-color: #f4f2f2;
    border-radius: 0 .65rem .65rem 0;
}
.toggle-password:hover{
    background-color: #e8e6e6;
}

/* GUINDA */
.bg-guinda{
    background-color: var(--guinda) !important;
    color:#fff !important;
}
.card-header.bg-guinda{
    background-color: var(--guinda) !important;
    color:#fff !important;
}
.btn-guinda{
    background-color: var(--guinda);
    border-color: var(--guinda);
    color:#fff;
    border-radius: .85rem;
    transition: .2s ease;
}
.btn-guinda:hover{
    background-color: var(--guinda-2);
    border-color: var(--guinda-2);
    color:#fff;
    transform: translateY(-1px);
}

/* Seguridad header */
.security-head{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: .35rem;
}
.security-chip{
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .6rem;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 700;
    color: var(--guinda);
    background: rgba(97,18,50,.08);
}

/* Strength wrap */
.strength-wrap{
    border: 1px dashed rgba(0,0,0,.15);
    background: rgba(0,0,0,.02);
    border-radius: .85rem;
    padding: .9rem 1rem;
}

/* Responsive: compacta bloques en móvil */
@media (max-width: 576px){
    .section-block{ padding: 1rem; }
    .section-title{ font-size: 1rem; }
}
</style>
@stop

@section('js')
<script>
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.target);
        const icon = button.querySelector('i');
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.querySelector('#password');
    if (!passwordInput) return;

    const bar = document.getElementById('passwordStrengthBar');
    const text = document.getElementById('passwordStrengthText');

    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        let score = 0;

        if (val.length >= 6) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { text: 'Muy débil', class: 'bg-danger', width: '25%' },
            { text: 'Débil', class: 'bg-warning', width: '50%' },
            { text: 'Aceptable', class: 'bg-info', width: '75%' },
            { text: 'Fuerte', class: 'bg-success', width: '100%' },
        ];

        if (val.length === 0) {
            bar.style.width = '0%';
            bar.className = 'progress-bar';
            text.textContent = '';
            return;
        }

        const level = levels[Math.min(score - 1, 3)];
        bar.style.width = level.width;
        bar.className = 'progress-bar ' + level.class;
        text.textContent = `Seguridad: ${level.text}`;
    });
});
</script>
@stop
