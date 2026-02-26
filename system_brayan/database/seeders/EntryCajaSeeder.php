<?php

namespace Database\Seeders;

use App\Models\Caja\Caja;
use App\Models\Caja\EntryCaja;
use App\Models\Configuration\TipoEntryCaja;
use Illuminate\Database\Seeder;

class EntryCajaSeeder extends Seeder
{
    public function run(): void
    {
        if (EntryCaja::query()->exists()) {
            return;
        }

        $caja = Caja::query()->first();
        $tipo = TipoEntryCaja::query()->first();

        if (!$caja || !$tipo) {
            return;
        }

        $metodosPago = ['Efectivo', 'Yape', 'Tarjeta', 'Transferencia'];

        for ($i = 0; $i < 5; $i++) {
            EntryCaja::create([
                'caja_id' => $caja->id,
                'tipo_entry_id' => $tipo->id,
                'monto_entry' => rand(50, 300) + (rand(0, 99) / 100),
                'description' => 'Ingreso adicional ' . ($i + 1),
                'metodo_pago' => $metodosPago[array_rand($metodosPago)],
            ]);
        }
    }
}
