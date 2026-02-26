<?php

namespace App\Http\Controllers\Api\frontend;

use App\Http\Controllers\Controller;
use App\Models\Package\Encomienda;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RastreoController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:64',
        ]);

        try {
            $result = Encomienda::where('code', $validated['code'])->first();
            if (!$result) {
                return response()->json(['message' => 'No se encontró la encomienda.'], 404);
            }
            $result = [
                'code' => $result->code,
                'estado_encomienda' => $result->estado_encomienda,
                'name_origen' => $result->sucursal_remitente->name,
                'name_destino' => $result->sucursal_destinatario->name,
                'lugar_origen' => $result->sucursal_remitente->address,
                'lugar_destino' => $result->sucursal_destinatario->address,
                'direccion_envio' => $result->direccion_envio,
                'fecha_creacion' => $result->fecha_creacion,
                'fecha_envio' => $result->fecha_envio,
                'fecha_recepcion' => $result->fecha_recepcion,
                'fecha_entrega' => $result->fecha_entrega,
                'fecha_retorno' => $result->fecha_retorno,
                'isHome' => $result->isHome,
            ];
            return response()->json($result, 200);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Error al consultar el seguimiento. Intenta de nuevo.'], 500);
        }
    }
}