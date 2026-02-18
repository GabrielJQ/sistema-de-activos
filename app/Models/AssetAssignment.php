<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\RegionUnitScope;

use App\Models\UnitTechnician;
use App\Models\Asset;

class AssetAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'employee_id',
        'department_id',
        'assigned_at',
        'returned_at',
        'is_current',
        'assignment_type', // normal o temporal
        'observations',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    // Mutator para evitar error de tipo en Postgres (boolean vs integer)
    public function setIsCurrentAttribute($value)
    {
        $this->attributes['is_current'] = $value ? 'true' : 'false';
    }

    // Relaciones principales
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relación para guardar datos del ocupante temporal cuando la asignación es temporal
    public function temporaryAssignment()
    {
        return $this->hasOne(TemporaryAssignment::class);
    }

    // Asigna un activo: cierra la asignación actual, resuelve responsable (empleado o técnico), crea nueva asignación y sincroniza estado/departamento del activo
    public static function assignToEmployee(
        int $assetId,
        ?int $employeeId,
        $assignedAt = null,
        ?string $observations = null,
        string $assignmentType = 'normal',
        ?string $temporaryHolder = null
    ): self {
        $assignedAt = $assignedAt ?: now();

        // Cierra cualquier asignación vigente del activo antes de crear la nueva
        self::where('asset_id', $assetId)
            ->where('is_current', 'true')
            ->update([
                'is_current' => \Illuminate\Support\Facades\DB::raw('false'),
                'returned_at' => $assignedAt,
            ]);

        // Si no se manda empleado, se asigna al técnico activo de la unidad del activo
        if (is_null($employeeId)) {

            $asset = Asset::with('department.unit')->findOrFail($assetId);
            $unit = $asset->department?->unit;

            if (!$unit) {
                throw new \Exception('El activo no tiene unidad asociada.');
            }

            $technician = UnitTechnician::where('unit_id', $unit->id)
                ->where('is_active', 'true')
                ->first()
                    ?->employee;

            if (!$technician) {
                throw new \Exception("No hay técnico asignado para la unidad {$unit->uninom}");
            }

            $employeeId = $technician->id;
        }

        // Crea la asignación actual y fija el departamento según el departamento del empleado
        $assignment = self::create([
            'asset_id' => $assetId,
            'employee_id' => $employeeId,
            'department_id' => Employee::find($employeeId)?->department_id,
            'assigned_at' => $assignedAt,
            'returned_at' => null,
            'is_current' => true,
            'assignment_type' => $assignmentType,
            'observations' => $observations,
        ]);

        // Si la asignación es temporal, se guarda el nombre del ocupante temporal
        if ($assignmentType === 'temporal' && $temporaryHolder) {
            $assignment->temporaryAssignment()->create([
                'temporary_holder' => $temporaryHolder,
            ]);
        }

        // Sincroniza el activo: cambia estado y mueve el department_id al del responsable actual
        $asset = $assignment->asset;
        if ($asset) {
            $asset->update([
                'estado' => 'OPERACION',
                'department_id' => $assignment->department_id,
            ]);
        }

        return $assignment;
    }

    // Se registra el scope global que filtra por región y unidad del usuario
    protected static function booted()
    {
        static::addGlobalScope(new RegionUnitScope);
    }

    // Obtiene el técnico de informática asociado al usuario (útil para importaciones y validaciones)
    public static function resolveTechnicianForImport($user = null)
    {
        $user = $user ?? auth()->user();

        $technician = \App\Models\UnitTechnician::getTechnicianForUser($user);

        if (!$technician || !$technician->department) {
            throw new \Exception('No hay técnico de informática configurado correctamente.');
        }

        return $technician;
    }
}
