<section class="profile-section mb-5">

    <header class="section-header mb-3">
        <h2 class="section-title">
            <i class="fas fa-lock me-2 text-guinda"></i> Actualizar Contraseña
        </h2>
        <p class="section-subtext">
            Asegúrate de usar una contraseña larga y segura.
        </p>
    </header>

    <div class="card shadow-sm rounded-4 p-4">
        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label class="form-label fw-semibold">Contraseña actual</label>
                <input type="password" name="current_password"
                       class="form-control form-control-modern">
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="text-danger mt-1" />
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nueva contraseña</label>
                <input type="password" name="password"
                       class="form-control form-control-modern">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="text-danger mt-1" />
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Confirmar contraseña</label>
                <input type="password" name="password_confirmation"
                       class="form-control form-control-modern">
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="text-danger mt-1" />
            </div>

            <button class="btn btn-guinda px-4 py-2 mt-2">Guardar</button>

            @if (session('status') === 'password-updated')
                <span class="text-success ms-3 fw-semibold">Guardado.</span>
            @endif
        </form>
    </div>

</section>