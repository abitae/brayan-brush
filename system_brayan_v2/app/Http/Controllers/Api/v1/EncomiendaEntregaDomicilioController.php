<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Package\Encomienda;
use App\Services\Package\EncomiendaService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EncomiendaEntregaDomicilioController extends Controller
{
    public function entregar(Request $request, EncomiendaService $encomiendaService): JsonResponse
    {
        $data = $request->validate([
            'encomienda_id' => 'required|integer|exists:encomiendas,id',
            'fecha_entrega' => 'nullable|date',
        ], [
            'encomienda_id.required' => 'La encomienda es obligatoria.',
            'encomienda_id.integer' => 'La encomienda seleccionada no es válida.',
            'encomienda_id.exists' => 'La encomienda seleccionada no existe.',
            'fecha_entrega.date' => 'La fecha de entrega no es válida.',
        ]);

        $encomienda = Encomienda::findOrFail($data['encomienda_id']);
        if (!$encomienda->isHome) {
            return response()->json([
                'message' => 'Esta encomienda no es para entrega a domicilio.',
            ], 422);
        }

        try {
            $fecha = !empty($data['fecha_entrega']) ? Carbon::parse($data['fecha_entrega']) : null;
            $encomiendaService->entregarEncomienda($encomienda->id, null, $fecha);

            $encomienda->refresh();
            return response()->json([
                'message' => 'Encomienda entregada a domicilio.',
                'encomienda' => $encomienda,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
