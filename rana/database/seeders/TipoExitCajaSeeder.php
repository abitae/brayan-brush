<?php

namespace Database\Seeders;

use App\Models\Configuration\TipoExitCaja;
use Illuminate\Database\Seeder;

class TipoExitCajaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'name' => 'Gasto',
                'is_active' => true,
            ],
            [
                'name' => 'Retiro',
                'is_active' => true,
            ],
            [
                'name' => 'Pago Proveedor',
                'is_active' => true,
            ],
            [
                'name' => 'Gasto Operativo',
                'is_active' => true,
            ],
            [
                'name' => 'Otros Gastos',
                'is_active' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoExitCaja::create($tipo);
        }
    }
}
