<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __construct(
        private readonly TrackingService $tracking
    ) {}

    /**
     * Consultar seguimiento por código (público).
     */
    public function show(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:64',
        ]);

        try {
            $result = $this->tracking->track($validated['code']);
            return response()->json($result);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'message' => 'Error al consultar el seguimiento. Intenta de nuevo.',
                'code' => '',
                'status' => 'registrado',
                'status_label' => 'Registrado',
                'current_location' => null,
                'origin' => '',
                'destination' => '',
                'estimated_delivery' => null,
                'progress' => 0,
                'history' => [],
            ], 500);
        }
    }
}
