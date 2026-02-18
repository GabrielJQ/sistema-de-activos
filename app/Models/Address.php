<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelo para gestionar las direcciones de los departamentos
class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'calle', 'colonia', 'cp', 'municipio', 'ciudad', 'estado'
    ];

}
