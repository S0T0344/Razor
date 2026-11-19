<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Servicio;
use App\Models\Barbero;
use App\Models\Cita;
use App\Models\EstacionGame;
use App\Models\ReservaGame;
use App\Models\User;
use App\Models\FeedbackPagina;
use App\Models\ConfiguracionPromocion;
use App\Models\Producto;


// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 1 & SPRINT 2) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: HU-01 A HU-10 (PORTAL CLIENTE, CRM Y REGLA ANTIFRAUDE)
// =========================================================================

class ClientePortalController extends Controller
{
    /**
     * Carga el portal exclusivo del cliente con datos reales desde MySQL 3307.
     */
    public function index()
    {
        $cliente = User::with('categoria')->find(Auth::id());
        $servicios  = Servicio::all();
        $barberos   = Barbero::orderByDesc('calificacion')->get();
        $estaciones = EstacionGame::all();

        // HU-05 & HU-06: Historial unificado y Mis Reservas del cliente autenticado
        $misCitas = Cita::with(['barbero', 'servicio'])
            ->where('user_id', $cliente->id)
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->get();

        $misReservasGame = ReservaGame::with('estacion')
            ->where('user_id', $cliente->id)
            ->orderByDesc('created_at')
            ->get();

        $promoConfig = ConfiguracionPromocion::first();
        $productos = Producto::where('stock_actual', '>', 0)->get();

        return view('portal-cliente', compact(
    'cliente',
    'servicios',
    'barberos',
    'misCitas',
    'estaciones',
    'misReservasGame',
    'promoConfig',
    'productos'
));
    }

