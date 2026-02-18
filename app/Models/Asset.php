<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\RegionUnitScope;

/**
 * Modelo Asset
 *
 * Representa un activo informático dentro del sistema (equipo de cómputo,
 * impresora, periférico, etc.).
 *
 * Este modelo centraliza:
 * - Los datos generales del activo (TAG, serie, marca, modelo, etc.)
 * - Sus relaciones con tipo de equipo, proveedor y departamento
 * - Su historial de asignaciones
 * - La lógica para obtener la asignación y responsable actual
 *
 * Además, aplica automáticamente el scope RegionUnitScope para que cada
 * usuario solo vea los activos de su región/unidad, excepto el super admin.
 */
class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_type_id',
        'tag',
        'serie',
        'marca',
        'modelo',
        'propiedad',
        'supplier_id',
        'department_id',
        'activo',
        'estado',
    ];


    protected $appends = ['device_type_equipo'];

    public function getDeviceTypeEquipoAttribute()
    {
        return $this->deviceType?->equipo ?? '';
    }
    // Relación con el tipo de equipo del activo
    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class);
    }

    // Relación con el proveedor del activo
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relación con el departamento al que pertenece el activo
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function networkInterface()
    {
        return $this->hasOne(AssetNetworkInterface::class);
    }

    // Relación con las asignaciones del activo
    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    // Método para obtener la asignación actual del activo
    public function currentAssignment()
    {
        return $this->hasOne(AssetAssignment::class)
            ->where('is_current', 'true');
    }
    // Método para obtener el empleado que tiene asignado el activo actualmente
    public function currentHolder()
    {
        return $this->hasOne(AssetAssignment::class)
            ->where('is_current', 'true')
            ->with('employee');
    }

    // Método para obtener el empleado responsable actual del activo
    public function currentResponsible()
    {
        return $this->currentHolder?->employee;
    }

    // Método para verificar si el activo está dado de baja
    public function isDecommissioned()
    {
        return $this->estado === 'BAJA';
    }

    // Accesor para obtener una etiqueta legible del estado del activo
    public function getStatusLabelAttribute()
    {
        return match ($this->estado) {
            'operativo' => 'En operación',
            'mantenimiento' => 'En mantenimiento',
            'BAJA' => 'Dado de baja',
            default => ucfirst($this->estado),
        };
    }

    //Se registra el scope global que filtra por región y unidad del usuario.
    protected static function booted()
    {
        static::addGlobalScope(new RegionUnitScope);
    }
}
