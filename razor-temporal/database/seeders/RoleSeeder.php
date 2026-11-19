<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * -------------------------------------------------------------------------
     * SPRINT 1 / MATRIZ RBAC: Sembrado Automatizado de Roles Core
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Inserta los 3 registros maestros autorizados en MySQL 
     * asociándoles su slug único para la validación estricta en el servidor.
     */
    public function run(): void
    {
        // Rol 1: Cliente (Portal Público / Autenticado)
        Role::create([
            'nombre' => 'Cliente (Portal Personalizado)',
            'slug' => 'cliente'
        ]);

        // Rol 2: Barbero (Panel Operativo)
        Role::create([
            'nombre' => 'Barbero (Gestión Operativa)',
            'slug' => 'barbero'
        ]);

        // Rol 3: Administrador / Gerente (Panel 100% Cloud)
        Role::create([
            'nombre' => 'Administrador / Gerente',
            'slug' => 'admin'
        ]);
    }
}
