<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['prvnombre', 'contrato', 'prvstatus', 'telefono', 'enlace', 'logo_path'];

    // Mutator para evitar error de tipo en Postgres (boolean vs integer)
    public function setPrvstatusAttribute($value)
    {
        $this->attributes['prvstatus'] = $value ? 'true' : 'false';
    }

    // Relación con los activos que pertenecen a este proveedor
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
