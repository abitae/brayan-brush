<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Listado de reclamaciones (admin).
     */
    public function index(): JsonResponse
    {
        $complaints = Complaint::orderByDesc('created_at')->get()->map(fn (Complaint $c) => $this->toArray($c));

        return response()->json($complaints);
    }

    /**
     * Registrar reclamación (público).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|string|max:50',
            'telefono' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'direccion' => 'required|string|max:500',
            'tipo' => 'required|string|in:Queja,Reclamo',
            'detalle' => 'required|string|max:5000',
        ]);

        $complaint = Complaint::create([
            ...$validated,
            'status' => Complaint::STATUS_PENDING,
        ]);

        $code = Complaint::generateCode($complaint->id);
        $complaint->update(['code' => $code]);

        return response()->json([
            'id' => $complaint->id,
            'code' => $code,
            'message' => 'Reclamación registrada',
        ], 201);
    }

    /**
     * Actualizar reclamación (admin).
     */
    public function update(Request $request, Complaint $complaint): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|string|in:pendiente,en_proceso,resuelto',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $complaint->update($validated);

        return response()->json($this->toArray($complaint->fresh()));
    }

    public function destroy(Complaint $complaint): JsonResponse
    {
        $complaint->delete();

        return response()->json(['message' => 'Reclamación eliminada']);
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(Complaint $c): array
    {
        return [
            'id' => $c->id,
            'code' => $c->code,
            'nombre' => $c->nombre,
            'documento' => $c->documento,
            'telefono' => $c->telefono,
            'email' => $c->email,
            'direccion' => $c->direccion,
            'tipo' => $c->tipo,
            'detalle' => $c->detalle,
            'status' => $c->status,
            'admin_notes' => $c->admin_notes,
            'created_at' => $c->created_at->toIso8601String(),
        ];
    }
}
