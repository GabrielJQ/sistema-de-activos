<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    // Comando: php artisan admin:create
    protected $signature = 'admin:create';

    protected $description = 'Crea o actualiza el superusuario desde variables de entorno';

    public function handle()
    {
        // Credenciales definidas en el .env
        $email    = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name     = env('ADMIN_NAME', 'admin');

        // Seguridad: no permitir crear admin sin credenciales explícitas
        if (!$email || !$password) {
            $this->error('Faltan ADMIN_EMAIL o ADMIN_PASSWORD en el .env');
            return 1;
        }

        // Crea o actualiza siempre el mismo super_admin por email
        $user = User::updateOrCreate(
            ['email' => $email], // Clave única
            [
                'name'              => $name,
                'password'          => Hash::make($password),
                'email_verified_at' => now(),
                'role'              => 'super_admin',
                'department_id'     => null,
            ]
        );

        // Confirmación en consola
        $this->info("Superusuario creado/actualizado: {$user->email}");

        return 0;
    }
}
