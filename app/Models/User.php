<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'region_id',
        'unit_id',
        'supabase_user_id',
        'smiab_refresh_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Helpers para validar el rol del usuario
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCollaborator(): bool
    {
        return $this->role === 'collaborator';
    }

    public function isVisitor(): bool
    {
        return $this->role === 'visitor';
    }

    // Verifica si el usuario tiene una sesión activa reciente
    public function isOnline(): bool
    {
        $lastActivityThreshold = Carbon::now()->subMinutes(5)->timestamp;

        return DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>=', $lastActivityThreshold)
            ->exists();
    }

    // Relaciones de ubicación del usuario
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Imagen de perfil usada por AdminLTE
    public function adminlte_image()
    {
        return 'ruta/imagen.jpg';
    }

    // Descripción del usuario mostrada en AdminLTE según su rol
    public function adminlte_desc()
    {
        return match ($this->role) {
                'super_admin' => 'Super Administrador',
                'admin' => 'Administrador del sistema',
                'collaborator' => 'Colaborador',
                'visitor' => 'Visitante',
                default => 'Usuario',
            };
    }

    // URL del perfil para AdminLTE
    public function adminlte_profile_url()
    {
        return route('profile.edit');
    }
}
