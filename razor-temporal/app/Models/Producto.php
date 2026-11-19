<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 3 & 4) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: HU-08 & HU-14 (GESTIÓN LOGÍSTICA Y ALERTAS DE STOCK)
// =========================================================================

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'categoria',
        'precio_venta',
        'stock_actual',
        'stock_minimo',
    ];

    /**
     * RELACIÓN: Un producto puede estar en múltiples registros de venta.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'producto_id');
    }
}
