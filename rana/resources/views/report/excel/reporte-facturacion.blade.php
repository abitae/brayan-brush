<table>
    <thead>
        <tr>
            <th colspan="4" style="font-size: 16px; font-weight: bold; text-align: center;">REPORTE DE FACTURACIÓN</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">Resumen</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #dbeafe;">Total Facturas</th>
            <th style="font-weight: bold; background-color: #dcfce7;">Total Tickets</th>
            <th style="font-weight: bold; background-color: #fee2e2;">Total Notas</th>
            <th style="font-weight: bold; background-color: #f3e8ff;">Total General</th>
        </tr>
        <tr>
            <td>S/ {{ number_format($totalFacturas, 2) }}</td>
            <td>S/ {{ number_format($totalTickets, 2) }}</td>
            <td>S/ {{ number_format($totalNotas, 2) }}</td>
            <td>S/ {{ number_format($totalGeneral, 2) }}</td>
        </tr>
    </thead>
</table>

@if($invoices->count() > 0)
<table style="margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="4" style="font-weight: bold; background-color: #f3f4f6;">FACTURAS Y BOLETAS</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f4f6;">Documento</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Cliente</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Monto</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->serie }}-{{ $invoice->correlativo }}</td>
            <td>{{ $invoice->client->name ?? 'N/A' }}</td>
            <td>S/ {{ number_format($invoice->mtoImpVenta, 2) }}</td>
            <td>{{ $invoice->created_at->format('d-m-Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($notes->count() > 0)
<table style="margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="4" style="font-weight: bold; background-color: #f3f4f6;">NOTAS DE CRÉDITO</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f4f6;">Documento</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Cliente</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Monto</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($notes as $note)
        <tr>
            <td>{{ $note->serie }}-{{ $note->correlativo }}</td>
            <td>{{ $note->client->name ?? 'N/A' }}</td>
            <td>S/ {{ number_format($note->mtoImpVenta, 2) }}</td>
            <td>{{ $note->created_at->format('d-m-Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($tickets->count() > 0)
<table style="margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="4" style="font-weight: bold; background-color: #f3f4f6;">TICKETS</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f4f6;">Documento</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Cliente</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Monto</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tickets as $ticket)
        <tr>
            <td>{{ $ticket->serie }}-{{ $ticket->correlativo }}</td>
            <td>{{ $ticket->client->name ?? 'N/A' }}</td>
            <td>S/ {{ number_format($ticket->mtoImpVenta, 2) }}</td>
            <td>{{ $ticket->created_at->format('d-m-Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
