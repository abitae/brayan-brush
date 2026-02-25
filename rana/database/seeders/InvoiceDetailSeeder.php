<?php

namespace Database\Seeders;

use App\Models\Facturacion\Invoice;
use App\Models\Facturacion\InvoiceDetail;
use Illuminate\Database\Seeder;

class InvoiceDetailSeeder extends Seeder
{
    public function run(): void
    {
        if (InvoiceDetail::query()->exists()) {
            return;
        }

        $invoices = Invoice::query()->get();
        if ($invoices->isEmpty()) {
            return;
        }

        foreach ($invoices as $invoice) {
            $cantidad = rand(1, 4);
            $valorUnitario = round(rand(15, 80) + (rand(0, 99) / 100), 2);
            $valorVenta = round($cantidad * $valorUnitario, 2);
            $igv = round($valorVenta * 0.18, 2);
            $precioUnitario = round($valorUnitario * 1.18, 2);

            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'tipAfeIgv' => '10',
                'codProducto' => 'SERV-001',
                'unidad' => 'NIU',
                'descripcion' => 'Servicio de transporte',
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
