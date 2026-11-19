<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 1) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: HU-02 (SELECCIÓN DE BARBERO POR ESPECIALIDAD/CALIFICACIÓN)
// MODELO DE DATOS CORE: GESTIÓN DEL PERSONAL OPERATIVO
// =========================================================================

class Barbero extends Model
{
    use HasFactory;

    /**
     * ENFOQUE XP (Diseño Simple): Vinculación explícita con la tabla relacional.
     * Mapea este modelo directamente a la tabla física 'barberos' en MySQL.
     */
    protected $table = 'barberos';

    /**
     * PROCESO TÉCNICO: Atributos habilitados para asignación masiva (Mass Assignment).
     * Permite almacenar de forma segura el nombre, especialidad técnica y calificación.
     */
    protected $fillable = [
        'nombre',
        'especialidad',
        'calificacion',
    ];
    public function citas()
    {
        return $this->hasMany(Cita::class, 'barbero_id');
    }
}
