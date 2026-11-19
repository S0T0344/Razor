<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstacionGame;

class EstacionGameSeeder extends Seeder
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 3 / HU-10 & HU-13 (SCRUM): Sembrado de la Zona de Entretenimiento
     * -------------------------------------------------------------------------
     */
    public function run(): void
    {
        EstacionGame::create([
            'nombre'      => 'Estación de Play 1 (PS5)',
            'precio_hora' => 15.00,
            'estado'      => 'ocupado',
        ]);

        EstacionGame::create([
            'nombre'      => 'Estación de Play 2 (PS5)',
            'precio_hora' => 15.00,
            'estado'      => 'disponible',
        ]);

        EstacionGame::create([
            'nombre'      => 'Mesa Arcade Retro',
            'precio_hora' => 10.00,
            'estado'      => 'disponible',
        ]);

        EstacionGame::create([
            'nombre'      => 'Mesa de Pool Profesional',
            'precio_hora' => 20.00,
            'estado'      => 'mantenimiento',
        ]);
    }
}
