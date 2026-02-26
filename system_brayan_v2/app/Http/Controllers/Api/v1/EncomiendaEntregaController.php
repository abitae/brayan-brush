<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Package\Encomienda;
use App\Services\Package\EncomiendaService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EncomiendaEntregaController extends Controller
{
    public function entregar(Request $request, EncomiendaService $encomiendaService): JsonResponse
    {
        $data = $request->validate([
            'encomienda_id' => 'required|integer|exists:encomiendas,id',
            'pin' => 'nullable|string|max:3',
            'fecha_entrega' => 'nullable|date',
        ], [
            'encomienda_id.required' => 'La encomienda es obligatoria.',
            'encomienda_id.integer' => 'La encomienda seleccionada no es válida.',
            'encomienda_id.exists' => 'La encomienda seleccionada no existe.',
            'pin.max' => 'El PIN no debe exceder 3 caracteres.',
            'fecha_entrega.date' => 'La fecha de entrega no es válida.',
        ]);

        $encomienda = Encomienda::findOrFail($data['encomienda_id']);
        if ($encomienda->isHome) {
            return response()->json([
                'message' => 'Esta encomienda es para entrega a domicilio.',
            ], 422);
        }

        try {
            $fecha = !empty($data['fecha_entrega']) ? Carbon::parse($data['fecha_entrega']) : null;
            $encomiendaService->entregarEncomienda($encomienda->id, $data['pin'] ?? null, $fecha);

            $encomienda->refresh();
            return response()->json([
                'message' => 'Encomienda entregada.',
                'encomienda' => $encomienda,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
