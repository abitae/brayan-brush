<?php

namespace App\Services\Configuration;

use App\Models\Configuration\Sucursal;
use Illuminate\Support\Facades\DB;

class SucursalService
{
    /**
     * Obtener todas las sucursales con búsqueda y paginación
     */
    public function getSucursales($search = '', $filterStatus = '')
    {
        return Sucursal::when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('code', 'like', "%$search%")
                      ->orWhere('address', 'like', "%$search%")
                      ->orWhere('phone', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('departamento', 'like', "%$search%")
                      ->orWhere('provincia', 'like', "%$search%")
                      ->orWhere('distrito', 'like', "%$search%");
                });
            })
            ->when($filterStatus !== '', function ($query) use ($filterStatus) {
                $query->where('isActive', $filterStatus);
            })
            ->orderBy('name')
            ->paginate(10);
    }

    /**
     * Crear una nueva sucursal
     */
    public function crearSucursal($data)
    {
        return Sucursal::create($data);
    }

    /**
     * Actualizar una sucursal existente
     */
    public function actualizarSucursal($sucursalId, $data)
    {
        $sucursal = Sucursal::findOrFail($sucursalId);
        $sucursal->update($data);
        return $sucursal;
    }

    /**
     * Eliminar una sucursal
     */
    public function eliminarSucursal($sucursalId)
    {
        $sucursal = Sucursal::findOrFail($sucursalId);

        // Verificar si la sucursal tiene relaciones antes de eliminar
        if ($sucursal->users()->count() > 0) {
            throw new \Exception('No se puede eliminar la sucursal porque tiene usuarios asociados.');
        }

        if ($sucursal->invoices()->count() > 0) {
            throw new \Exception('No se puede eliminar la sucursal porque tiene facturas asociadas.');
        }

        if ($sucursal->encomiendas_remitente()->count() > 0 || $sucursal->encomiendas_destinatario()->count() > 0) {
            throw new \Exception('No se puede eliminar la sucursal porque tiene encomiendas asociadas.');
        }

        $sucursal->delete();
        return true;
    }

    /**
     * Obtener estadísticas de sucursales
     */
    public function getEstadisticas()
    {
        $total = Sucursal::count();
        $activas = Sucursal::where('isActive', true)->count();
        $inactivas = Sucursal::where('isActive', false)->count();
        $departamentos = Sucursal::whereNotNull('departamento')->distinct('departamento')->count();

        return [
            'total' => $total,
            'activas' => $activas,
            'inactivas' => $inactivas,
            'departamentos' => $departamentos,
        ];
    }

    /**
     * Obtener sucursales activas para selects
     */
    public function getSucursalesActivas()
    {
        return Sucursal::where('isActive', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}
