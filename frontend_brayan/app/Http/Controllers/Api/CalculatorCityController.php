<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalculatorCity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalculatorCityController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(CalculatorCity::listForFront()->values()->all());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:calculator_cities,name',
            'can_origin' => 'nullable|boolean',
            'can_destination' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);

        $city = CalculatorCity::create([
            'name' => trim($validated['name']),
            'can_origin' => $validated['can_origin'] ?? true,
            'can_destination' => $validated['can_destination'] ?? true,
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json($this->formatAdmin($city), 201);
    }

    public function update(Request $request, CalculatorCity $calculatorCity): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100|unique:calculator_cities,name,'.$calculatorCity->id,
            'can_origin' => 'nullable|boolean',
            'can_destination' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);

        if (isset($validated['name'])) {
            $validated['name'] = trim($validated['name']);
        }

        $calculatorCity->update($validated);
        $calculatorCity->refresh();

        return response()->json($this->formatAdmin($calculatorCity));
    }

    public function destroy(CalculatorCity $calculatorCity): JsonResponse
    {
        $calculatorCity->delete();

        return response()->json(['message' => 'Ciudad eliminada']);
    }

    private function formatAdmin(CalculatorCity $city): array
    {
        return [
            'id' => $city->id,
            'name' => $city->name,
            'can_origin' => (bool) $city->can_origin,
            'can_destination' => (bool) $city->can_destination,
            'is_active' => (bool) $city->is_active,
            'sort_order' => (int) $city->sort_order,
        ];
    }
}
