<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Region;
use App\Models\Unit;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Intentar obtener una región y unidad por defecto si existen, opcional
        $region = Region::first();
        $unit = Unit::first();

        try {
            $supabaseService = app(\App\Services\SupabaseAuthService::class);
            $email = 'admin@admin.com';
            $password = 'password';

            // 1. Obtener o Crear usuario en Supabase
            $existingUser = $supabaseService->getUserByEmail($email);

            if ($existingUser) {
                $uuid = $existingUser['id'];
                $this->command->info("User already exists in Supabase. Using UUID: $uuid");
            } else {
                $uuid = $supabaseService->createUser($email, $password, [
                    'name' => 'Super Admin',
                    'role' => 'super_admin'
                ]);
                $this->command->info("Created new user in Supabase. UUID: $uuid");
            }

            // 2. Crear usuario local con el UUID de Supabase
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make($password),
                    'role' => 'super_admin',
                    'region_id' => $region?->id,
                    'unit_id' => $unit?->id,
                    'email_verified_at' => now(),
                    'supabase_user_id' => $uuid, // Guardamos el UUID real
                ]
            );

            $this->command->info('Super Admin user created successfully (Synced with Supabase).');
            $this->command->info("Email: $email");
            $this->command->info("Password: $password");
            $this->command->info("Supabase UUID: $uuid");

        } catch (\Exception $e) {
            $this->command->error("Error creating Super Admin: " . $e->getMessage());
        }
    }
}
