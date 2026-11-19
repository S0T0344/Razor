<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 1) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: HU-03, HU-04, HU-05 (AGENDAMIENTO Y MIS RESERVAS)
// MODELO DE DATOS CORE: GESTIÓN TRANSACCIONAL DE CITAS
// =========================================================================

class Cita extends Model
{
    use HasFactory;

    /**
     * ENFOQUE XP (Diseño Simple): Vinculación explícita con la tabla relacional.
     */
    protected $table = 'citas';

    /**
     * PROCESO TÉCNICO: Atributos habilitados para asignación masiva (Mass Assignment).
     * Incluye claves foráneas, banderas de Hora Valle, método de pago y estados.
     */
    protected $fillable = [
        'user_id',
        'barbero_id',
        'servicio_id',
        'fecha',
        'hora',
        'es_hora_valle',
        'metodo_pago',
        'precio_final',
        'estado',
    ];

    /**
     * CONVERSIÓN DE TIPOS NATIVOS (Casting):
     * Asegura que 'es_hora_valle' se maneje estrictamente como booleano (true/false).
     */
    protected $casts = [
        'es_hora_valle' => 'boolean',
    ];

    /**
     * RELACIÓN 1: Una cita pertenece a un Cliente (User).
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * RELACIÓN 2: Una cita es atendida por un Barbero.
     */
    public function barbero()
    {
        return $this->belongsTo(Barbero::class, 'barbero_id');
    }

    /**
     * RELACIÓN 3: Una cita corresponde a un Servicio del catálogo.
     */
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cita_id');
    }
}
