<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM & PROGRAMACIÓN EXTREMA (XP - DISEÑO SIMPLE)
// CUMPLIMIENTO DE SEGURIDAD (RBAC - ISO/IEC 25010)
// MODELO DE DATOS CORE: ROLES DE ACCESO DEL SISTEMA
// =========================================================================

class Role extends Model
{
    use HasFactory;

    /**
     * ENFOQUE XP (Diseño Simple): Vinculación explícita con la tabla relacional.
     * Define formalmente el mapeo hacia la tabla física 'roles' en MySQL.
     */
    protected $table = 'roles';

    /**
     * PROCESO TÉCNICO: Atributos asignables de forma masiva (Mass Assignment).
     * Habilita el guardado seguro del nombre y el slug único para auditorías.
     */
    protected $fillable = [
        'nombre',
        'slug',
    ];

    /**
     * RELACIÓN RELACIONAL CORE (Matriz de Control de Acceso):
     * Define una relación de UNO a MUCHOS (hasMany). Un rol específico (ej: barbero) 
     * puede estar asignado a múltiples usuarios dentro de la tabla 'users'.
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
