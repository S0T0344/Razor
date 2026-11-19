<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota de Venta #{{ str_pad($cita->id, 5, '0', STR_PAD_LEFT) }} | RAZOR Barbería</title>
    <link rel="stylesheet" href="{{ asset('css/razor.css') }}">
</head>
<body class="login-page">
    <div class="card login-card" style="max-width: 520px;">
        <div class="brand-header">
            <h3 class="mb-0 fw-bold">RAZOR BARBERÍA</h3>
            <small>NOTA DE VENTA INTERNA N° {{ str_pad($cita->id, 5, '0', STR_PAD_LEFT) }}</small>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><span>Cliente:</span> <strong>{{ $cita->cliente->name }}</strong></li>
                <li class="list-group-item"><span>Fecha / Hora:</span> <strong>{{ $cita->fecha }} ({{ substr($cita->hora, 0, 5) }})</strong></li>
                <li class="list-group-item"><span>Barbero Asignado:</span> <strong>{{ $cita->barbero->nombre }}</strong></li>
                <li class="list-group-item"><span>Método de Pago:</span> <strong style="text-transform: uppercase;">{{ $cita->metodo_pago }}</strong></li>
            </ul>

            <table class="table">
                <thead>
                    <tr>
                        <th>Detalle</th>
                        <th style="text-align: right;">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            {{ $cita->servicio->nombre }}
                            @if($cita->es_hora_valle)
                                <span class="badge bg-success">Hora Valle Aplicada</span>
                            @endif
                        </td>
                        <td style="text-align: right;">Bs. {{ number_format($cita->precio_final, 2) }}</td>
                    </tr>
                    @foreach($cita->ventas as $venta)
                        <tr>
                            <td>{{ $venta->cantidad }}x {{ $venta->producto->nombre }} (Extra POS)</td>
                            <td style="text-align: right;">Bs. {{ number_format($venta->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @php $totalGeneral = $cita->precio_final + $cita->ventas->sum('total'); @endphp
            <div class="d-flex" style="justify-content: space-between; margin-top: 15px; font-size: 1.15rem;">
                <strong>TOTAL PAGADO:</strong>
                <strong class="text-danger">Bs. {{ number_format($totalGeneral, 2) }}</strong>
            </div>

            <div class="d-flex" style="margin-top: 20px; gap: 10px;">
                <button onclick="window.print()" class="btn btn-primary w-100">Imprimir / Guardar PDF</button>
                <a href="{{ route('cliente.portal') }}" class="btn btn-dark w-100" style="text-align: center; text-decoration: none;">Volver</a>
            </div>
        </div>
    </div>
</body>
</html>