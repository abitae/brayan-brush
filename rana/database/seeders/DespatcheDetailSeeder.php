<?php

namespace Database\Seeders;

use App\Models\Facturacion\Despatche;
use App\Models\Facturacion\DespatcheDetail;
use Illuminate\Database\Seeder;

class DespatcheDetailSeeder extends Seeder
{
    public function run(): void
    {
        if (DespatcheDetail::query()->exists()) {
            return;
        }

        $despatches = Despatche::query()->get();
        if ($despatches->isEmpty()) {
            return;
        }

        foreach ($despatches as $despatche) {
            $cantidad = rand(1, 3);
            $valorUnitario = round(rand(10, 50) + (rand(0, 99) / 100), 2);
            $valorVenta = round($cantidad * $valorUnitario, 2);
            $igv = round($valorVenta * 0.18, 2);
            $precioUnitario = round($valorUnitario * 1.18, 2);

            DespatcheDetail::create([
                'despatche_id' => $despatche->id,
                'tipAfeIgv' => '10',
                'codProducto' => 'PROD-001',
                'unidad' => 'NIU',
                'descripcion' => 'Servicio de traslado',
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
