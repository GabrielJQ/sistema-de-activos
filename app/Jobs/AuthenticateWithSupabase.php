<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\User;
use App\Services\SupabaseAuthService;
use Illuminate\Support\Facades\Log;

class AuthenticateWithSupabase implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $password;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle(SupabaseAuthService $supabaseService): void
    {
        try {
            // Realizamos la petición pesada a Supabase en segundo plano
            $tokens = $supabaseService->login($this->user->email, $this->password);

            // Guardamos el refresh_token directamente en el usuario (BD)
            // No podemos guardar en sesión (\session()) desde un Job
            $this->user->update([
                'smiab_refresh_token' => $tokens['refresh_token']
            ]);

        }
        catch (\Exception $e) {
            // Si falla en segundo plano (ej. credenciales desincronizadas con Supabase o sin internet)
            // Solo logueamos el error. El usuario ya entró al sistema local exitosamente.
            // Cuando quiera entrar a "Impresoras", el middleware 'smiab.token' le pedirá re-autenticarse.
            Log::warning('Supabase background login failed for user: ' . $this->user->email . '. Error: ' . $e->getMessage());
        }
    }
}
