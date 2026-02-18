<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TemporaryAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_assignment_id',
        'temporary_holder',
    ];

    // Relaciones
    public function assetAssignment()
    {
        return $this->belongsTo(AssetAssignment::class);
    }
    // Relación inversa para acceder a la asignación temporal desde AssetAssignment
    public function temporaryAssignment()
    {
        return $this->hasOne(TemporaryAssignment::class);
    }
    // Relación con el empleado (responsable) asociado a la asignación temporal
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
