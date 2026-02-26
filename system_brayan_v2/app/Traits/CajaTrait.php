<?php
namespace App\Traits;

use App\Models\Caja\Caja;
use App\Models\Caja\EntryCaja;
use App\Models\Caja\ExitCaja;
use App\Models\User;
use App\Services\Shared\PdfService;
trait CajaTrait
{
    /*
     *   Función para obtener la caja abierta
     */
    function cajaIsActive(User $user)
    {
        return Caja::where('user_id', $user->id)
            ->where('isActive', true)
            ->latest()->first();
    }
    /*
     *   Función para obtener la caja cerrada
     */
    function cajaListPaginate(User $user, $paginate)
    {
        return Caja::where('user_id', $user->id)
            ->latest()->paginate($paginate);
    }
    /*
     *   Función para agregar entrada
     */
    function cajaEntry(int $caja_id, float $monto, string $description,string $metodo_pago, string $tipo)
    {
        $entry = EntryCaja::create([
            'caja_id' => $caja_id,
            'monto_entry' => $monto,
            'description' => $description,
            'metodo_pago' => $metodo_pago,
            'tipo_entry' => $tipo,
        ]);
        return $entry;
    }

    /*
     *   Función para agregar exit
     */
    function cajaExit(int $caja_id, float $monto, string $description,string $metodo_pago, string $tipo)
    {
        $exit = ExitCaja::create([
            'caja_id' => $caja_id,
            'monto_exit' => $monto,
            'description' => $description,
            'metodo_pago' => $metodo_pago,
            'tipo_exit' => $tipo,
        ]);
        return $exit;
    }
    /*
     *   Función para crear entrada o salida de caja
     */

    /*
     *   Función para imprimir reporte de caja
     */
    public function cajaPrint(Caja $caja)
    {
        $height = 250 + $caja->entries->count() * 8;
        $html = view('pdfs.caja.caja', ['caja' => $caja])->render();
        
        $pdfService = app(PdfService::class);
        return $pdfService->generateCustom($html, [
            'format' => [80, $height],
            'margin_left' => 1,
            'margin_right' => 10,
            'margin_top' => 5,
            'margin_bottom' => 5,
        ], "caja-{$caja->id}.pdf");
    }
}
