<?php

namespace Database\Seeders;

use App\Models\Configuration\Company;
use App\Models\Facturacion\Despatche;
use App\Models\Package\Customer;
use App\Models\Package\Encomienda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DespatcheSeeder extends Seeder
{
    public function run(): void
    {
        if (Despatche::query()->exists()) {
            return;
        }

        $company = Company::query()->first();
        $customers = Customer::query()->get();
        $encomiendas = Encomienda::query()->take(5)->get();

        if (!$company || $customers->isEmpty() || $encomiendas->isEmpty()) {
            return;
        }

        foreach ($encomiendas as $index => $encomienda) {
            $flete = $customers->random();
            $remitente = $customers->random();
            $destinatario = $customers->random();

            $valorVenta = 100 + ($index * 20);
            $igv = round($valorVenta * 0.18, 2);
            $mtoImpVenta = $valorVenta + $igv;

            Despatche::create([
                'encomienda_id' => $encomienda->id,
                'tipoDoc' => '31',
                'serie' => 'T001',
                'correlativo' => str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                'fechaEmision' => Carbon::now()->format('Y-m-d'),
                'company_id' => $company->id,
                'flete_id' => $flete->id,
                'remitente_id' => $remitente->id,
                'destinatario_id' => $destinatario->id,
                'codTraslado' => '01',
                'modTraslado' => '01',
                'docsTraslado' => json_encode([]),
                'fecTraslado' => Carbon::now()->addDay()->format('Y-m-d'),
                'pesoTotal' => '10',
                'undPesoTotal' => 'KG',
                'llegada_ubigueo' => '150101',
                'llegada_direccion' => 'Av. Destino 123',
                'partida_ubigueo' => '150101',
                'partida_direccion' => 'Av. Origen 456',
                'chofer_tipoDoc' => '1',
                'chofer_nroDoc' => '12345678',
                'chofer_licencia' => 'Q1234567',
                'chofer_nombres' => 'Pedro',
                'chofer_apellidos' => 'Gomez',
                'vehiculo_placa' => 'ABC-123',
                'mtoIGV' => $igv,
                'valorVenta' => $valorVenta,
                'mtoImpVenta' => $mtoImpVenta,
                'monto_letras' => 'CIENTO VEINTE Y 00/100',
            ]);
        }
    }
}
