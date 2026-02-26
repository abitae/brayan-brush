<?php

namespace Database\Seeders;

use App\Models\Configuration\Company;
use App\Models\Configuration\Sucursal;
use App\Models\Facturacion\Invoice;
use App\Models\Package\Customer;
use App\Models\Package\Encomienda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        if (Invoice::query()->exists()) {
            return;
        }

        $company = Company::query()->first();
        $customer = Customer::query()->first();
        $sucursal = Sucursal::query()->first();
        $encomienda = Encomienda::query()->first();

        if (!$company || !$customer) {
            return;
        }

        for ($i = 1; $i <= 3; $i++) {
            $valorVenta = 150 + ($i * 25);
            $igv = round($valorVenta * 0.18, 2);
            $subTotal = $valorVenta;
            $mtoImpVenta = $valorVenta + $igv;

            Invoice::create([
                'encomienda_id' => $encomienda?->id,
                'sucursal_id' => $sucursal?->id,
                'company_id' => $company->id,
                'client_id' => $customer->id,
                'tipoDoc' => '01',
                'tipoOperacion' => '0101',
                'serie' => 'F001',
                'correlativo' => str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'fechaEmision' => Carbon::now()->format('Y-m-d'),
                'formaPago_moneda' => 'PEN',
                'formaPago_tipo' => 'Contado',
                'tipoMoneda' => 'PEN',
                'mtoOperGravadas' => $valorVenta,
                'mtoIGV' => $igv,
                'totalImpuestos' => $igv,
                'valorVenta' => $valorVenta,
                'subTotal' => $subTotal,
                'mtoImpVenta' => $mtoImpVenta,
                'monto_letras' => 'CIENTO CINCUENTA Y 00/100',
                'legends' => json_encode([['code' => '1000', 'value' => 'MONTO EN LETRAS']]),
            ]);
        }
    }
}
