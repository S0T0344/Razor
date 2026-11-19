<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 1) & PROGRAMACIÓN EXTREMA (XP)
// MODELO DE DATOS: CATEGORÍAS DE CLIENTES AUTOMÁTICAS
// =========================================================================

class CategoriasCliente extends Model
{
    use HasFactory;

    /**
     * ENFOQUE XP (Diseño Simple): Vinculación explícita con la tabla física de MySQL.
     * Le indica a Laravel el nombre exacto de la tabla relacional.
     */
    protected $table = 'categorias_cliente';

    /**
     * PROCESO TÉCNICO: Definición de campos habilitados para inserción masiva.
     * Protege el sistema contra inyecciones de campos no autorizados.
     */
    protected $fillable = [
        'nombre',
        'descuento_pos',
        'beneficio_game',
    ];

    /**
     * RELACIÓN RELACIONAL (Scrum - Módulo de Fidelización):
     * Define que una categoría (ej: Oro) puede pertenecer a MUCHOS usuarios (hasMany).
     * Esto alimenta la lógica que cuenta el historial de visitas para subir de nivel al cliente.
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'categoria_id');
    }
}