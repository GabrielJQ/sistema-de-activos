<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'regcve', 'regnom'
    ];

    // Relación con las unidades que pertenecen a esta región
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    // Relación con los departamentos a través de las unidades
    public function departments()
    {
        return $this->hasManyThrough(Department::class, Unit::class);
    }
}
