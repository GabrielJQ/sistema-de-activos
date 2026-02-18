<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\RegionUnitScope;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'unicve', 'uninom', 'region_id'
    ];

    // Una unidad pertenece a una región
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    // Una unidad tiene muchos departamentos
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
    // Se registra el scope global que filtra por región y unidad del usuario
    protected static function booted()
    {
        static::addGlobalScope(new RegionUnitScope);
    }
    // Relación con el técnico asignado a esta unidad
    public function technician()
    {
        return $this->hasOne(UnitTechnician::class);
    }

}
