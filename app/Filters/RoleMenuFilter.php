<?php

namespace App\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Auth;

class RoleMenuFilter implements FilterInterface
{
    /**
     * Filtra los ítems del menú según el rol del usuario.
     *
     * @param array $item
     * @return array|bool
     */
    public function transform($item)
    {
        // Si no hay roles definidos para este ítem, mostrarlo siempre
        if (!isset($item['roles'])) {
            return $item;
        }

        $user = Auth::user();

        // Si el usuario no está autenticado o no tiene el rol correcto, ocultar el ítem
        if (!$user || !in_array($user->role, $item['roles'])) {
            return false;
        }

        return $item;
    }
}
