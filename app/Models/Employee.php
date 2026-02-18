<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\RegionUnitScope;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'expediente',
        'nombre',
        'apellido_pat',
        'apellido_mat',
        'curp',
        'department_id',
        'puesto',
        'tipo',
        'email',
        'telefono',
        'extension',
        'status',
    ];

    // Relación base: el empleado pertenece a un departamento
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Historial de asignaciones de activos del empleado
    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    // Activos actualmente asignados al empleado (filtra por is_current)
    public function currentAssets()
    {
        return $this->hasManyThrough(
            Asset::class,
            AssetAssignment::class,
            'employee_id',
            'id',
            'id',
            'asset_id'
        )->where('asset_assignments.is_current', \Illuminate\Support\Facades\DB::raw('true'));
    }

    // Nombre completo listo para mostrar en vistas/reportes
    public function getFullNameAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido_pat} {$this->apellido_mat}");
    }

    // Nombre del departamento (fallback si no existe relación)
    public function getDepartmentNameAttribute(): string
    {
        return $this->department->areanom ?? '-';
    }

    // Unidad operativa tomada desde el departamento
    public function getUnidadOperativaAttribute()
    {
        return $this->department->unit->uninom ?? null;
    }

    // Dirección formateada del departamento (si existe address)
    public function getDireccionAttribute()
    {
        return $this->department->address
            ? $this->department->address->calle . ', ' .
            $this->department->address->colonia . ', ' .
            $this->department->address->municipio . ', ' .
            $this->department->address->ciudad . ', ' .
            $this->department->address->estado . ', CP ' .
            $this->department->address->cp
            : null;
    }

    // Devuelve el nombre del almacén solo si el tipo del departamento es "almacen"
    public function getAlmacenAttribute()
    {
        if (!$this->department) {
            return null;
        }

        $tipo = strtolower($this->department->tipo);

        return $tipo === 'almacen' ? $this->department->areanom : null;
    }

    // Supervisor asociado al empleado (si aplica a tu modelo)
    public function supervisor()
    {
        return $this->hasOne(Supervisor::class);
    }

    // Se registra el scope global que filtra por región y unidad del usuario
    protected static function booted()
    {
        static::addGlobalScope(new RegionUnitScope);
    }

    // Relación si el empleado es técnico asignado a una unidad
    public function technicianUnit()
    {
        return $this->hasOne(UnitTechnician::class);
    }
}
