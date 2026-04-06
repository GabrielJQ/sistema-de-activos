<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSmiabToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $now = now()->timestamp;
        $expiresAt = session('smiab_expires_at', 0);
        
        // Refrescar si: 
        // 1. No hay access_token en sesión
        // 2. O el token está por expirar (margen de 5 minutos)
        $needsRefresh = !session()->has('smiab_access_token') || ($expiresAt - $now < 300);

        if (auth()->check() && $needsRefresh && !empty($user->smiab_refresh_token)) {
            try {
                $supabaseService = app(\App\Services\SupabaseAuthService::class);
                $tokens = $supabaseService->refreshToken($user->smiab_refresh_token);

                // Si tiene éxito: Actualiza sesión y base de datos
                session([
                    'smiab_access_token' => $tokens['access_token'],
                    'smiab_expires_at' => now()->addSeconds($tokens['expires_in'])->timestamp
                ]);

                $user->update([
                    'smiab_refresh_token' => $tokens['refresh_token']
                ]);
            }
            catch (\Exception $e) {
                // Solo invalidamos el refresh_token si Supabase confirma que no es válido (ej: revocado)
                // Ignoramos errores temporales de red para no obligar al usuario a re-loguearse innecesariamente
                if (str_contains($e->getMessage(), 'invalid_grant') || str_contains($e->getMessage(), 'Refresh Token Error')) {
                    \Log::warning("SMIAB Refresh Token invalidado para usuario {$user->id}: " . $e->getMessage());
                    
                    $user->update(['smiab_refresh_token' => null]);
                    session()->forget(['smiab_access_token', 'smiab_expires_at']);

                    // CRÍTICO: Si el token de SMIAB muere irrevocablemente, debemos cerrar la sesión de SAI
                    // para romper el bucle de redirección y obligar al usuario a re-autenticarse con su password.
                    auth()->logout();
                    session()->invalidate();
                    session()->regenerateToken();

                    return redirect()->route('login')->with('error', 'Su sesión de SMIAB ha expirado totalmente. Por favor, ingrese de nuevo para sincronizar.');
                }
            }
        }

        return $next($request);
    }
}
