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

class BarberoPanelController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $primerNombre = strtok($usuario->name, ' ');
        $barbero = Barbero::where('nombre', 'like', '%' . $primerNombre . '%')->first()
            ?? Barbero::first();

        $hoy = date('Y-m-d');

        $citasHoy = Cita::with(['cliente.categoria', 'servicio', 'ventas.producto'])
            ->where('barbero_id', $barbero->id)
            ->where('fecha', $hoy)
            ->orderBy('hora', 'asc')
            ->get();

        $totalServiciosFinalizados = $citasHoy->where('estado', 'finalizado')->sum('precio_final');
        $comisionDiaria = round($totalServiciosFinalizados * 0.15, 2);

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

    public function cambiarEstadoCita(Request $request, int $id)
    {
        $request->validate([
            'estado' => 'required|in:en_espera,en_atencion,finalizado',
        ]);

        $cita = Cita::findOrFail($id);
        $estadoAnterior = $cita->estado;
        $cita->update(['estado' => $request->estado]);

        if ($request->estado === 'finalizado' && $estadoAnterior !== 'finalizado') {
            $cliente = User::findOrFail($cita->user_id);
            $cliente->increment('puntos_razor', 50);

            $visitasCompletadas = Cita::where('user_id', $cliente->id)
                ->where('estado', 'finalizado')
                ->count();

            if ($visitasCompletadas >= 6) {
                $cliente->update(['categoria_id' => 3]);
            } elseif ($visitasCompletadas >= 3) {
                $cliente->update(['categoria_id' => 2]);
            }
        }

        return back()->with('success', "Estado de la cita actualizado a: " . strtoupper(str_replace('_', ' ', $request->estado)));
    }

    public function ventaExpress(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1',
            'cita_id'     => 'nullable|exists:citas,id',
        ]);

        $usuario = Auth::user();
        $primerNombre = strtok($usuario->name, ' ');
        $barbero = Barbero::where('nombre', 'like', '%' . $primerNombre . '%')->first()
            ?? Barbero::first();
        $producto = Producto::findOrFail($request->producto_id);

        if ($producto->stock_actual < $request->cantidad) {
            return back()->withErrors(['cantidad' => "Stock insuficiente. Solo quedan {$producto->stock_actual} unidades."]);
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

            $producto->decrement('stock_actual', $request->cantidad);
        });

        return back()->with('success', "Venta Express registrada: {$request->cantidad}x {$producto->nombre}.");
    }
}
