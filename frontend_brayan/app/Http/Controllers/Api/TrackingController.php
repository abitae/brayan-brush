<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\TrackingNotFoundException;
use App\Exceptions\TrackingServerException;
use App\Http\Controllers\Controller;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    public function __construct(
        private readonly TrackingService $tracking
    ) {}

    /**
     * Consultar seguimiento por código y documento (GET, público).
     */
    public function show(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:64',
            'document' => 'required|string|regex:/^\d{8,11}$/',
        ]);

        return $this->resolveTracking($validated['code'], $validated['document']);
    }

    /**
     * Consultar seguimiento por código y documento (POST, sin captcha).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:64',
            'document' => 'required|string|regex:/^\d{8,11}$/',
        ]);

        return $this->resolveTracking($validated['code'], $validated['document']);
    }

    private function resolveTracking(string $code, string $document): JsonResponse
    {
        try {
            $result = $this->tracking->track($code, $document);

            return response()->json($result);
        } catch (TrackingNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (TrackingServerException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        } catch (\Throwable $e) {
            report($e);
            Log::warning('Tracking error', ['message' => $e->getMessage()]);

            return response()->json(['message' => 'Error al consultar el seguimiento. Intenta de nuevo.'], 500);
        }
    }
}
