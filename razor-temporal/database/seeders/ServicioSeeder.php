<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servicio;

class ServicioSeeder extends Seeder
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 1 / HU-01 (SCRUM): Sembrado del Menú de Servicios en Bs. y Minutos
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Inserta los registros base en MySQL configurando el 
     * precio comercial real y la duración estimada para la agenda.
     */
    public function run(): void
    {
        // Registro 1: Corte de Cabello tradicional
        Servicio::create([
            'nombre' => 'Corte de Cabello Tradicional',
            'precio' => 35.00,
            'duracion_minutos' => 30
        ]);

        // Registro 2: Corte Degradado Urbano (Mayor complejidad)
        Servicio::create([
            'nombre' => 'Corte Degradado Urbano',
            'precio' => 40.00,
            'duracion_minutos' => 45
        ]);

        // Registro 3: Combo Completo (Corte + Cuidado de Barba)
        Servicio::create([
            'nombre' => 'Combo Razor: Corte + Barba Clásica',
            'precio' => 50.00,
            'duracion_minutos' => 60
        ]);
    }
}
