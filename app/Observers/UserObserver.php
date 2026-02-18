<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Si ya tiene supabase_user_id (ej. creado por seeder), no hacer nada
        if ($user->supabase_user_id) {
            return;
        }

        try {
            $supabaseService = app(\App\Services\SupabaseAuthService::class);

            // Nota: Aquí usamos una contraseña por defecto o la que venga en el request si fuera accesible.
            // Dado que Laravel hashea el password antes de guardar, no podemos recuperar el texto plano fácilmente
            // a menos que lo pasemos explícitamente. Para este caso de uso (Dashboard), asumiremos una password temporal
            // o idealmente deberíamos interceptar esto antes del hash, pero el Observer corre "después".

            // WORKAROUND: Para usuarios creados desde Panel, usaremos una contraseña temporal conocida
            // o intentaremos obtenerla del request si está disponible.
            $password = request()->input('password', 'TempPass123!');

            $uuid = $supabaseService->createUser($user->email, $password, [
                'name' => $user->name,
                'role' => $user->role,
            ]);

            $user->supabase_user_id = $uuid;
            $user->saveQuietly(); // Evita loop infinito

        } catch (\Exception $e) {
            // Loguear error pero no interrumpir flujo local necesariamente, o lanzar excepción según preferencia
            \Illuminate\Support\Facades\Log::error("Error sync Supabase: " . $e->getMessage());
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if ($user->supabase_user_id) {
            try {
                $supabaseService = app(\App\Services\SupabaseAuthService::class);
                $supabaseService->deleteUser($user->supabase_user_id);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error deleting Supabase user: " . $e->getMessage());
            }
        }
    }
}
