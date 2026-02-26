<?php

namespace Database\Seeders;

use App\Models\Package\Encomienda;
use App\Models\Package\Paquete;
use Illuminate\Database\Seeder;

class PaqueteSeeder extends Seeder
{
    public function run(): void
    {
        if (Paquete::query()->exists()) {
            return;
        }

        $encomiendas = Encomienda::orderBy('id')->take(10)->get();
        if ($encomiendas->isEmpty()) {
            return;
        }

        $unidadesMedida = ['NIU', 'KG', 'M2', 'M3'];
        $descripciones = [
            'Documentos',
            'Ropa',
            'Electrodomesticos',
            'Productos fragiles',
            'Mercancia general',
        ];

        foreach ($encomiendas as $encomienda) {
            $cantidad = rand(1, 3);
            $amount = round(rand(20, 120) + (rand(0, 99) / 100), 2);
            $subTotal = round($cantidad * $amount, 2);

            Paquete::create([
                'encomienda_id' => $encomienda->id,
                'cantidad' => $cantidad,
                'und_medida' => $unidadesMedida[array_rand($unidadesMedida)],
                'description' => $descripciones[array_rand($descripciones)],
                'peso' => (string) round(rand(1, 30) + (rand(0, 99) / 100), 2),
                'amount' => $amount,
                'sub_total' => $subTotal,
            ]);
        }
    }
}
