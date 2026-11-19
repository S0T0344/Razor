<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Barbero;
use App\Models\Cita;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\User;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 3) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: HU-10, HU-11, HU-12 (AGENDA, VENTA EXPRESS Y COMISIONES)
// =========================================================================

class BarberoPanelController extends Controller
{
    /**
     * Carga el panel operativo del barbero autenticado con datos reales de MySQL 3307.
     */
    public function index()
    {
        $usuario = Auth::user();
        // Vincula el usuario logueado con su perfil en la tabla 'barberos' (o toma el primero asignado)
        $barbero = Barbero::where('nombre', 'like', '%' . explode(' ', $usuario->name)[0] . '%')->first()
            ?? Barbero::first();

        $hoy = date('Y-m-d');

        // HU-10 & HU-06: Agenda del día con historial y nivel de fidelidad unificado del cliente
        $citasHoy = Cita::with(['cliente.categoria', 'servicio', 'ventas.producto'])
            ->where('barbero_id', $barbero->id)
            ->where('fecha', $hoy)
            ->orderBy('hora', 'asc')
            ->get();

        // HU-12: Cálculo automático de ingresos por servicios finalizados y comisión del 15%
        $totalServiciosFinalizados = $citasHoy->where('estado', 'finalizado')->sum('precio_final');
        $comisionDiaria = round($totalServiciosFinalizados * 0.15, 2);

        // Ventas express realizadas hoy por este barbero
        $ventasHoy = Venta::with('producto')
            ->where('barbero_id', $barbero->id)
            ->whereDate('created_at', $hoy)
            ->get();

        $productosDisponibles = Producto::where('stock_actual', '>', 0)->get();

        return view('agenda-barbero', compact(
            'usuario',
            'barbero',
            'citasHoy',
            'totalServiciosFinalizados',
            'comisionDiaria',
            'ventasHoy',
            'productosDisponibles'
        ));
    }

    /**
     * HU-10 & HU-07: Actualiza el estado de la cita (En Espera -> En Atención -> Finalizado).
     * Al finalizar, otorga +50 Puntos Razor y evalúa ascenso automático de Tier.
     */
    public function cambiarEstadoCita(Request $request, int $id)
    {
        $request->validate([
            'estado' => 'required|in:en_espera,en_atencion,finalizado',
        ]);

        $cita = Cita::findOrFail($id);
        $estadoAnterior = $cita->estado;
        $cita->update(['estado' => $request->estado]);

        // Automatización CRM: Si pasa a 'finalizado' por primera vez, premia al cliente
        if ($request->estado === 'finalizado' && $estadoAnterior !== 'finalizado') {
            $cliente = User::findOrFail($cita->user_id);
            $cliente->increment('puntos_razor', 50);

            // Recalcular Tier según visitas completadas (Bronce = 1, Plata = 2, Oro = 3)
            $visitasCompletadas = Cita::where('user_id', $cliente->id)
                ->where('estado', 'finalizado')
                ->count();

            if ($visitasCompletadas >= 6) {
                $cliente->update(['categoria_id' => 3]); // Oro (10%)
            } elseif ($visitasCompletadas >= 3) {
                $cliente->update(['categoria_id' => 2]); // Plata (5%)
            }
        }

        return back()->with('success', "Estado de la cita actualizado a: " . strtoupper(str_replace('_', ' ', $request->estado)));
    }

    /**
     * HU-11: Registra Venta Express desde el sillón con transacción atómica DB::transaction.
     */
    public function ventaExpress(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1',
            'cita_id'     => 'nullable|exists:citas,id',
        ]);

        $usuario  = Auth::user();
        $barbero  = Barbero::where('nombre', 'like', '%' . explode(' ', $usuario->name)[0] . '%')->first()
            ?? Barbero::first();
        $producto = Producto::findOrFail($request->producto_id);

        if ($producto->stock_actual < $request->cantidad) {
            return back()->withErrors(['cantidad' => "Stock insuficiente. Solo quedan {$producto->stock_actual} unidades de {$producto->nombre}."]);
        }

        DB::transaction(function () use ($request, $barbero, $producto) {
            $totalVenta = round($producto->precio_venta * $request->cantidad, 2);

            Venta::create([
                'producto_id' => $producto->id,
                'barbero_id'  => $barbero->id,
                'cita_id'     => $request->cita_id,
                'cantidad'    => $request->cantidad,
                'total'       => $totalVenta,
            ]);

            // Descuento automático del inventario logístico
            $producto->decrement('stock_actual', $request->cantidad);
        });

        return back()->with('success', "Venta Express registrada: {$request->cantidad}x {$producto->nombre}. Stock actualizado.");
    }
}
