<table>
    <thead>
        <tr>
            <th colspan="8" style="font-size: 16px; font-weight: bold; text-align: center;">REPORTE DE CLIENTES</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f4f6;">#</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Documento</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Tipo</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Nombre</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Dirección</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Teléfono</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Remitente</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Destinatario</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
        <tr>
            <td>{{ $customer->id }}</td>
            <td>{{ $customer->code }}</td>
            <td>{{ $customer->type_code }}</td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->address }}</td>
            <td>{{ $customer->phone }}</td>
            <td>{{ $customer->encomiendas_remitente_count ?? 0 }}</td>
            <td>{{ $customer->encomiendas_destinatario_count ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
