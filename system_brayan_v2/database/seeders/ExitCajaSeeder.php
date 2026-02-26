<?php

namespace Database\Seeders;

use App\Models\Caja\Caja;
use App\Models\Caja\ExitCaja;
use App\Models\Configuration\TipoExitCaja;
use Illuminate\Database\Seeder;

class ExitCajaSeeder extends Seeder
{
    public function run(): void
    {
        if (ExitCaja::query()->exists()) {
            return;
        }

        $caja = Caja::query()->first();
        $tipo = TipoExitCaja::query()->first();

        if (!$caja || !$tipo) {
            return;
        }

        $metodosPago = ['Efectivo', 'Yape', 'Tarjeta', 'Transferencia'];

        for ($i = 0; $i < 3; $i++) {
            ExitCaja::create([
                'caja_id' => $caja->id,
                'tipo_exit_id' => $tipo->id,
                'monto_exit' => rand(20, 150) + (rand(0, 99) / 100),
                'description' => 'Egreso adicional ' . ($i + 1),
                'metodo_pago' => $metodosPago[array_rand($metodosPago)],
            ]);
        }
    }
}
