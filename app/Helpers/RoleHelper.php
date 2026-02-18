<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('hasRole')) {
    /**
     * Verifica si el usuario autenticado tiene alguno de los roles indicados.
     *
     * @param string|array $roles
     * @param string $mode 'view' | 'disable'
     * @return bool|string
     */
    function hasRole($roles, $mode = 'view')
    {
        if (!Auth::check()) {
            return $mode === 'disable' ? 'disabled' : false;
        }

        $userRole = Auth::user()->role; // Asegúrate que tu campo se llama 'role'

        $allowed = is_array($roles)
            ? in_array($userRole, $roles)
            : $userRole === $roles;

        if ($mode === 'disable') {
            // Si el usuario no tiene el rol → devolver 'disabled'
            return $allowed ? '' : 'disabled';
        }

        // Modo normal → devolver booleano
        return $allowed;
    }
}
