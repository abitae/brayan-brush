<?php

namespace Database\Seeders;

use App\Models\Caja\Caja;
use App\Models\Caja\EntryCaja;
use App\Models\Caja\ExitCaja;
use App\Models\Configuration\TipoEntryCaja;
use App\Models\Configuration\TipoExitCaja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CajaSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $tiposEntry = TipoEntryCaja::all();
        $tiposExit = TipoExitCaja::all();

        if ($users->isEmpty() || $tiposEntry->isEmpty() || $tiposExit->isEmpty()) {
            return;
        }

        $metodosPago = ['Efectivo', 'Yape', 'Tarjeta', 'Transferencia'];

        // Crear cajas para cada usuario activo
        foreach ($users as $user) {
            // Caja abierta actual
            $cajaAbierta = Caja::create([
                'user_id' => $user->id,
                'monto_apertura' => 500.00,
                'monto_cierre' => 0.00, // Caja abierta, monto_cierre es 0 hasta que se cierre
                'isActive' => true,
            ]);

            // Crear algunos ingresos
            for ($i = 0; $i < 5; $i++) {
                EntryCaja::create([
                    'caja_id' => $cajaAbierta->id,
                    'tipo_entry_id' => $tiposEntry->random()->id,
                    'monto_entry' => rand(50, 500) + (rand(0, 99) / 100),
                    'description' => 'Ingreso ' . ($i + 1),
                    'metodo_pago' => $metodosPago[array_rand($metodosPago)],
                    'created_at' => Carbon::now()->subHours(rand(0, 2)),
                ]);
            }

            // Crear algunos egresos
            for ($i = 0; $i < 3; $i++) {
                ExitCaja::create([
                    'caja_id' => $cajaAbierta->id,
                    'tipo_exit_id' => $tiposExit->random()->id,
                    'monto_exit' => rand(20, 200) + (rand(0, 99) / 100),
                    'description' => 'Egreso ' . ($i + 1),
                    'metodo_pago' => $metodosPago[array_rand($metodosPago)],
                    'created_at' => Carbon::now()->subHours(rand(0, 2)),
                ]);
            }

            // Caja cerrada de ayer
            $cajaCerrada = Caja::create([
                'user_id' => $user->id,
                'monto_apertura' => 500.00,
                'monto_cierre' => 1250.50,
                'isActive' => false,
                'created_at' => Carbon::yesterday()->setTime(8, 0),
                'updated_at' => Carbon::yesterday()->setTime(18, 0),
            ]);

            // Crear ingresos para caja cerrada
            for ($i = 0; $i < 10; $i++) {
                EntryCaja::create([
                    'caja_id' => $cajaCerrada->id,
                    'tipo_entry_id' => $tiposEntry->random()->id,
                    'monto_entry' => rand(50, 500) + (rand(0, 99) / 100),
                    'description' => 'Ingreso ' . ($i + 1),
                    'metodo_pago' => $metodosPago[array_rand($metodosPago)],
                    'created_at' => Carbon::yesterday()->setTime(rand(8, 17), rand(0, 59)),
                ]);
            }

            // Crear egresos para caja cerrada
            for ($i = 0; $i < 5; $i++) {
                ExitCaja::create([
                    'caja_id' => $cajaCerrada->id,
                    'tipo_exit_id' => $tiposExit->random()->id,
                    'monto_exit' => rand(20, 200) + (rand(0, 99) / 100),
                    'description' => 'Egreso ' . ($i + 1),
                    'metodo_pago' => $metodosPago[array_rand($metodosPago)],
                    'created_at' => Carbon::yesterday()->setTime(rand(8, 17), rand(0, 59)),
                ]);
            }
        }
    }
}