    /**
     * Procesa el agendamiento inteligente en 4 pasos y aplica la Regla Antifraude (HU-09).
     */
    public function agendarCita(Request $request)
    {
        $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'barbero_id'  => 'required|string',
            'fecha'       => 'required|date',
            'hora'        => 'required|string',
            'metodo_pago' => 'required|in:efectivo,qr',
        ]);

        $cliente = User::with('categoria')->find(Auth::id());
        $servicio = Servicio::findOrFail($request->servicio_id);

        // HU-02: Asignación manual o automática por menor carga de citas del día
        if ($request->barbero_id === 'auto') {
            $barbero = Barbero::withCount(['citas' => function ($q) use ($request) {
                $q->where('fecha', $request->fecha)->whereIn('estado', ['en_espera', 'en_atencion']);
            }])->orderBy('citas_count', 'asc')->first();
        } else {
            $barbero = Barbero::findOrFail($request->barbero_id);
        }

        // Validación anti-choques en servidor
        $existeChoque = Cita::where('barbero_id', $barbero->id)
            ->where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->whereIn('estado', ['en_espera', 'en_atencion'])
            ->exists();

        if ($existeChoque) {
            return back()->withErrors(['hora' => 'Ese bloque horario ya fue reservado para ' . $barbero->nombre . '. Elige otro horario.']);
        }

        // Validación de canje de 300 Puntos Razor por Corte Gratis
        $canjearCorte = $request->boolean('canjear_corte_gratis');
        if ($canjearCorte && $cliente->puntos_razor < 300) {
            return back()->withErrors(['servicio_id' => 'Necesitas al menos 300 Puntos Razor para canjear un corte gratis.']);
        }

        // Detección de Hora Valle dinámica desde ConfiguracionPromocion
        $promo = ConfiguracionPromocion::first();
        $horaEntera = (int) explode(':', $request->hora);
        $fechaValida = ($promo && $promo->activo && $request->fecha >= $promo->fecha_inicio && $request->fecha <= $promo->fecha_fin);
        $esHoraValle = ($fechaValida && $horaEntera >= $promo->hora_inicio && $horaEntera < $promo->hora_fin);
        $descuentoHoraValle = $esHoraValle ? (float) $promo->porcentaje_descuento : 0.00;
        $descuentoCategoria = $cliente->categoria ? (float) $cliente->categoria->porcentaje_descuento : 0.00;

        // Regla Antifraude de Márgenes (HU-09) o Canje Total de 300 Puntos
        if ($canjearCorte) {
            $descuentoAplicado = 100.00;
            $precioFinal = 0.00;
        } else {
            $descuentoAplicado = max($descuentoCategoria, $descuentoHoraValle);
            $precioFinal = round($servicio->precio * (1 - ($descuentoAplicado / 100)), 2);
        }

        DB::transaction(function () use ($cliente, $barbero, $servicio, $request, $esHoraValle, $precioFinal, $canjearCorte) {
            if ($canjearCorte) {
                $cliente->decrement('puntos_razor', 300);
            }

            Cita::create([
                'user_id'       => $cliente->id,
                'barbero_id'    => $barbero->id,
                'servicio_id'   => $servicio->id,
                'fecha'         => $request->fecha,
                'hora'          => $request->hora,
                'es_hora_valle' => $esHoraValle,
                'metodo_pago'   => $canjearCorte ? 'puntos' : $request->metodo_pago,
                'precio_final'  => $precioFinal,
                'estado'        => 'en_espera',
            ]);
        });

        $mensaje = $canjearCorte
            ? "¡Corte Gratis canjeado por 300 Puntos Razor con {$barbero->nombre}!"
            : "Cita confirmada con {$barbero->nombre}. Descuento aplicado: {$descuentoAplicado}% (Total: Bs. {$precioFinal}).";

        return back()->with('success', $mensaje);
    }

    /**
     * HU-10 & HU-13: Reserva de Zona Gamer con canje atómico de 150 Puntos Razor.
     */
    public function reservarGame(Request $request)
    {
        $request->validate([
            'estacion_id' => 'required|exists:estaciones_game,id',
            'fecha'       => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|string',
            'duracion'    => 'required|in:30,60',
            'modalidad'   => 'required|in:canje_puntos,pago_directo',
        ]);

        $cliente  = User::with('categoria')->findOrFail(Auth::id());
        $estacion = EstacionGame::findOrFail($request->estacion_id);

        if ($estacion->estado === 'mantenimiento') {
            return back()->withErrors(['estacion_id' => 'Esta estación está en mantenimiento técnico temporal.']);
        }

        $horaFormateada = strlen($request->hora_inicio) === 5 ? $request->hora_inicio . ':00' : $request->hora_inicio;
        $horaInicioSeg = strtotime($horaFormateada);
        $horaFinSeg    = $horaInicioSeg + ((int) $request->duracion * 60);

        // Validación real de cruce de intervalos horarios (evita solapamientos de 30 o 60 min)
        $reservasDelDia = ReservaGame::where('estacion_id', $estacion->id)
            ->where('fecha', $request->fecha)
            ->get();

        foreach ($reservasDelDia as $res) {
            $resInicio = strtotime($res->hora_inicio);
            $resFin    = $resInicio + ($res->minutos_rentados * 60);
            if ($horaInicioSeg < $resFin && $horaFinSeg > $resInicio) {
                return back()->withErrors([
                    'hora_inicio' => "Cruce de horario: {$estacion->nombre} ya está ocupada de " . substr($res->hora_inicio, 0, 5) . " a " . date('H:i', $resFin) . "."
                ]);
            }
        }

        if ($request->modalidad === 'canje_puntos' && $cliente->puntos_razor < 150) {
            return back()->withErrors(['estacion_id' => 'No tienes suficientes Puntos Razor (requieres 150 pts).']);
        }

        DB::transaction(function () use ($request, $cliente, $estacion, $horaFormateada) {
            $minutosExtra = $cliente->categoria ? (int) $cliente->categoria->minutos_extra_game : 0;
            $minutosBase = (int) $request->duracion;
            $minutosTotales = $minutosBase + $minutosExtra;

            if ($request->modalidad === 'canje_puntos') {
                $cliente->decrement('puntos_razor', 150);
                $costoFinal = 0.00;
                $estadoPago = 'pagado';
            } else {
                $factor = $minutosBase / 60;
                $costoFinal = round($estacion->precio_hora * $factor, 2);
                $estadoPago = 'pendiente';
            }

            ReservaGame::create([
                'user_id'          => $cliente->id,
                'estacion_id'      => $estacion->id,
                'fecha'            => $request->fecha,
                'hora_inicio'      => $horaFormateada,
                'minutos_rentados' => $minutosTotales,
                'monto_pago'       => $costoFinal,
                'estado_pago'      => $estadoPago,
            ]);
        });

        return back()->with('success', "Turno reservado en {$estacion->nombre} para el {$request->fecha} a las {$request->hora_inicio}.");
    }

    /**
     * SPRINT 5 / CIERRE CAPÍTULO I: Registra la valoración y comentario del cliente.
     */
    public function enviarFeedback(Request $request)
    {
        $request->validate([
            'me_gusta'   => 'required|boolean',
            'comentario' => 'nullable|string|max:500',
        ]);

        FeedbackPagina::create([
            'user_id'    => Auth::id(),
            'me_gusta'   => (bool) $request->me_gusta,
            'comentario' => $request->comentario,
        ]);

        return back()->with('success', '¡Gracias por tu valoración! Tu reseña ha sido registrada para mejorar nuestro servicio.');
    }

    /**
     * Paso A: Genera la Nota de Venta / Comprobante Digital de una cita finalizada.
     */
    public function verRecibo(int $id)
    {
        $cita = Cita::with(['cliente.categoria', 'barbero', 'servicio', 'ventas.producto'])
            ->where('user_id', Auth::id())
            ->where('estado', 'finalizado')
            ->findOrFail($id);

        return view('recibo-cita', compact('cita'));
    }
}
