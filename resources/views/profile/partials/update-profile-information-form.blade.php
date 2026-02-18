<section class="profile-section mb-5">

    <header class="section-header mb-3">
        <h2 class="section-title">
            <i class="fas fa-id-card me-2 text-guinda"></i> Información del Perfil
        </h2>
        <p class="section-subtext">
            Actualiza tus datos personales y correo electrónico.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <div class="card shadow-sm rounded-4 p-4">
        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" id="name" name="name"
                       class="form-control form-control-modern"
                       value="{{ old('name', $user->name) }}">
                <x-input-error :messages="$errors->get('name')" class="text-danger mt-1" />
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       class="form-control form-control-modern"
                       value="{{ old('email', $user->email) }}">
                <x-input-error :messages="$errors->get('email')" class="text-danger mt-1" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <p class="mt-2 text-warning small">
                        Tu correo no ha sido verificado.
                        <button form="send-verification" class="btn btn-link p-0 ms-1 text-guinda fw-semibold">
                            Reenviar verificación
                        </button>
                    </p>
                @endif
            </div>

            <button class="btn btn-guinda px-4 py-2 mt-2">Guardar</button>

            @if (session('status') === 'profile-updated')
                <span class="text-success ms-3 fw-semibold">Guardado.</span>
            @endif
        </form>
    </div>

</section>
