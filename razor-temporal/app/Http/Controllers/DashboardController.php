<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 3) & PROGRAMACIÓN EXTREMA (XP)
// PROYECTO DE GRADO: PLATAFORMA INTEGRAL "RAZOR BARBERÍA"
// CONTROLADOR LOGÍCO: MÓDULO ANALÍTICO (BUSINESS INTELLIGENCE)
// =========================================================================

class DashboardController extends Controller
{
    /**
     * -------------------------------------------------------------------------
     * HISTORIA DE USUARIO (SCRUM): Cuadro de Mando e Inteligencia de Negocio
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Recopila los datos operacionales de MySQL mediante agregaciones 
     * avanzadas (SUM, COUNT) y agrupaciones (GROUP BY) para transformarlos en 
     * métricas visuales interpretables por Chart.js en el frontend.
     * 
     * ENFOQUE METODOLÓGICO (XP - Diseño Simple y Valor del Software): Este módulo 
     * demuestra madurez de ingeniería. En lugar de procesar los datos en el cliente, 
     * las consultas se ejecutan directamente optimizadas en el servidor de MySQL.
     */
    public function obtenerMetricas()
    {
        // 1. CONSULTA DE INGRESOS MENSUALES (Gráfico de Líneas / Barras)
        // PROCESO: Suma las ventas totales netas agrupándolas por el año y mes actual.
        $ingresosMensuales = DB::table('ventas')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"), 
                DB::raw("SUM(total_neto) as total")
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('mes')
            ->orderBy('mes', 'ASC')
            ->get();

        // 2. CONSULTA DE SERVICIOS MÁS PEDIDOS (Gráfico de Pastel / Doughnut)
        // PROCESO: Cuenta cuántas citas se han marcado como 'completada' agrupándolas por servicio.
        $serviciosMasPedidos = DB::table('citas')
            ->join('servicios', 'citas.servicio_id', '=', 'servicios.id')
            ->select('servicios.nombre', DB::raw('COUNT(citas.id) as cantidad'))
            ->where('citas.estado', 'completada')
            ->groupBy('servicios.id', 'servicios.nombre')
            ->orderBy('cantidad', 'DESC')
            ->limit(5) // Extrae únicamente el Top 5 para un diseño limpio
            ->get();

        // 3. CONSULTA DE HORAS PICO EN LA AGENDA (Gráfico de Líneas Temporales)
        // PROCESO: Extrae y formatea el bloque de hora de las citas para contar la afluencia.
        $horasPico = DB::table('citas')
            ->select(
                DB::raw("TIME_FORMAT(hora, '%H:00') as hora_bloque"), 
                DB::raw('COUNT(id) as total_citas')
            )
            ->where('estado', 'completada')
            ->groupBy('hora_bloque')
            ->orderBy('total_citas', 'DESC')
            ->get();

        // 4. CONSULTA DE RANKING DE PELUQUEROS (Gráfico de Barras de Rendimiento)
        // PROCESO: Cuenta la cantidad de servicios completados por cada barbero para evaluar productividad.
        $rankingBarberos = DB::table('citas')
            ->join('barberos', 'citas.barbero_id', '=', 'barberos.id')
            ->select('barberos.nombre', DB::raw('COUNT(citas.id) as total_servicios'))
            ->where('citas.estado', 'completada')
            ->groupBy('barberos.id', 'barberos.nombre')
            ->orderBy('total_servicios', 'DESC')
            ->get();

        // Envía los datos estructurados en formato JSON listos para ser consumidos por Chart.js
        return response()->json([
            'ingresos'  => $ingresosMensuales,
            'servicios' => $serviciosMasPedidos,
            'horas'     => $horasPico,
            'barberos'  => $rankingBarberos
        ]);
    }
}