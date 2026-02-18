<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        $user = DB::table('users')->where('email', $credentials['email'])->first();

        if ($user) {
            // Verificar si el usuario ya tiene sesión activa
            $activeSession = DB::table('sessions')
                ->where('user_id', $user->id)
                ->first();

            if ($activeSession) {
                // Sesión activa encontrada: bloquear login
                return back()->withErrors([
                    'email' => 'Este usuario ya tiene una sesión activa en otro dispositivo.'
                ])->onlyInput('email');
            }
        }

        if (Auth::attempt($credentials)) {
            // Autenticado correctamente
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();

        Auth::logout();

        // Eliminar la sesión actual
        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', $request->session()->getId())
            ->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
