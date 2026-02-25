<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Encomienda #{{ $encomienda->code }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px; 
            margin: 0; 
            padding: 5px; 
        }
        .header { 
            text-align: center; 
            font-weight: bold; 
            font-size: 14px; 
            margin-bottom: 8px; 
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .section { 
            margin-bottom: 8px; 
            padding: 2px 0;
        }
        .label { 
            font-weight: bold; 
            color: #333;
        }
        .info {
            margin-left: 5px;
        }
        .divider {
            border-top: 1px dashed #ccc;
            margin: 5px 0;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 5px; 
            font-size: 9px;
        }
        th, td { 
            border: 1px solid #ccc; 
            padding: 2px; 
            text-align: left; 
        }
        th { 
            background: #f5f5f5; 
            font-weight: bold;
        }
        .total {
            font-weight: bold;
            font-size: 12px;
            text-align: right;
            margin-top: 5px;
            border-top: 2px solid #000;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">TICKET DE ENCOMIENDA</div>
    
    <div class="section">
        <span class="label">Código:</span> 
        <span class="info">{{ $encomienda->code ?? 'N/A' }}</span>
    </div>
    
    <div class="section">
        <span class="label">Fecha:</span> 
        <span class="info">{{ $encomienda->fecha_creacion ? \Carbon\Carbon::parse($encomienda->fecha_creacion)->format('d/m/Y H:i') : 'N/A' }}</span>
    </div>
    
    <div class="divider"></div>
    
    <div class="section">
        <span class="label">ORIGEN:</span><br>
        <span class="info">{{ $encomienda->sucursal_remitente->name ?? 'N/A' }}</span>
    </div>
    
    <div class="section">
        <span class="label">DESTINO:</span><br>
        <span class="info">{{ $encomienda->sucursal_destinatario->name ?? 'N/A' }}</span>
    </div>
    
    <div class="divider"></div>
    
    <div class="section">
        <span class="label">REMITENTE:</span><br>
        <span class="info">{{ $encomienda->remitente->name ?? 'N/A' }}</span>
    </div>
    
    <div class="section">
        <span class="label">DESTINATARIO:</span><br>
        <span class="info">{{ $encomienda->destinatario->name ?? 'N/A' }}</span>
    </div>
    
    @if($encomienda->transportista)
    <div class="section">
        <span class="label">Transportista:</span> 
        <span class="info">{{ $encomienda->transportista->name ?? 'N/A' }}</span>
    </div>
    @endif
    
    @if($encomienda->vehiculo)
    <div class="section">
        <span class="label">Vehículo:</span> 
        <span class="info">{{ $encomienda->vehiculo->name ?? 'N/A' }}</span>
    </div>
    @endif
    
    @if($encomienda->paquetes && count($encomienda->paquetes) > 0)
    <div class="divider"></div>
    <div class="section">
        <span class="label">PAQUETES:</span>
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Peso</th>
                    <th>Cant.</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($encomienda->paquetes as $paquete)
                    <tr>
                        <td>{{ $paquete->descripcion ?? '-' }}</td>
                        <td>{{ $paquete->peso ?? '-' }} kg</td>
                        <td>{{ $paquete->cantidad ?? '-' }}</td>
                        <td>S/ {{ number_format($paquete->valor ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <div class="divider"></div>
    
    <div class="total">
        <span class="label">TOTAL:</span> S/ {{ number_format($encomienda->monto ?? 0, 2) }}
    </div>
    
    <div class="divider"></div>
    
    <div style="text-align: center; font-size: 8px; color: #666; margin-top: 10px;">
        <p>Gracias por confiar en nuestros servicios</p>
        <p>{{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
