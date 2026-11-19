<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 5) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: AUDITORÍA DE SATISFACCIÓN Y RETROALIMENTACIÓN (CAPÍTULO I)
// =========================================================================

class FeedbackPagina extends Model
{
    use HasFactory;

    protected $table = 'feedback_pagina';

    protected $fillable = [
        'user_id',
        'me_gusta',
        'comentario',
    ];

    protected $casts = [
        'me_gusta' => 'boolean',
    ];

    /**
     * RELACIÓN: Cada reseña pertenece a un Cliente (User).
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
