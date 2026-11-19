<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Cita;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Barbero;
use App\Models\EstacionGame;
use App\Models\GastoOperativo;
use App\Models\FeedbackPagina;
use App\Models\ConfiguracionPromocion;

// =========================================================================
// METODOLOGÍA ÁGIL: SCRUM (SPRINT 4) & PROGRAMACIÓN EXTREMA (XP)
// PRODUCT BACKLOG: HU-13 A HU-17 (LOGÍSTICA, ANALÍTICAS CHART.JS Y CONTROL)
// =========================================================================

class AdminDashboardController extends Controller
{
    /**
     * Carga el Dashboard Gerencial remoto con KPIs, tablas y series para Chart.js.
     */
    public function index()
    {
        $admin = Auth::user();
        $hoy   = date('Y-m-d');

        // HU-15: KPIs Principales del Día
        $ingresosServiciosHoy = Cita::whereDate('fecha', $hoy)
            ->where('estado', 'finalizado')
            ->sum('precio_final');

        $ingresosProductosHoy = Venta::whereDate('created_at', $hoy)
            ->sum('total');

        $reservasTotales = Cita::count();

        // HU-14: Logística de Inventario y Alertas de Stock Bajo
        $productos = Producto::orderBy('stock_actual', 'asc')->get();
        $alertasStockCount = $productos->filter(function ($p) {
            return $p->stock_actual <= $p->stock_minimo;
        })->count();

        // Ranking de Barberos por citas atendidas
        $rankingBarberos = Barbero::withCount(['citas as citas_completadas' => function ($q) {
            $q->where('estado', 'finalizado');
        }])->orderByDesc('citas_completadas')->get();

        // Top Productos Más Vendidos
        $productosMasVendidos = Venta::select('producto_id', DB::raw('SUM(cantidad) as total_unidades'), DB::raw('SUM(total) as total_dinero'))
            ->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('total_unidades')
            ->take(5)
            ->get();

        // HU-13: Control en tiempo real de Estaciones de la Zona de Entretenimiento
        $estaciones = EstacionGame::all();

        // HU-16: Serie de últimos 7 días (Servicios vs. Productos) para Chart.js
        $fechasSemana = [];
        $serieServicios = [];
        $serieProductos = [];

        for ($i = 6; $i >= 0; $i--) {
            $dia = date('Y-m-d', strtotime("-{$i} days"));
            $fechasSemana[] = date('d/m', strtotime($dia));
            $serieServicios[] = (float) Cita::whereDate('fecha', $dia)->where('estado', 'finalizado')->sum('precio_final');
            $serieProductos[] = (float) Venta::whereDate('created_at', $dia)->sum('total');
        }

        // HU-17: Mapa de flujo de Horas Pico y Valle (09:00 a 18:00) para Chart.js
        $bloquesHoras = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
        $flujoPorHora = [];

        foreach ($bloquesHoras as $bloque) {
            $horaPrefijo = substr($bloque, 0, 2);
            $flujoPorHora[] = Cita::where('hora', 'like', $horaPrefijo . ':%')->count();
        }

        $gastosHoy = GastoOperativo::whereDate('fecha', $hoy)->sum('monto');
        $ingresosTotalesHoy = $ingresosServiciosHoy + $ingresosProductosHoy;
        $utilidadNetaHoy = $ingresosTotalesHoy - $gastosHoy;
        $ultimosGastos = GastoOperativo::orderByDesc('fecha')->take(5)->get();
        $resenasClientes = FeedbackPagina::with('cliente')->orderByDesc('created_at')->take(5)->get();
        $promoConfig = ConfiguracionPromocion::first();

        return view('dashboard-admin', compact(
            'admin',
            'ingresosServiciosHoy',
            'ingresosProductosHoy',
            'reservasTotales',
            'productos',
            'alertasStockCount',
            'rankingBarberos',
            'productosMasVendidos',
            'estaciones',
            'fechasSemana',
            'serieServicios',
            'serieProductos',
            'bloquesHoras',
            'flujoPorHora',
            'gastosHoy',
            'utilidadNetaHoy',
            'ultimosGastos',
            'resenasClientes',
            'promoConfig'
        ));
    }

