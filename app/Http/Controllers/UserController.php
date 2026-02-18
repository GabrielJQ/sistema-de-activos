<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    // LISTA DE USUARIOS
    public function index()
    {
        $auth = auth()->user();

        $query = User::query();

        // SUPER ADMIN → puede ver todos
        if (!$auth->isSuperAdmin()) {

            // Filtrar por región
            $query->where('region_id', $auth->region_id);

            // Filtrar por unidad si aplica
            if ($auth->unit_id !== null) {
                $query->where('unit_id', $auth->unit_id);
            }

            // Un admin NO ve al super_admin
            $query->where('role', '!=', 'super_admin');
        }

        $users = $query->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * FORMULARIO DE CREACIÓN
     */
    public function create()
    {
        $regions = \App\Models\Region::all();
        $units = \App\Models\Unit::all();

        return view('users.create', compact('regions', 'units'));
    }

    /**
     * GUARDAR UN NUEVO USUARIO
     */
    public function store(UserRequest $request)
    {
        // Evitar más de un super admin
        if ($request->role === 'super_admin' && User::where('role', 'super_admin')->exists()) {
            return back()->with('error', 'Ya existe un Super Administrador.')->withInput();
        }

        $auth = auth()->user();
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        // Set region/unit based on auth user if not super admin
        if (!$auth->isSuperAdmin()) {
            $validated['region_id'] = $auth->region_id;
            $validated['unit_id'] = $auth->unit_id;
        }

        $user = User::create($validated);

        event(new Registered($user));

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * EDITAR USUARIO
     */
    public function edit(User $user)
    {
        $regions = \App\Models\Region::all();
        $units = \App\Models\Unit::all();

        return view('users.edit', compact('user', 'regions', 'units'));
    }

    /**
     * ACTUALIZAR
     */
    public function update(UserRequest $request, User $user)
    {
        $auth = auth()->user();

        // Restricción: solo super admin puede editar admins o super admins
        if (!$auth->isSuperAdmin() && in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('users.index')
                ->with('error', 'No tienes permiso para editar a este usuario.');
        }

        $validated = $request->validated();

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Solo super admin puede cambiar región y unidad
        if (!$auth->isSuperAdmin()) {
            unset($validated['region_id']);
            unset($validated['unit_id']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * ELIMINAR
     */
    public function destroy(User $user)
    {
        $auth = auth()->user();

        if (!$auth->isSuperAdmin() && in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('users.index')
                ->with('error', 'No tienes permiso para eliminar a este usuario.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * ESTADO ONLINE
     */
    public function status()
    {
        $users = User::all();

        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'is_online' => $user->isOnline(),
            ];
        });

        return response()->json($data);
    }
}
