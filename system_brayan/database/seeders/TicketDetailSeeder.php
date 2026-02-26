<?php

namespace Database\Seeders;

use App\Models\Facturacion\Ticket;
use App\Models\Facturacion\TicketDetail;
use Illuminate\Database\Seeder;

class TicketDetailSeeder extends Seeder
{
    public function run(): void
    {
        if (TicketDetail::query()->exists()) {
            return;
        }

        $tickets = Ticket::query()->get();
        if ($tickets->isEmpty()) {
            return;
        }

        foreach ($tickets as $ticket) {
            $cantidad = rand(1, 3);
            $valorUnitario = round(rand(10, 60) + (rand(0, 99) / 100), 2);
            $valorVenta = round($cantidad * $valorUnitario, 2);
            $igv = round($valorVenta * 0.18, 2);
            $precioUnitario = round($valorUnitario * 1.18, 2);

            TicketDetail::create([
                'ticket_id' => $ticket->id,
                'tipAfeIgv' => '10',
                'codProducto' => 'SERV-002',
                'unidad' => 'NIU',
                'descripcion' => 'Servicio de encomienda',
                'cantidad' => $cantidad,
                'mtoValorUnitario' => $valorUnitario,
                'mtoValorVenta' => $valorVenta,
                'mtoBaseIgv' => $valorVenta,
                'porcentajeIgv' => 18,
                'igv' => $igv,
                'totalImpuestos' => $igv,
                'mtoPrecioUnitario' => $precioUnitario,
            ]);
        }
    }
}
