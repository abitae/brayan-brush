<table>
    <tbody>
        <tr>
            <th colspan="5">{{ $encomienda && $encomienda->fecha_envio ? $encomienda->fecha_envio->format('d/m/Y') : ($encomienda && $encomienda->fecha_creacion ? $encomienda->fecha_creacion->format('d/m/Y') : date('d/m/Y')) }}</th>
        </tr>
        <tr>
            <td colspan="1"></td>
            <th>CONDUCTOR</th>
            <td>{{ $encomienda && $encomienda->ruta && $encomienda->ruta->transportista ? $encomienda->ruta->transportista->name : ($encomienda && $encomienda->transportista ? $encomienda->transportista->name : 'N/A') }}</td>
            <th>MARCA DEL VEHICULO</th>
            <td>{{ $encomienda && $encomienda->ruta && $encomienda->ruta->vehiculo ? $encomienda->ruta->vehiculo->marca : ($encomienda && $encomienda->vehiculo ? $encomienda->vehiculo->marca : 'N/A') }}</td>
        </tr>
        <tr>
            <td colspan="1"></td>
            <th>DNI</th>
            <td>{{ $encomienda && $encomienda->ruta && $encomienda->ruta->transportista ? $encomienda->ruta->transportista->dni : ($encomienda && $encomienda->transportista ? $encomienda->transportista->dni : 'N/A') }}</td>
            <th>CONFIGURACION VEHICULAR</th>
            <td>{{ $encomienda && $encomienda->ruta && $encomienda->ruta->vehiculo ? $encomienda->ruta->vehiculo->tipo : ($encomienda && $encomienda->vehiculo ? $encomienda->vehiculo->tipo : 'N/A') }}</td>
        </tr>
        <tr>
            <td colspan="1"></td>
            <th>PLACA</th>
            <td>{{ $encomienda && $encomienda->ruta && $encomienda->ruta->vehiculo ? $encomienda->ruta->vehiculo->name : ($encomienda && $encomienda->vehiculo ? $encomienda->vehiculo->name : 'N/A') }}</td>
            <th>MTC</th>
            <td>{{ $encomienda && $encomienda->ruta && $encomienda->ruta->vehiculo ? $encomienda->ruta->vehiculo->mtc : ($encomienda && $encomienda->vehiculo ? ($encomienda->vehiculo->mtc ?? 'N/A') : 'N/A') }}</td>
        </tr>
        <tr>
            <td colspan="1"></td>
            <th>LICENCIA</th>
            <td>{{ $encomienda && $encomienda->ruta && $encomienda->ruta->transportista ? $encomienda->ruta->transportista->licencia : ($encomienda && $encomienda->transportista ? $encomienda->transportista->licencia : 'N/A') }}</td>
            <th>TELEF</th>
            <td>{{ $encomienda && $encomienda->ruta && $encomienda->ruta->transportista ? ($encomienda->ruta->transportista->tipo ?? 'N/A') : ($encomienda && $encomienda->transportista ? ($encomienda->transportista->tipo ?? 'N/A') : 'N/A') }}</td>
        </tr>
    </tbody>
</table>

