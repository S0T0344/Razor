<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{
    /**
     * Crear una nueva cita con validación anti-choques (XP)
     */
    public function agendarCita(Request $request)
    {
        // 1. Validar los datos recibidos del formulario web
        $request->validate([
            'cliente_id'  => 'required|integer',
            'barbero_id'  => 'required|integer',
            'servicio_id' => 'required|integer',
            'fecha'       => 'required|date',
            'hora'        => 'required|date_format:H:i',
        ]);

        // 2. Validación de Ingeniería (XP): Verificar si el barbero ya está ocupado a esa hora
        $existeChoque = Cita::where('barbero_id', $request->barbero_id)
            ->where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($existeChoque) {
            return response()->json([
                'success' => false,
                'message' => 'El barbero seleccionado ya tiene una cita reservada en este horario.'
            ], 422);
        }

        // 3. Si el horario está libre, se guarda la cita en MySQL
        $cita = Cita::create([
            'cliente_id'  => $request->cliente_id,
            'barbero_id'  => $request->barbero_id,
            'servicio_id' => $request->servicio_id,
            'fecha'       => $request->fecha,
            'hora'        => $request->hora,
            'estado'      => 'pendiente',
        ]);

        return response()->json([
            'success' => true,
            'message' => '¡Cita agendada con éxito para la barbería RAZOR!',
            'data'    => $cita
        ], 201);
    }

    /**
     * Marcar cita como completada y disparar la recategorización automática del cliente
     */
    public function completarCita($id)
    {
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        // Marcar estado como completada
        $cita->estado = 'completada';
        $cita->save();

        // LLAMADA MAESTRA (Scrum + XP): Ejecuta el controlador de categorías automáticamente
        $categoriaController = new CategoriasClienteController();
        $nuevaCategoria = $categoriaController->actualizarCategoriaCliente($cita->cliente_id);

        return response()->json([
            'success' => true,
            'message' => 'Cita completada. El historial del cliente fue analizado.',
            'categoria_actual_id' => $nuevaCategoria
        ]);
    }
}