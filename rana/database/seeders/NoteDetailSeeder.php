<?php

namespace Database\Seeders;

use App\Models\Facturacion\Note;
use App\Models\Facturacion\NoteDetail;
use Illuminate\Database\Seeder;

class NoteDetailSeeder extends Seeder
{
    public function run(): void
    {
        if (NoteDetail::query()->exists()) {
            return;
        }

        $notes = Note::query()->get();
        if ($notes->isEmpty()) {
            return;
        }

        foreach ($notes as $note) {
            $cantidad = rand(1, 3);
            $valorUnitario = round(rand(10, 50) + (rand(0, 99) / 100), 2);
            $valorVenta = round($cantidad * $valorUnitario, 2);
            $igv = round($valorVenta * 0.18, 2);
            $precioUnitario = round($valorUnitario * 1.18, 2);

            NoteDetail::create([
                'note_id' => $note->id,
                'tipAfeIgv' => '10',
                'codProducto' => 'SERV-003',
                'unidad' => 'NIU',
                'descripcion' => 'Ajuste por devolucion',
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
