<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Encomienda #{{ $encomienda->code ?? '-' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 8px; }
        .logo { max-width: 120px; margin-bottom: 4px; }
        .company-info { font-size: 10px; margin-bottom: 6px; }
        .ticket-number { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px 0; text-align: center; font-weight: bold; font-size: 11px; }
        .customer-info { font-size: 10px; margin: 3px 0; border-bottom: 1px solid #eee; padding-top: 3px; }
        .section-title { font-weight: bold; font-size: 10px; margin-top: 6px; }
        .items-table { width: 100%; border-collapse: collapse; font-size: 9px; margin: 8px 0; }
        .items-table th, .items-table td { padding: 2px; text-align: left; }
        .items-table th { background-color: #f0f0f0; font-size: 9px; }
        .totals { font-size: 10px; text-align: right; margin: 8px 0; border-top: 1px solid #000; padding-top: 4px; }
        .qr-code { text-align: center; margin: 8px 0; }
        .qr-code img { width: 45px; }
        .footer { text-align: center; font-size: 9px; margin-top: 8px; border-top: 1px solid #000; padding-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        @if($encomienda->company_logo)
            <img src="{{ $encomienda->company_logo }}" alt="Logo" class="logo">
        @endif
        <div class="company-info">
            <strong>{{ $encomienda->company_name ?? 'CORPORACIÓN LOGÍSTICA BRAYAN BRUSH EIRL' }}</strong><br>
            R.U.C.: {{ $encomienda->company_ruc ?? '20612345678' }}<br>
            {{ $encomienda->sucursal_remitente->address ?? 'Dirección de la sucursal' }}<br>
            Telf: {{ $encomienda->sucursal_remitente->phone ?? '-' }}<br>
            Email: {{ $encomienda->sucursal_remitente->email ?? '-' }}
        </div>
    </div>

    <div class="ticket-number">
        ENCOMIENDA N° {{ $encomienda->code ?? '-' }}<br>
        {{ $encomienda->estado_pago ?? 'PENDIENTE' }}<br>
        @if ($encomienda->isHome ?? false)
            DOMICILIO
        @else
            AGENCIA
        @endif
    </div>

    <div class="customer-info">
        <span class="section-title">FECHAS</span><br>
        Emisión: {{ $encomienda->fecha_creacion ? \Carbon\Carbon::parse($encomienda->fecha_creacion)->format('Y-m-d') : date('Y-m-d') }}<br>
        @if($encomienda->fecha_envio)
        Envío: {{ \Carbon\Carbon::parse($encomienda->fecha_envio)->format('Y-m-d') }}<br>
        @endif
    </div>
    
    <div class="customer-info">
        <span class="section-title">REMITENTE</span><br>
        {{ $encomienda->remitente->name ?? '-' }}<br>
        {{ strtoupper(($encomienda->remitente->type_code ?? '') == 1 ? 'DNI' : 'RUC') }}: {{ $encomienda->remitente->code ?? '-' }}<br>
        @if ($encomienda->remitente->address)
            Dirección: {{ $encomienda->remitente->address }}<br>
        @endif
    </div>
    
    <div class="customer-info">
        <span class="section-title">DESTINATARIO</span><br>
        {{ $encomienda->destinatario->name ?? '-' }}<br>
        {{ strtoupper(($encomienda->destinatario->type_code ?? '') == 1 ? 'DNI' : 'RUC') }}: {{ $encomienda->destinatario->code ?? '-' }}<br>
        @if ($encomienda->destinatario->address)
            Dirección: {{ $encomienda->destinatario->address }}<br>
        @endif
        @if (!empty($encomienda->docsTraslado))
            <span class="section-title">DOCUMENTOS DE TRASLADO</span><br>
            @php $docsTraslado = is_array($encomienda->docsTraslado) ? $encomienda->docsTraslado : json_decode($encomienda->docsTraslado, true); @endphp
            @forelse ($docsTraslado as $doc)
                {{ $doc['tipoDoc'] ?? $doc['tipo'] ?? '-' }}: {{ $doc['documento'] ?? $doc['numero'] ?? '-' }} - {{ $doc['ruc'] ?? $doc['ruc_emisor'] ?? '-' }}<br>
            @empty
                Sin documentos<br>
            @endforelse
        @endif
    </div>
    
    <div class="customer-info">
        <span class="section-title">ENVÍO</span><br>
        <strong>ORIGEN:</strong> {{ $encomienda->sucursal_remitente->address ?? '-' }}<br>
        <strong>DESTINO:</strong> {{ $encomienda->sucursal_destinatario->address ?? '-' }}
    </div>
    
    <div class="customer-info">
        <span class="section-title">TRANSPORTE Y CONDUCTOR</span><br>
        <strong>PLACA:</strong> {{ $encomienda->vehiculo->name ?? '-' }}<br>
        <strong>DNI:</strong> {{ $encomienda->transportista->dni ?? '-' }}<br>
        <strong>NOMBRE:</strong> {{ $encomienda->transportista->name ?? '-' }}<br>
        <strong>LICENCIA:</strong> {{ $encomienda->transportista->licencia ?? '-' }}
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th style="text-align: right">Cant</th>
                <th style="text-align: right">Precio</th>
                <th style="text-align: right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($encomienda->paquetes as $detail)
                <tr>
                    <td>{{ $detail->description ?? '-' }}</td>
                    <td style="text-align: right">{{ $detail->cantidad ?? '-' }}</td>
                    <td style="text-align: right">{{ number_format($detail->amount ?? 0, 2) }}</td>
                    <td style="text-align: right">{{ number_format($detail->sub_total ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;">Sin ítems</td></tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="totals">
        <table style="width: 100%">
            @php
                $subtotal = $encomienda->paquetes->sum('sub_total') ?? 0;
                $baseImponible = $subtotal / 1.18;
                $igv = $subtotal - $baseImponible;
            @endphp
            <tr>
                <td style="text-align: left">Gravada:</td>
                <td style="text-align: right">S/ {{ number_format($baseImponible, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align: left">IGV (18%):</td>
                <td style="text-align: right">S/ {{ number_format($igv, 2) }}</td>
            </tr>
            @if (($encomienda->monto_descuento ?? 0) > 0)
                <tr>
                    <td style="text-align: left">Descuento:</td>
                    <td style="text-align: right">S/ {{ number_format($encomienda->monto_descuento, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td style="text-align: left"><strong>Total:</strong></td>
                <td style="text-align: right"><strong>S/ {{ number_format($encomienda->monto ?? 0, 2) }}</strong></td>
            </tr>
        </table>
    </div>
    
    <div class="qr-code">
        <img style="width: 50px" src="./img/terminos_qr.jpg" alt="Código QR">
    </div>
    
    <div class="footer">
        Gracias por su compra<br>
        Políticas de Envío<br>
        {{ $encomienda->company_name ?? 'Corporación Logística Brayan Brush EIRL' }}<br>
        Usuario: {{ $encomienda->user->name ?? '-' }}
    </div>
</body>
</html>
