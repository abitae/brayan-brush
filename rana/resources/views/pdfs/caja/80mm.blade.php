<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 9pt;
            margin-bottom: 5px;
        }
        .info {
            margin-bottom: 5px;
            font-size: 9pt;
            padding: 3px 0;
        }
        .info p {
            margin: 2px 0;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8pt;
        }
        th, td {
            border: 0.5px solid #ddd;
            padding: 2px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 7pt;
        }
        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 8px;
            font-size: 10pt;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8pt;
            border-top: 1px dashed #000;
            padding-top: 8px;
        }
        .section-title {
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 4px;
            font-size: 9pt;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 8pt;
        }
        .summary-label {
            font-weight: bold;
        }
        .summary-value {
            text-align: right;
        }
        .final-total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 0;
            margin-top: 8px;
            font-size: 11pt;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">CIERRE DE CAJA</div>
        <div class="subtitle">Caja #{{ $caja->id }}</div>
        <div class="subtitle">{{ $caja->monto_cierre ? $caja->updated_at->format('d/m/Y H:i:s') : date('d/m/Y H:i:s') }}</div>
    </div>

    <div class="info">
        <p><strong>Usuario:</strong> {{ $caja->user->name }}</p>
        <p><strong>Fecha Apertura:</strong> {{ $caja->created_at->format('d/m/Y H:i:s') }}</p>
        @if($caja->monto_cierre)
            <p><strong>Fecha Cierre:</strong> {{ $caja->updated_at->format('d/m/Y H:i:s') }}</p>
        @else
            <p><strong>Estado:</strong> Caja Abierta</p>
        @endif
    </div>

    <div class="divider"></div>

    <div class="section-title">INGRESOS POR MÉTODO DE PAGO</div>
    @php
        $metodosPago = ['EFECTIVO', 'YAPE', 'TARJETA', 'CHEQUE', 'TRANSFERENCIA', 'OTRO'];
        $entriesPorMetodo = $caja->entries->groupBy(function($entry) {
            return strtoupper($entry->metodo_pago);
        });
    @endphp
    @if($caja->entries->count() > 0)
        @foreach($metodosPago as $metodo)
            @php
                $entriesMetodo = $entriesPorMetodo->get($metodo, collect());
            @endphp
            @if($entriesMetodo->count() > 0)
                <div style="margin-top: 5px; margin-bottom: 3px;">
                    <strong style="font-size: 8pt; text-transform: uppercase;">{{ $metodo }}</strong>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th style="text-align: center;">Tipo</th>
                            <th style="text-align: right;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entriesMetodo as $entry)
                        <tr>
                            <td style="font-size: 7pt;">{{ Str::limit($entry->description, 20) }}</td>
                            <td style="text-align: center; font-size: 7pt;">{{ $entry->tipoEntry?->name ?? '-' }}</td>
                            <td style="text-align: right; font-size: 7pt;">S/ {{ number_format($entry->monto_entry, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="summary-row" style="margin-bottom: 5px;">
                    <span class="summary-label">Subtotal {{ $metodo }}:</span>
                    <span class="summary-value">S/ {{ number_format($entriesMetodo->sum('monto_entry'), 2) }}</span>
                </div>
            @endif
        @endforeach
        <div class="summary-row" style="margin-top: 8px; border-top: 1px solid #000; padding-top: 5px;">
            <span class="summary-label">TOTAL INGRESOS:</span>
            <span class="summary-value">S/ {{ number_format($caja->entries->sum('monto_entry'), 2) }}</span>
        </div>
    @else
        <p style="font-size: 8pt; text-align: center;">No hay ingresos registrados</p>
    @endif

    <div class="divider"></div>

    <div class="section-title">EGRESOS POR MÉTODO DE PAGO</div>
    @php
        $exitsPorMetodo = $caja->exits->groupBy(function($exit) {
            return strtoupper($exit->metodo_pago);
        });
    @endphp
    @if($caja->exits->count() > 0)
        @foreach($metodosPago as $metodo)
            @php
                $exitsMetodo = $exitsPorMetodo->get($metodo, collect());
            @endphp
            @if($exitsMetodo->count() > 0)
                <div style="margin-top: 5px; margin-bottom: 3px;">
                    <strong style="font-size: 8pt; text-transform: uppercase;">{{ $metodo }}</strong>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th style="text-align: center;">Tipo</th>
                            <th style="text-align: right;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exitsMetodo as $exit)
                        <tr>
                            <td style="font-size: 7pt;">{{ Str::limit($exit->description, 20) }}</td>
                            <td style="text-align: center; font-size: 7pt;">{{ $exit->tipoExit?->name ?? '-' }}</td>
                            <td style="text-align: right; font-size: 7pt;">S/ {{ number_format($exit->monto_exit, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="summary-row" style="margin-bottom: 5px;">
                    <span class="summary-label">Subtotal {{ $metodo }}:</span>
                    <span class="summary-value">S/ {{ number_format($exitsMetodo->sum('monto_exit'), 2) }}</span>
                </div>
            @endif
        @endforeach
        <div class="summary-row" style="margin-top: 8px; border-top: 1px solid #000; padding-top: 5px;">
            <span class="summary-label">TOTAL EGRESOS:</span>
            <span class="summary-value">S/ {{ number_format($caja->exits->sum('monto_exit'), 2) }}</span>
        </div>
    @else
        <p style="font-size: 8pt; text-align: center;">No hay egresos registrados</p>
    @endif

    <div class="divider"></div>

    <div class="section-title">RESUMEN POR MÉTODO DE PAGO</div>
    @php
        $resumenPorMetodo = [];
        foreach($metodosPago as $metodo) {
            $ingresosMetodo = $entriesPorMetodo->get($metodo, collect())->sum('monto_entry');
            $egresosMetodo = $exitsPorMetodo->get($metodo, collect())->sum('monto_exit');
            $balanceMetodo = $ingresosMetodo - $egresosMetodo;
            if($ingresosMetodo > 0 || $egresosMetodo > 0) {
                $resumenPorMetodo[$metodo] = [
                    'ingresos' => $ingresosMetodo,
                    'egresos' => $egresosMetodo,
                    'balance' => $balanceMetodo
                ];
            }
        }
    @endphp
    @if(count($resumenPorMetodo) > 0)
        @foreach($resumenPorMetodo as $metodo => $resumen)
            <div style="margin-top: 5px; margin-bottom: 3px;">
                <strong style="font-size: 8pt; text-transform: uppercase;">{{ $metodo }}</strong>
            </div>
            <div class="summary-row">
                <span class="summary-label" style="font-size: 7pt;">Ingresos:</span>
                <span class="summary-value" style="font-size: 7pt;">S/ {{ number_format($resumen['ingresos'], 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label" style="font-size: 7pt;">Egresos:</span>
                <span class="summary-value" style="font-size: 7pt;">S/ {{ number_format($resumen['egresos'], 2) }}</span>
            </div>
            <div class="summary-row" style="margin-bottom: 5px; border-bottom: 1px dashed #000; padding-bottom: 3px;">
                <span class="summary-label" style="font-size: 7pt;">Balance:</span>
                <span class="summary-value" style="font-size: 7pt; font-weight: bold;">S/ {{ number_format($resumen['balance'], 2) }}</span>
            </div>
        @endforeach
    @endif

    <div class="divider"></div>

    <div class="section-title">RESUMEN GENERAL</div>
    <div class="summary-row">
        <span class="summary-label">Monto Apertura:</span>
        <span class="summary-value">S/ {{ number_format($caja->monto_apertura, 2) }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Total Ingresos:</span>
        <span class="summary-value">S/ {{ number_format($caja->entries->sum('monto_entry'), 2) }}</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Total Egresos:</span>
        <span class="summary-value">S/ {{ number_format($caja->exits->sum('monto_exit'), 2) }}</span>
    </div>
    <div class="final-total" style="text-align: center;">
        <div>BALANCE FINAL</div>
        <div>S/ {{ number_format($caja->monto_apertura + $caja->entries->sum('monto_entry') - $caja->exits->sum('monto_exit'), 2) }}</div>
    </div>
    @if($caja->monto_cierre)
        <div class="summary-row" style="margin-top: 5px;">
            <span class="summary-label">Monto Cierre:</span>
            <span class="summary-value">S/ {{ number_format($caja->monto_cierre, 2) }}</span>
        </div>
    @endif

    <div class="footer">
        <p>Documento generado el {{ date('d/m/Y H:i:s') }}</p>
        <p>Gracias por su confianza</p>
    </div>
</body>
</html>

