<?php

namespace Database\Seeders;

use App\Models\Configuration\Sucursal;
use App\Models\Package\Encomienda;
use App\Models\Package\Manifiesto;
use Illuminate\Database\Seeder;

class ManifiestoSeeder extends Seeder
{
    public function run(): void
    {
        if (Manifiesto::query()->exists()) {
            return;
        }

        $sucursales = Sucursal::all();
        $encomiendas = Encomienda::orderBy('id')->take(10)->pluck('id')->values()->all();

        if ($sucursales->count() < 2 || empty($encomiendas)) {
            return;
        }

        $sucursalOrigen = $sucursales->get(0);
        $sucursalDestino = $sucursales->get(1);

        Manifiesto::create([
            'sucursal_id' => $sucursalOrigen->id,
            'sucursal_destino_id' => $sucursalDestino->id,
            'ids' => json_encode(array_slice($encomiendas, 0, 5)),
        ]);
    }
}
