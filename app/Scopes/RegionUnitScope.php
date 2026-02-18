<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Scope global: limita los registros visibles por región y/o unidad del usuario autenticado.
 *
 * Reglas:
 * - Si no hay usuario autenticado, no aplica filtro.
 * - Si el usuario es super_admin, no aplica filtro (ve todo).
 * - Para cada tabla/modelo, aplica el filtro navegando por relaciones hasta llegar a units.
 * - Si el usuario tiene unit_id, filtra por esa unidad.
 * - Si el usuario tiene region_id, filtra por esa región.
 *
 * Nota:
 * Este scope asume que las relaciones existen:
 * - Asset -> department -> unit
 * - Employee -> department -> unit
 * - AssetAssignment -> asset -> department -> unit
 * - Department -> unit
 */
class RegionUnitScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Usuario actual
        $user = Auth::user();

        // Si no hay sesión, no restringimos (útil para jobs/seeders/CLI según tu caso)
        if (!$user)
            return;

        // Super admin ve todo
        if ($user->isSuperAdmin())
            return;

        // Tabla del modelo actual (assets, employees, etc.)
        $table = $model->getTable();

        switch ($table) {

            // Activos: filtra por la unidad/región de la unidad del departamento
            case 'assets':
                $builder->whereHas('department.unit', function ($q) use ($user) {
                    if ($user->unit_id)
                        $q->where('units.id', $user->unit_id);
                    if ($user->region_id)
                        $q->where('units.region_id', $user->region_id);
                });
                break;

            // Empleados: mismo criterio que activos
            case 'employees':
                $builder->whereHas('department.unit', function ($q) use ($user) {
                    if ($user->unit_id)
                        $q->where('units.id', $user->unit_id);
                    if ($user->region_id)
                        $q->where('units.region_id', $user->region_id);
                });
                break;

            // Asignaciones: filtra a través del activo asignado y su departamento/unidad
            case 'asset_assignments':
                $builder->whereHas('asset.department.unit', function ($q) use ($user) {
                    if ($user->unit_id)
                        $q->where('units.id', $user->unit_id);
                    if ($user->region_id)
                        $q->where('units.region_id', $user->region_id);
                });
                break;

            // Departamentos: filtra por la unidad del departamento
            case 'departments':
                $builder->whereHas('unit', function ($q) use ($user) {
                    if ($user->unit_id)
                        $q->where('units.id', $user->unit_id);
                    if ($user->region_id)
                        $q->where('units.region_id', $user->region_id);
                });
                break;

            // Unidades: normalmente basta con limitar por región (si la tiene)
            case 'units':
                if ($user->region_id) {
                    $builder->where('region_id', $user->region_id);
                }
                break;

            // Si el modelo no está contemplado, no aplicamos restricciones
            default:
                return;
        }
    }
}
