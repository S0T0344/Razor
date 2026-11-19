<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// =========================================================================
// METODOLOGÍA ÁGILE: SCRUM (SPRINT 2) & PROGRAMACIÓN EXTREMA (XP)
// CONTROLADOR LOGÍCO: PUNTO DE VENTA (POS) E INVENTARIO AUTOMATIZADO
// =========================================================================

class VentaController extends Controller
{
    /**
     * -------------------------------------------------------------------------
     * HISTORIA DE USUARIO (SCRUM): Registro de Ventas en el POS
     * -------------------------------------------------------------------------
     * PROCESO TÉCNICO: Método POST. Recibe la venta, procesa los montos y activa
     * una transacción en MySQL para asegurar que el dinero y el stock cuadren.
     * 
     * ENFOQUE XP (Diseño Simple y Robustez): Si el cliente compra un producto 
     * físico, el sistema descuenta automáticamente las unidades vendidas del stock.
     */
    public function registrarVenta(Request $request)
    {
        // 1. Validar rigurosamente los datos que entran desde la caja registradora web
        $request->validate([
            'user_id'            => 'nullable|integer|exists:users,id',
            'total_bruto'        => 'required|numeric',
            'descuento_aplicado' => 'required|numeric',
            'total_neto'         => 'required|numeric',
            'tipo_venta'         => 'required|in:servicio,producto,mixto,zona_game',
            'producto_id'        => 'nullable|integer|exists:productos,id', // Opcional (solo si compra producto)
            'cantidad'           => 'nullable|integer|min:1',
        ]);

        // 2. Uso de Transacciones SQL (Rigor Técnico) para evitar datos corruptos
        DB::beginTransaction();

        try {
            // Verificar stock antes de vender si es un producto físico
            if ($request->tipo_venta == 'producto' || $request->tipo_venta == 'mixto') {
                $producto = Producto::find($request->producto_id);
                
                if (!$producto || $producto->stock < $request->cantidad) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente en el inventario para el producto seleccionado.'
                    ], 422);
                }

                // OPERACIÓN MATEMÁTICA AUTOMATIZADA: Restar las unidades del inventario
                $producto->stock = $producto->stock - $request->cantidad;
                $producto->save();
            }

            // 3. Guardar la factura de venta en MySQL
            $venta = Venta::create([
                'user_id'            => $request->user_id,
                'total_bruto'        => $request->total_bruto,
                'descuento_aplicado' => $request->descuento_aplicado,
                'total_neto'         => $request->total_neto,
                'tipo_venta'         => $request->tipo_venta,
            ]);

            DB::commit(); // Confirmar cambios en MySQL de manera segura

            return response()->json([
                'success' => true,
                'message' => '¡Venta procesada con éxito y stock actualizado en la barbería RAZOR!',
                'data'    => $venta
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Si algo falla, revierte los cambios para proteger la base de datos
            return response()->json([
                'success' => false,
                'message' => 'Error crítico al procesar la venta: ' . $e->getMessage()
            ], 500);
        }
    }
}