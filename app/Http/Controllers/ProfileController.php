<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // Mostrar formulario de edición del perfil
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // Actualizar perfil del usuario
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Asignar datos validados
        $user->fill($request->validated());

        // Si cambia el email, invalidar verificación
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    // Eliminar cuenta del usuario
    public function destroy(Request $request): RedirectResponse
    {
        // Validar contraseña actual
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Eliminar usuario
        $user->delete();

        // Invalida sesión
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
