<?php

namespace App\Services\Facturacion;

use App\Models\Facturacion\Ticket;
use App\Models\Facturacion\TicketDetail;
use Illuminate\Support\Facades\DB;
use Exception;

class TicketService
{
    /**
     * Crea un ticket con sus detalles.
     *
     * @param array $data Datos principales del ticket (Ticket)
     * @param array $detailsData Array de arrays con los detalles (TicketDetail)
     * @return Ticket
     * @throws Exception
     */
    public function createTicket(array $data, array $detailsData): Ticket
    {
        return DB::transaction(function () use ($data, $detailsData) {
            // Crear el ticket principal
            $ticket = Ticket::create($data);

            // Crear los detalles
            foreach ($detailsData as $detail) {
                $detail['ticket_id'] = $ticket->id;
                TicketDetail::create($detail);
            }

            // Opcional: cargar detalles para retornar con la relación
            $ticket->load('details');

            return $ticket;
        });
    }
}
