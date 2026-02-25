<?php

namespace Database\Seeders;

use App\Models\Configuration\TipoEntryCaja;
use Illuminate\Database\Seeder;

class TipoEntryCajaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'name' => 'Venta',
                'is_active' => true,
            ],
            [
                'name' => 'Abono',
                'is_active' => true,
            ],
            [
                'name' => 'Cobro Encomienda',
                'is_active' => true,
            ],
            [
                'name' => 'Ingreso Extraordinario',
                'is_active' => true,
            ],
            [
                'name' => 'Reembolso',
                'is_active' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoEntryCaja::create($tipo);
        }
    }
}
