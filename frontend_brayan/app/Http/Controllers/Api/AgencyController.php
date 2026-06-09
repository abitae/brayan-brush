<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Agency::listForFront()->values()->all());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:32',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'is_active' => 'nullable|boolean',
        ]);

        $maxOrder = Agency::max('sort_order') ?? -1;
        $validated['sort_order'] = $maxOrder + 1;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $agency = Agency::create($validated);

        return response()->json($agency, 201);
    }

    public function update(Request $request, Agency $agency): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
            'city' => 'sometimes|string|max:100',
            'phone' => 'sometimes|string|max:32',
            'lat' => 'sometimes|numeric',
            'lng' => 'sometimes|numeric',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $agency->update($validated);

        return response()->json($agency);
    }

    public function destroy(Agency $agency): JsonResponse
    {
        $agency->delete();

        return response()->json(['message' => 'Agencia eliminada']);
    }
}
