<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecución secuencial estricta (XP)
        $this->call(RoleSeeder::class);
        $this->call(ServicioSeeder::class);
        $this->call(BarberoSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProductoSeeder::class);
        $this->call(EstacionGameSeeder::class);
    }
}
