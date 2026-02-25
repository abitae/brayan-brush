<?php

namespace Database\Seeders;

use App\Models\Configuration\Company;
use App\Models\Configuration\Sucursal;
use App\Models\Facturacion\Note;
use App\Models\Package\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        if (Note::query()->exists()) {
            return;
        }

        $company = Company::query()->first();
        $customer = Customer::query()->first();
        $sucursal = Sucursal::query()->first();

        if (!$company || !$customer) {
            return;
        }

        for ($i = 1; $i <= 2; $i++) {
            $valorVenta = 80 + ($i * 20);
            $igv = round($valorVenta * 0.18, 2);
            $mtoImpVenta = $valorVenta + $igv;

            Note::create([
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'sucursal_id' => $sucursal?->id,
                'ublVersion' => '2.1',
                'tipoDoc' => '07',
                'serie' => 'FC01',
                'correlativo' => str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'fechaEmision' => Carbon::now()->toDateString(),
                'tipoDocAfectado' => '01',
                'numDocfectado' => 'F001-000001',
                'codMotivo' => '01',
                'desMotivo' => 'Anulacion de la operacion',
                'tipoMoneda' => 'PEN',
                'mtoOperGravadas' => $valorVenta,
                'mtoIGV' => $igv,
                'totalImpuestos' => $igv,
                'mtoImpVenta' => $mtoImpVenta,
                'monto_letras' => 'OCHENTA Y 00/100',
                'legends' => json_encode([['code' => '1000', 'value' => 'MONTO EN LETRAS']]),
            ]);
        }
    }
}
