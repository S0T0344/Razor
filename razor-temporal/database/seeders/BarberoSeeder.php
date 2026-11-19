<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barbero;

class BarberoSeeder extends Seeder
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 1 / HU-02 (SCRUM): Sembrado del Equipo de Barberos
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Inserta el catálogo de profesionales con su especialidad
     * técnica y calificación promedio (0.00 a 5.00) para habilitar el filtrado
     * manual o la asignación automática por menor tiempo de espera.
     */
    public function run(): void
    {
        Barbero::create([
            'nombre' => 'Carlos Barber',
            'especialidad' => 'Experto en Degradados (Fade) y Diseños',
            'calificacion' => 4.95
        ]);

        Barbero::create([
            'nombre' => 'Alejandro Estilista',
            'especialidad' => 'Estilista Clásico y Ritual de Barba',
            'calificacion' => 4.85
        ]);

        Barbero::create([
            'nombre' => 'Mateo Cortéz',
            'especialidad' => 'Colorimetría y Cortes Modernos',
            'calificacion' => 4.75
        ]);
    }
}
