<section class="mb-5">

    <header>
        <h2 class="section-title">Eliminar Cuenta</h2>
        <p class="section-subtext">
            Una vez eliminada tu cuenta, todos los datos serán borrados permanentemente. 
            Asegúrate de descargar la información que necesites.
        </p>
    </header>

    <button 
        class="btn btn-danger-guinda mt-3"
        x-data
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Eliminar Cuenta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-4">
            @csrf
            @method('delete')

            <h2 class="section-title">¿Seguro que deseas eliminar tu cuenta?</h2>

            <p class="section-subtext mt-1">
                Ingresa tu contraseña para confirmar esta acción irreversible.
            </p>

            <div class="mt-4">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" name="password" type="password" class="form-control">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-danger" />
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary-guinda" x-on:click="$dispatch('close')">
                    Cancelar
                </button>

                <button class="btn btn-danger-guinda">
                    Eliminar Cuenta
                </button>
            </div>
        </form>
    </x-modal>

</section>
