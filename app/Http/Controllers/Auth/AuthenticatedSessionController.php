<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }
/**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

// ==========================================
        // 2. CONEXIÓN A SUPABASE (SMIAB)
        // ==========================================
        try {
            $supabaseService = app(\App\Services\SupabaseAuthService::class);
            $tokens = $supabaseService->login($request->email, $request->password);

            // 1. Guardamos usando el objeto Request (más confiable)
            $request->session()->put('smiab_access_token', $tokens['access_token']);
            
            // 2. FORZAMOS el guardado de la sesión en este exacto milisegundo
            $request->session()->save();

            // 3. Guardamos el refresh token en la base de datos
            $request->user()->update([
                'smiab_refresh_token' => $tokens['refresh_token']
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al iniciar sesión en Supabase (SMIAB): ' . $e->getMessage());
        }
        // ==========================================

        // EL RETURN SE MUEVE HASTA AQUÍ ABAJO, AL FINAL DE TODO
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
