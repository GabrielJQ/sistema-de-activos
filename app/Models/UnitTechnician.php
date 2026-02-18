<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\RegionUnitScope;

class UnitTechnician extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'unit_id',
        'employee_id',
        'is_active',
    ];

    // Mutator para forzar string 'true'/'false' y evitar error de tipo en Postgres (boolean vs integer)
    public function setIsActiveAttribute($value)
    {
        $this->attributes['is_active'] = $value ? 'true' : 'false';
    }

    // Relaciones principales: ubicación (región/unidad) y empleado técnico
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Se registra el scope global que filtra por región y unidad del usuario
    protected static function booted()
    {
        static::addGlobalScope(new RegionUnitScope);
    }

    // Resuelve el técnico activo según la unidad del usuario (útil para importación/asignaciones por defecto)
    public static function getTechnicianForUser($user)
    {
        return self::where('unit_id', $user->unit_id)
            ->where('is_active', 'true')
            ->with('employee')
            ->first()?->employee;
    }
}
