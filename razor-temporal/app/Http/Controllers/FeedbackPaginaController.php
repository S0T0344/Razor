<?php

namespace App\Http\Controllers;

use App\Models\FeedbackPagina;
use Illuminate\Http\Request;

// =========================================================================
// METODOLOGÍA PROGRAMACIÓN EXTREMA (XP): PRÁCTICA DE FEEDBACK CONTINUO
// PROYECTO DE GRADO: PLATAFORMA INTEGRAL "RAZOR BARBERÍA"
// CONTROLADOR LOGÍCO: EVALUACIÓN DE LA CALIDAD DEL SISTEMA
// =========================================================================

class FeedbackPaginaController extends Controller
{
    /**
     * -------------------------------------------------------------------------
     * HISTORIA DE USUARIO DE EVALUACIÓN (SCRUM): Caja de Comentarios y Reseñas
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Recibe los datos enviados desde el formulario web (voto 
     * cuantitativo 'me_gusta' y opinión cualitativa 'comentario'), los valida 
     * bajo reglas de negocio estrictas y los almacena en MySQL.
     * 
     * ENFOQUE XP (Feedback del Cliente): Permite recopilar de primera mano los
     * criterios de aceptación externos para aplicar refactorizaciones inmediatas.
     */
    public function guardarFeedback(Request $request)
    {
        // 1. Validar los datos de entrada para evitar inyecciones o registros nulos
        $request->validate([
            'user_id'    => 'required|integer|exists:users,id',
            'me_gusta'   => 'required|boolean', // true = Like, false = Dislike
            'comentario' => 'nullable|string|max:500', // Campo opcional de texto corto
        ]);

        // 2. Guardar el registro en la base de datos a través del Modelo Eloquent
        $feedback = FeedbackPagina::create([
            'user_id'    => $request->user_id,
            'me_gusta'   => $request->me_gusta,
            'comentario' => $request->comentario,
        ]);

        // 3. Responder con un formato JSON estandarizado para la integración frontend
        return response()->json([
            'success' => true,
            'message' => '¡Muchas gracias por evaluar la plataforma de la barbería RAZOR!',
            'data'    => $feedback
        ], 201);
    }
}