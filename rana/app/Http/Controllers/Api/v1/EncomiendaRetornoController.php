<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Package\Encomienda;
use App\Services\Package\EncomiendaService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EncomiendaRetornoController extends Controller
{
    public function retornar(Request $request, EncomiendaService $encomiendaService): JsonResponse
    {
        $data = $request->validate([
            'encomienda_id' => 'required|integer|exists:encomiendas,id',
            'motivo' => 'required|string|max:255',
            'fecha_retorno' => 'nullable|date',
        ], [
            'encomienda_id.required' => 'La encomienda es obligatoria.',
            'encomienda_id.integer' => 'La encomienda seleccionada no es válida.',
            'encomienda_id.exists' => 'La encomienda seleccionada no existe.',
            'motivo.required' => 'El motivo del retorno es obligatorio.',
            'motivo.max' => 'El motivo no debe exceder 255 caracteres.',
            'fecha_retorno.date' => 'La fecha de retorno no es válida.',
        ]);

        $encomienda = Encomienda::findOrFail($data['encomienda_id']);
        if (!$encomienda->isReturn) {
            return response()->json([
                'message' => 'Esta encomienda no está marcada como retorno.',
            ], 422);
        }

        try {
            $fecha = !empty($data['fecha_retorno']) ? Carbon::parse($data['fecha_retorno']) : null;
            $encomiendaService->retornarEncomienda($encomienda->id, $data['motivo'], $fecha);

            $encomienda->refresh();
            return response()->json([
                'message' => 'Encomienda retornada.',
                'encomienda' => $encomienda,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