    /**
     * HU-14: Reabastece el stock de un producto desde el panel administrativo.
     */
    public function reabastecer(Request $request, int $id)
    {
        $request->validate([
            'cantidad_ingreso' => 'required|integer|min:1|max:500',
        ]);

        $producto = Producto::findOrFail($id);
        $producto->increment('stock_actual', $request->cantidad_ingreso);

        return back()->with('success', "Reabastecimiento aplicado: +{$request->cantidad_ingreso} unidades agregadas a {$producto->nombre}.");
    }

    /**
     * HU-13: Actualiza el estado físico de una estación de entretenimiento.
     */
    public function cambiarEstadoEstacion(Request $request, int $id)
    {
        $request->validate([
            'estado' => 'required|in:disponible,ocupado,mantenimiento',
        ]);

        $estacion = EstacionGame::findOrFail($id);
        $estacion->update(['estado' => $request->estado]);

        return back()->with('success', "Estado de {$estacion->nombre} cambiado a: " . strtoupper($request->estado));
    }

    /**
     * Actualiza la configuración de promociones y Horas Valle.
     */
    public function actualizarPromocion(Request $request)
    {
        $request->validate([
            'porcentaje_descuento' => 'required|numeric|min:5|max:80',
            'hora_inicio'          => 'required|integer|min:7|max:21',
            'hora_fin'             => 'required|integer|gt:hora_inicio|max:22',
            'fecha_inicio'         => 'required|date',
            'fecha_fin'            => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $promo = ConfiguracionPromocion::first();
        $promo->update([
            'activo'               => $request->has('activo'),
            'porcentaje_descuento' => $request->porcentaje_descuento,
            'hora_inicio'          => $request->hora_inicio,
            'hora_fin'             => $request->hora_fin,
            'fecha_inicio'         => $request->fecha_inicio,
            'fecha_fin'            => $request->fecha_fin,
        ]);

        return back()->with('success', 'Configuración de Horas Valle actualizada correctamente.');
    }

    /**
     * Registra un nuevo gasto operativo en la base de datos.
     */
    public function registrarGasto(Request $request)
    {
        $validated = $request->validate([
            'concepto'    => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'detalle'     => 'nullable|string|max:255',
            'categoria'   => 'nullable|string|max:100',
            'tipo'        => 'nullable|string|max:100',
            'monto'       => 'required|numeric|min:0.01',
            'fecha'       => 'nullable|date',
        ]);

        $textoConcepto = $validated['concepto']
            ?? $validated['descripcion']
            ?? $validated['detalle']
            ?? 'Gasto operativo';

        $textoCategoria = $validated['categoria']
            ?? $validated['tipo']
            ?? 'General';

        $fechaGasto = $validated['fecha'] ?? now()->toDateString();

        $columnas = Schema::getColumnListing('gasto_operativos');
        $data = [];

        foreach (['concepto', 'descripcion', 'detalle', 'titulo', 'nombre'] as $col) {
            if (in_array($col, $columnas)) {
                $data[$col] = $textoConcepto;
                break;
            }
        }

        foreach (['categoria', 'tipo', 'rubro'] as $col) {
            if (in_array($col, $columnas)) {
                $data[$col] = $textoCategoria;
                break;
            }
        }

        foreach (['monto', 'importe', 'total', 'valor'] as $col) {
            if (in_array($col, $columnas)) {
                $data[$col] = $validated['monto'];
                break;
            }
        }

        foreach (['fecha', 'fecha_gasto'] as $col) {
            if (in_array($col, $columnas)) {
                $data[$col] = $fechaGasto;
                break;
            }
        }

        if (in_array('user_id', $columnas) && Auth::check()) {
            $data['user_id'] = Auth::id();
        }

        GastoOperativo::create($data);

        return redirect()->back()->with('success', 'Gasto operativo registrado correctamente.');
    }
}
