@extends('layouts.admin')

@section('title', 'Perfil de Usuario')

@section('content_header')
<div class="d-flex align-items-center gap-2 mb-2">
    <span class="icon-circle icon-circle-guinda">
        <i class="fas fa-user-cog"></i>
    </span>
    <h1 class="fw-bold mb-0">Perfil de Usuario</h1>
</div>
<p class="text-muted mb-0">
    Administra tu información personal, credenciales y seguridad de la cuenta.
</p>
@stop

@section('content')

<div class="row g-4 mt-1">

    {{-- Información del Perfil --}}
    <div class="col-lg-6 col-md-12">
        <div class="profile-card h-100">
            <div class="profile-card-header bg-guinda">
                <i class="fas fa-id-card me-2"></i> Información del Perfil
            </div>

            <div class="profile-card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>

    {{-- Seguridad --}}
    <div class="col-lg-6 col-md-12">
        <div class="profile-card h-100">
            <div class="profile-card-header bg-warning-guinda">
                <i class="fas fa-lock me-2"></i> Seguridad de la Cuenta
            </div>

            <div class="profile-card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

</div>

{{-- Eliminar cuenta --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="profile-card danger-card">
            <div class="profile-card-header bg-danger-guinda">
                <i class="fas fa-user-slash me-2"></i> Eliminar Cuenta
            </div>

            <div class="profile-card-body">
                <p class="section-subtext mb-3">
                    Esta acción es <strong>irreversible</strong>. Se eliminarán permanentemente
                    todos tus datos y accesos al sistema.
                </p>

                <button
                    class="btn btn-danger-guinda"
                    x-data
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
                    <i class="fas fa-trash-alt me-1"></i> Eliminar Cuenta
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CONFIRMACIÓN --}}
<x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('profile.destroy') }}" class="p-4">
        @csrf
        @method('delete')

        <h2 class="section-title mb-1">Confirmar eliminación</h2>
        <p class="section-subtext mb-3">
            Ingresa tu contraseña para confirmar esta acción.
        </p>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Contraseña</label>
            <input id="password" name="password" type="password"
                   class="form-control form-control-modern">
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1 text-danger" />
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary-guinda" x-on:click="$dispatch('close')">
                Cancelar
            </button>
            <button class="btn btn-danger-guinda">
                Eliminar Cuenta
            </button>
        </div>
    </form>
</x-modal>

@endsection

@section('css')
<style>
:root{
    --guinda:#611232;
    --guinda-dark:#4b0f27;
    --warning:#f4c430;
    --danger:#b02a37;
    --border:#e5e7eb;
}

/* HEADER */
.icon-circle{
    width:42px;height:42px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
}
.icon-circle-guinda{
    background:rgba(97,18,50,.12);
    color:var(--guinda);
}

/* TARJETAS PERFIL */
.profile-card{
    background:#fff;
    border-radius:1.2rem;
    box-shadow:0 6px 18px rgba(0,0,0,.08);
    overflow:hidden;
}
.profile-card-header{
    padding:1rem 1.25rem;
    font-weight:700;
    color:#fff;
}
.profile-card-body{
    padding:1.5rem;
}

/* COLORES */
.bg-guinda{ background:var(--guinda); }
.bg-warning-guinda{ background:#9f7a00; }
.bg-danger-guinda{ background:var(--danger); }

.btn-guinda{
    background:var(--guinda);
    border-color:var(--guinda);
    color:#fff;
    border-radius:.7rem;
}
.btn-guinda:hover{
    background:var(--guinda-dark);
    border-color:var(--guinda-dark);
}

.btn-danger-guinda{
    background:var(--danger);
    border-color:var(--danger);
    color:#fff;
    border-radius:.7rem;
}
.btn-danger-guinda:hover{
    background:#8f1d28;
}

.btn-secondary-guinda{
    background:#6c757d;
    border-color:#6c757d;
    color:#fff;
    border-radius:.7rem;
}

/* FORMULARIOS */
.form-control-modern{
    border-radius:.6rem;
    border:1px solid #d1d5db;
    padding:.65rem .85rem;
}
.form-control-modern:focus{
    border-color:var(--guinda);
    box-shadow:0 0 0 .18rem rgba(97,18,50,.2);
}

/* TEXTOS */
.section-title{
    font-size:1.2rem;
    font-weight:800;
}
.section-subtext{
    font-size:.9rem;
    color:#6b7280;
}

/* TARJETA PELIGRO */
.danger-card{
    border-left:6px solid var(--danger);
}

/* RESPONSIVE */
@media(max-width:768px){
    .profile-card-body{ padding:1.25rem; }
}
</style>
@endsection
