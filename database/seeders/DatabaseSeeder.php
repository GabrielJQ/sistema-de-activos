<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Limpieza de usuarios para evitar duplicados en pruebas
        DB::table('users')->truncate();

        $this->call([
            AdminUserSeeder::class,
            DepartmentSeeder::class,
            SupplierSeeder::class,
            DeviceTypeSeeder::class,
        ]);
    }

}
