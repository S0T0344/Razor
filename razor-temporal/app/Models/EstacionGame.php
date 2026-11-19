<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 2) & PROGRAMACIÓN EXTREMA (XP)
// MODELO DE DATOS (ORM): ESTACIONES DE ENTRETENIMIENTO (ZONA GAME)
// =========================================================================

class EstacionGame extends Model
{
    use HasFactory;

    /**
     * ENFOQUE XP (Diseño Simple): Vinculación explícita con la tabla relacional.
     * Mapea el modelo directamente a la tabla 'estaciones_game' en MySQL.
     */
    protected $table = 'estaciones_game';

    /**
     * PROCESO TÉCNICO: Atributos habilitados para asignación masiva.
     * Permite administrar el nombre de la consola, precio por hora y su estado.
     */
    protected $fillable = [
        'nombre',
        'precio_hora',
        'estado',
    ];
}