@if($encomiendas->count() > 0)
<table>
    <thead>
        <tr>
            <th>NRO GUIA</th>
            <th>GUIA CLIENTE</th>
            <th>REMITENTE</th>
            <th>TELEFONO</th>
            <th>DESTINATARIO</th>
            <th>TELEFONO</th>
            <th>DIRECCION</th>
            <th>CANTIDAD</th>
            <th>PAQUETES</th>
            <th>MONTO</th>
            <th>AGENCIA</th>
            <th>PAGO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($encomiendas as $encomiendaLibre)
            <tr>
                <td>{{ $encomiendaLibre->code }}</td>
                <td>
                    @php
                        $docsTraslado = json_decode($encomiendaLibre->docsTraslado, true);
                    @endphp
                    @if(is_array($docsTraslado) && count($docsTraslado) > 0)
                        @foreach($docsTraslado as $doc)
                            {{ $doc['documento'] ?? 'S/D' }}
                        @endforeach
                    @else
                        S/D
                    @endif
                </td>
                <td>{{ $encomiendaLibre->remitente ? $encomiendaLibre->remitente->name : 'N/A' }}</td>
                <td>{{ $encomiendaLibre->remitente ? $encomiendaLibre->remitente->phone : 'N/A' }}</td>
                <td>{{ $encomiendaLibre->destinatario ? $encomiendaLibre->destinatario->name : 'N/A' }}</td>
                <td>{{ $encomiendaLibre->destinatario ? $encomiendaLibre->destinatario->phone : 'N/A' }}</td>
                <td>{{ $encomiendaLibre->destinatario ? $encomiendaLibre->destinatario->address : 'N/A' }}</td>
                <td>{{ $encomiendaLibre->cantidad ?? 0 }}</td>
                <td>
                    @php
                        $packsLibre = '';
                    @endphp
                    @forelse ($encomiendaLibre->paquetes as $paquete)
                        {{ $packsLibre . '' . ($paquete->description ?? '') . '(' . ($paquete->cantidad ?? 0) . ')' . '(' . ($paquete->amount ?? 0) . ')-' }}
                    @empty
                        -
                    @endforelse
                </td>
                <td>{{ $encomiendaLibre->monto ?? 0 }}</td>
                <td>{{ $encomiendaLibre->isHome ? 'DOMICILIO' : 'AGENCIA' }}</td>
                <td>{{ $encomiendaLibre->estado_pago ?? 'N/A' }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="10">
                Total: {{ $encomiendas->sum('monto') }}
            </td>
        </tr>
    </tbody>
</table>
@endif

@if($encomiendasIsHome->count() > 0)
<table>
    <thead>
        <tr>
            <th>NRO GUIA</th>
            <th>GUIA CLIENTE</th>
            <th>REMITENTE</th>
            <th>TELEFONO</th>
            <th>DESTINATARIO</th>
            <th>TELEFONO</th>
            <th>DIRECCION</th>
            <th>CANTIDAD</th>
            <th>PAQUETES</th>
            <th>MONTO</th>
            <th>DOMICILIO</th>
            <th>PAGO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($encomiendasIsHome as $encomienda)
            <tr>
                <td>{{ $encomienda->code }}</td>
                <td>
                    @php
                        $docsTraslado = json_decode($encomienda->docsTraslado, true);
                    @endphp
                    @if(is_array($docsTraslado) && count($docsTraslado) > 0)
                        @foreach($docsTraslado as $doc)
                            {{ $doc['documento'] ?? 'S/D' }}
                        @endforeach
                    @else
                        S/D
                    @endif
                </td>
                <td>{{ $encomienda->remitente ? $encomienda->remitente->name : 'N/A' }}</td>
                <td>{{ $encomienda->remitente ? $encomienda->remitente->phone : 'N/A' }}</td>
                <td>{{ $encomienda->destinatario ? $encomienda->destinatario->name : 'N/A' }}</td>
                <td>{{ $encomienda->destinatario ? $encomienda->destinatario->phone : 'N/A' }}</td>
                <td>{{ $encomienda->destinatario ? $encomienda->destinatario->address : 'N/A' }}</td>
                <td>{{ $encomienda->cantidad ?? 0 }}</td>
                <td>
                    @php
                        $packs = '';
                    @endphp
                    @forelse ($encomienda->paquetes as $paquete)
                        {{ $packs . '' . ($paquete->description ?? '') . '(' . ($paquete->cantidad ?? 0) . ')' . '(' . ($paquete->amount ?? 0) . ')-' }}
                    @empty
                        -
                    @endforelse
                </td>
                <td>{{ $encomienda->monto ?? 0 }}</td>
                <td>{{ $encomienda->isHome ? 'DOMICILIO' : 'AGENCIA' }}</td>
                <td>{{ $encomienda->estado_pago ?? 'N/A' }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="10">
                Total: {{ $encomiendasIsHome->sum('monto') }}
            </td>
        </tr>
    </tbody>
</table>
@endif

@if($encomiendasIsReturn->count() > 0)
<table>
    <thead>
        <tr>
            <th>NRO GUIA</th>
            <th>GUIA CLIENTE</th>
            <th>REMITENTE</th>
            <th>TELEFONO</th>
            <th>DESTINATARIO</th>
            <th>TELEFONO</th>
            <th>DIRECCION</th>
            <th>CANTIDAD</th>
            <th>PAQUETES</th>
            <th>MONTO</th>
            <th>RETORNO</th>
            <th>PAGO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($encomiendasIsReturn as $encomiendaReturn)
            <tr>
                <td>{{ $encomiendaReturn->code }}</td>
                <td>
                    @php
                        $docsTraslado = json_decode($encomiendaReturn->docsTraslado, true);
                    @endphp
                    @if(is_array($docsTraslado) && count($docsTraslado) > 0)
                        @foreach($docsTraslado as $doc)
                            {{ $doc['documento'] ?? 'S/D' }}
                        @endforeach
                    @else
                        S/D
                    @endif
                </td>
                <td>{{ $encomiendaReturn->remitente ? $encomiendaReturn->remitente->name : 'N/A' }}</td>
                <td>{{ $encomiendaReturn->remitente ? $encomiendaReturn->remitente->phone : 'N/A' }}</td>
                <td>{{ $encomiendaReturn->destinatario ? $encomiendaReturn->destinatario->name : 'N/A' }}</td>
                <td>{{ $encomiendaReturn->destinatario ? $encomiendaReturn->destinatario->phone : 'N/A' }}</td>
                <td>{{ $encomiendaReturn->destinatario ? $encomiendaReturn->destinatario->address : 'N/A' }}</td>
                <td>{{ $encomiendaReturn->cantidad ?? 0 }}</td>
                <td>
                    @php
                        $packs = '';
                    @endphp
                    @forelse ($encomiendaReturn->paquetes as $paquete)
                        {{ $packs . '' . ($paquete->description ?? '') . '(' . ($paquete->cantidad ?? 0) . ')' . '(' . ($paquete->amount ?? 0) . ')-' }}
                    @empty
                        -
                    @endforelse
                </td>
                <td>{{ $encomiendaReturn->monto ?? 0 }}</td>
                <td>{{ $encomiendaReturn->isReturn ? 'RETORNO' : 'NO' }}</td>
                <td>{{ $encomiendaReturn->estado_pago ?? 'N/A' }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="10">
                Total: {{ $encomiendasIsReturn->sum('monto') }}
            </td>
        </tr>
    </tbody>
</table>
@endif

