<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 3 & 4 / HU-08, HU-14 (SCRUM): Sembrado de Inventario Logístico
     * -------------------------------------------------------------------------
     */
    public function run(): void
    {
        // 1. Producto en estado Óptimo (Stock > Mínimo)
        Producto::create([
            'nombre'       => 'Cera Modeladora Pomada Razor',
            'categoria'    => 'Fijadores',
            'precio_venta' => 45.00,
            'stock_actual' => 40,
            'stock_minimo' => 15,
        ]);

        // 2. Producto en estado Óptimo
        Producto::create([
            'nombre'       => 'Aceite Hidratante para Barba Premium',
            'categoria'    => 'Cuidado de Barba',
            'precio_venta' => 60.00,
            'stock_actual' => 25,
            'stock_minimo' => 10,
        ]);

        // 3. Producto en ALERTA ROJA (Stock Actual <= Stock Mínimo)
        Producto::create([
            'nombre'       => 'Champú Anticaída Profesional',
            'categoria'    => 'Cuidado Capilar',
            'precio_venta' => 80.00,
            'stock_actual' => 8,
            'stock_minimo' => 12,
        ]);

        // 4. Producto en ALERTA ROJA (Crítico)
        Producto::create([
            'nombre'       => 'Loción Aftershave Refrescante',
            'categoria'    => 'Barba',
            'precio_venta' => 50.00,
            'stock_actual' => 4,
            'stock_minimo' => 10,
        ]);
    }
}
