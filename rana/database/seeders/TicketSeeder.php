<?php

namespace Database\Seeders;

use App\Models\Configuration\Company;
use App\Models\Facturacion\Ticket;
use App\Models\Package\Customer;
use App\Models\Package\Encomienda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        if (Ticket::query()->exists()) {
            return;
        }

        $company = Company::query()->first();
        $customer = Customer::query()->first();
        $encomienda = Encomienda::query()->first();

        if (!$company || !$customer || !$encomienda) {
            return;
        }

        for ($i = 1; $i <= 3; $i++) {
            $valorVenta = 90 + ($i * 15);
            $igv = round($valorVenta * 0.18, 2);
            $subTotal = $valorVenta;
            $mtoImpVenta = $valorVenta + $igv;

            Ticket::create([
                'encomienda_id' => $encomienda->id,
                'tipoDoc' => '03',
                'tipoOperacion' => '0101',
                'serie' => 'B001',
                'correlativo' => str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'fechaEmision' => Carbon::now()->format('Y-m-d'),
                'formaPago_moneda' => 'PEN',
                'formaPago_tipo' => 'Contado',
                'tipoMoneda' => 'PEN',
                'company_id' => $company->id,
                'client_id' => $customer->id,
                'mtoOperGravadas' => $valorVenta,
                'mtoIGV' => $igv,
                'totalImpuestos' => $igv,
                'valorVenta' => $valorVenta,
                'subTotal' => $subTotal,
                'mtoImpVenta' => $mtoImpVenta,
                'monto_descuento' => 0,
                'motivo_descuento' => null,
            ]);
        }
    }
}
