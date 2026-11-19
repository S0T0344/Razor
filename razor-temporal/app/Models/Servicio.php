<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 1) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: HU-01 (SELECCIÓN DE SERVICIOS)
// MODELO DE DATOS CORE: CATÁLOGO DE OPERACIONES DE LA BARBERÍA
// =========================================================================

class Servicio extends Model
{
    use HasFactory;

    /**
     * ENFOQUE XP (Diseño Simple): Vinculación explícita con la tabla relacional.
     * Mapea el modelo directamente a la tabla física 'servicios' en MySQL.
     */
    protected $table = 'servicios';

    /**
     * PROCESO TÉCNICO: Atributos habilitados para asignación masiva (Mass Assignment).
     * Controla el guardado seguro del nombre, precio comercial y tiempo en minutos.
     */
    protected $fillable = [
        'nombre',
        'precio',
        'duracion_minutos',
    ];
}
