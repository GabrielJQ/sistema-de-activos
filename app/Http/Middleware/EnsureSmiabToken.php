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
        if (auth()->check() && !session()->has('smiab_access_token') && !empty(auth()->user()->smiab_refresh_token)) {
            try {
                $supabaseService = app(\App\Services\SupabaseAuthService::class);
                $tokens = $supabaseService->refreshToken(auth()->user()->smiab_refresh_token);

                // Si tiene éxito: Guarda el nuevo access_token en la sesión y actualiza el nuevo refresh_token en la BD
                session(['smiab_access_token' => $tokens['access_token']]);

                auth()->user()->update([
                    'smiab_refresh_token' => $tokens['refresh_token']
                ]);
            }
            catch (\Exception $e) {
                // Si falla (token caducado o inválido): Pon el smiab_refresh_token de la BD en null para no ciclarse
                auth()->user()->update(['smiab_refresh_token' => null]);
            }
        }

        return $next($request);
    }
}
