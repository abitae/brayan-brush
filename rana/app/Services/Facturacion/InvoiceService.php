<?php

namespace App\Services\Facturacion;

use App\Models\Facturacion\Invoice;
use App\Models\Facturacion\InvoiceDetail;
use Illuminate\Support\Facades\DB;
use Exception;

class InvoiceService
{
    /**
     * Crea una factura o boleta con sus detalles.
     *
     * @param array $data Datos principales de la factura (Invoice)
     * @param array $detailsData Array de arrays con los detalles (InvoiceDetail)
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(array $data, array $detailsData): Invoice
    {
        return DB::transaction(function () use ($data, $detailsData) {
            // Crear la factura principal
            $invoice = Invoice::create($data);

            // Crear los detalles
            foreach ($detailsData as $detail) {
                $detail['invoice_id'] = $invoice->id;
                InvoiceDetail::create($detail);
            }

            // Opcional: cargar detalles para retornar con la relación
            $invoice->load('details');

            return $invoice;
        });
    }
}
