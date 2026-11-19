<?php

namespace App\Http\Controllers;

use App\Models\ReservaGame;
use App\Models\EstacionGame;
use App\Models\User;
use Illuminate\Http\Request;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 2) & PROGRAMACIÓN EXTREMA (XP)
// CONTROLADOR LÓGICO: GESTIÓN DE TIEMPOS Y COBROS DE LA ZONA GAME
// =========================================================================

class ReservaGameController extends Controller
{
    /**
     * -------------------------------------------------------------------------
     * HISTORIA DE USUARIO (SCRUM): Reserva y Pago de Turnos de Consolas
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Método POST. Registra la reserva de un televisor/consola,
     * verifica la categoría del cliente (Oro/Plata/Bronce) para otorgar sus
     * beneficios y calcula el monto total a pagar basado en el precio por hora.
     * 
     * ENFOQUE XP (Integración de Datos Cruzados): Vincula directamente el módulo
     * de fidelización con el de entretenimiento en una sola transacción limpia.
     */
    public function reservarTurno(Request $request)
    {
        // 1. Validar la entrada de datos del formulario de la Zona Game
        $request->validate([
            'user_id'          => 'required|integer|exists:users,id',
            'estacion_id'      => 'required|integer|exists:estaciones_game,id',
            'fecha'            => 'required|date',
            'hora_inicio'      => 'required|date_format:H:i',
            'minutos_rentados' => 'required|integer|min:15',
        ]);

        // 2. Obtener la consola y el cliente para procesar los beneficios de nivel
        $estacion = EstacionGame::find($request->estacion_id);
        $cliente = User::find($request->user_id);

        if ($estacion->estado != 'disponible') {
            return response()->json(['success' => false, 'message' => 'La consola no está disponible.'], 422);
        }

        // LÓGICA DE NEGOCIO INTEGRADA (XP - Reglas Automatizadas):
        // Se calculan los minutos finales regalados según la categoría guardada en MySQL.
        $minutosTotales = $request->minutos_rentados;
        
        if ($cliente->categoria_id == 3) { // Categoría Oro
            $minutosTotales += 15; // 15 minutos extra gratis
        } elseif ($cliente->categoria_id == 2) { // Categoría Plata
            $minutosTotales += 10; // 10 minutos extra gratis
        }

        // 3. Calcular el costo financiero basado en la tasa de la consola
        $horas = $request->minutos_rentados / 60;
        $montoFinal = $horas * $estacion->precio_hora;

        // 4. Guardar la reservación y marcar la consola como Ocupada
        $reserva = ReservaGame::create([
            'user_id'          => $request->user_id,
            'estacion_id'      => $request->estacion_id,
            'fecha'            => $request->fecha,
            'hora_inicio'      => $request->hora_inicio,
            'minutos_rentados' => $minutosTotales, // Se guardan los minutos con el beneficio incluido
            'monto_pago'       => $montoFinal,
            'estado_pago'      => 'pendiente',
        ]);

        $estacion->estado = 'ocupado';
        $estacion->save();

        return response()->json([
            'success' => true,
            'message' => '¡Turno reservado con éxito en la Zona Game de RAZOR!',
            'data'    => $reserva
        ], 201);
    }
}