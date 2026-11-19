<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 1 & 2 (SCRUM): Sembrado de Usuarios por Rol (RBAC)
     * -------------------------------------------------------------------------
     */
    public function run(): void
    {
        $rolCliente = Role::where('slug', 'cliente')->first()->id;
        $rolBarbero = Role::where('slug', 'barbero')->first()->id;
        $rolAdmin   = Role::where('slug', 'admin')->first()->id;

        // 1. Cuenta Administrador / Gerente
        User::create([
            'name'         => 'Gustavo Gerente',
            'email'        => 'admin@razor.com',
            'password'     => Hash::make('12345678'),
            'role_id'      => $rolAdmin,
            'puntos_razor' => 0,
            'categoria_id' => 1,
        ]);

        // 2. Cuenta Barbero (Panel Operativo)
        User::create([
            'name'         => 'Carlos Barber',
            'email'        => 'carlos@razor.com',
            'password'     => Hash::make('12345678'),
            'role_id'      => $rolBarbero,
            'puntos_razor' => 0,
            'categoria_id' => 1,
        ]);

        // 3. Cuenta Cliente (Nivel Oro con Puntos Razor listos para canje)
        User::create([
            'name'         => 'Mateo Cliente',
            'email'        => 'cliente@razor.com',
            'password'     => Hash::make('12345678'),
            'role_id'      => $rolCliente,
            'puntos_razor' => 200, // Suficiente para probar canje en Zona Gamer (150 pts)
            'categoria_id' => 3,   // 3 = Oro (10% descuento)
        ]);
    }
}
