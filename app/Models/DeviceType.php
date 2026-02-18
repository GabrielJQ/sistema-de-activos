<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
    protected $fillable = ['equipo', 'descripcion', 'image_path', 'requires_ip'];

    // Mutator para evitar error de tipo en Postgres (boolean vs integer)
    public function setRequiresIpAttribute($value)
    {
        $this->attributes['requires_ip'] = $value ? 'true' : 'false';
    }

    // Relación con los activos que pertenecen a este tipo de dispositivo
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

}