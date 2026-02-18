<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\RegionUnitScope;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'areacve',
        'areanom',
        'tipo',
        'unit_id',
        'address_id',
    ];

    // Relaciones principales del departamento (personal, inventario, ubicación y estructura)
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Permite obtener la región del departamento navegando: Department -> Unit -> Region
    public function region()
    {
        return $this->hasOneThrough(
            Region::class,
            Unit::class,
            'id',       // clave foránea usada por Department para apuntar a Unit
            'id',       // clave primaria en Region
            'unit_id',  // columna en Department
            'region_id' // columna en Unit
        );
    }

    // Se registra el scope global que filtra por región y unidad del usuario
    protected static function booted()
    {
        static::addGlobalScope(new RegionUnitScope);
    }
}
