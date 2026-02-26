<table>
    <thead>
        <tr>
            <th colspan="17" style="font-size: 16px; font-weight: bold; text-align: center;">REPORTE DE ENCOMIENDAS</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f4f6;">CÓDIGO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">GUÍA CLIENTE</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">REMITENTE</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">DESTINATARIO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">TELÉFONO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">CANTIDAD</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">PAQUETES</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">MONTO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">DESCUENTO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">MÉTODO DE PAGO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">TIPO DE PAGO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">TIPO COMPROBANTE</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">ESTADO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">RETORNO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">DOMICILIO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">FECHA REGISTRO</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">FECHA ENTREGA</th>
        </tr>
    </thead>
    <tbody>
        @foreach($encomiendas as $encomienda)
        <tr>
            <td>{{ $encomienda->code }}</td>
            <td>
                @php
                    $docs = $encomienda->docsTraslado ? json_decode($encomienda->docsTraslado, true) : null;
                @endphp
                @if($docs && is_array($docs) && count($docs) > 0)
                    @foreach($docs as $doc)
                        {{ is_array($doc) ? json_encode($doc) : $doc }}@if(!$loop->last), @endif
                    @endforeach
                @else
                    NINGUNO
                @endif
            </td>
            <td>{{ $encomienda->remitente->name ?? '' }}</td>
            <td>{{ $encomienda->destinatario->name ?? '' }}</td>
            <td>{{ $encomienda->destinatario->phone ?? '' }}</td>
            <td>{{ $encomienda->cantidad }}</td>
            <td>
                @php
                    $packs = [];
                    foreach ($encomienda->paquetes ?? [] as $paquete) {
                        $packs[] = $paquete->description . '(' . $paquete->cantidad . ')(' . $paquete->amount . ')';
                    }
                @endphp
                {{ implode(' - ', $packs) }}
            </td>
            <td>S/ {{ number_format($encomienda->monto, 2) }}</td>
            <td>S/ {{ number_format($encomienda->monto_descuento ?? 0, 2) }}</td>
            <td>{{ $encomienda->metodo_pago ?? 'EFECTIVO' }}</td>
            <td>{{ $encomienda->tipo_pago ?? 'CONTADO' }}</td>
            <td>{{ $encomienda->tipo_comprobante ?? 'NINGUNO' }}</td>
            <td>{{ $encomienda->estado_encomienda ?? 'REGISTRADO' }}</td>
            <td>{{ $encomienda->isReturn ? 'SI' : 'NO' }}</td>
            <td>{{ $encomienda->isHome ? 'SI' : 'NO' }}</td>
            <td>{{ $encomienda->fecha_creacion ? \Carbon\Carbon::parse($encomienda->fecha_creacion)->format('d-m-Y H:i') : '' }}</td>
            <td>{{ $encomienda->fecha_entrega ? \Carbon\Carbon::parse($encomienda->fecha_entrega)->format('d-m-Y H:i') : '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
