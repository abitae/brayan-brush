<?php

namespace App\Services\Package;

use App\Models\Package\RutaSucursal;
use App\Models\Configuration\Sucursal;
use App\Models\Configuration\Transportista;
use App\Models\Configuration\Vehiculo;
use Illuminate\Support\Facades\DB;

class RutaSucursalService
{
    /**
     * Obtener todas las rutas con búsqueda y paginación
     */
    public function getRutas($search = '', $filters = [], $perPage = 10)
    {
        return RutaSucursal::with([
            'sucursalOrigen',
            'sucursalDestino',
            'transportista',
            'vehiculo'
        ])
        ->withCount('encomiendas')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('sucursalOrigen', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhereHas('sucursalDestino', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhereHas('transportista', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhereHas('vehiculo', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhere('dia_semana', 'like', "%$search%");
            });
        })
        ->when(isset($filters['estado_ruta']), function ($query) use ($filters) {
            $query->where('estado_ruta', $filters['estado_ruta']);
        })

        ->when(isset($filters['sucursal_origen_id']), function ($query) use ($filters) {
            $query->where('sucursal_origen_id', $filters['sucursal_origen_id']);
        })
        ->when(isset($filters['sucursal_destino_id']), function ($query) use ($filters) {
            $query->where('sucursal_destino_id', $filters['sucursal_destino_id']);
        })
        ->when(isset($filters['vehiculo_id']), function ($query) use ($filters) {
            $query->where('vehiculo_id', $filters['vehiculo_id']);
        })
        ->when(isset($filters['fecha_inicio']), function ($query) use ($filters) {
            $query->where('fecha_salida', '>=', $filters['fecha_inicio']);
        })
        ->when(isset($filters['fecha_fin']), function ($query) use ($filters) {
            $query->where('fecha_salida', '<=', $filters['fecha_fin']);
        })
        ->when(isset($filters['isActive']), function ($query) use ($filters) {
            $query->where('isActive', $filters['isActive']);
        })
        ->orderBy('dia_semana', 'desc')
        ->orderBy('hora_salida', 'desc')
        ->paginate($perPage);
    }

    /**
     * Obtener todas las sucursales
     */
    public function getSucursales()
    {
        return Sucursal::where('isActive', true)->orderBy('name')->get();
    }

    /**
     * Obtener todos los transportistas
     */
    public function getTransportistas()
    {
        return Transportista::where('isActive', true)->orderBy('name')->get();
    }

    /**
     * Obtener todos los vehículos
     */
    public function getVehiculos()
    {
        return Vehiculo::where('isActive', true)->orderBy('name')->get();
    }

    /**
     * Crear una nueva ruta
     */
    public function crearRuta($data)
    {
        // Generar código automáticamente si no se proporciona
        if (!isset($data['code']) || empty($data['code'])) {
            $data['code'] = $this->generateUniqueCode();
        }

        return RutaSucursal::create($data);
    }

    /**
     * Genera un código único para la ruta
     * Formato: RUTA-{correlativo}
     * 
     * @return string Código único generado
     */
    private function generateUniqueCode()
    {
        do {
            $correlativo = RutaSucursal::count() + 1;
            $code = 'RUTA-' . str_pad($correlativo, 6, '0', STR_PAD_LEFT);
        } while (RutaSucursal::where('code', $code)->exists());

        return $code;
    }

    /**
     * Actualizar una ruta existente
     */
    public function actualizarRuta($rutaId, $data)
    {
        $ruta = RutaSucursal::findOrFail($rutaId);
        $ruta->update($data);
        return $ruta;
    }

    /**
     * Eliminar una ruta
     */
    public function eliminarRuta($rutaId)
    {
        $ruta = RutaSucursal::findOrFail($rutaId);
        return $ruta->delete();
    }

    /**
     * Obtener una ruta por ID
     */
    public function getRutaById($rutaId)
    {
        return RutaSucursal::with([
            'sucursalOrigen',
            'sucursalDestino',
            'transportista',
            'vehiculo'
        ])->findOrFail($rutaId);
    }

    /**
     * Obtener rutas activas por sucursal origen
     */
    public function getRutasActivasPorSucursalOrigen($sucursalId)
    {
        return RutaSucursal::with(['sucursalDestino', 'transportista', 'vehiculo'])
            ->where('sucursal_origen_id', $sucursalId)
            ->where('estado_ruta', 'ACTIVA')
            ->where('isActive', true)
            ->orderBy('dia_semana')
            ->orderBy('hora_salida')
            ->get();
    }

    /**
     * Obtener rutas activas por sucursal destino
     */
    public function getRutasActivasPorSucursalDestino($sucursalId)
    {
        return RutaSucursal::with(['sucursalOrigen', 'transportista', 'vehiculo'])
            ->where('sucursal_destino_id', $sucursalId)
            ->where('estado_ruta', 'ACTIVA')
            ->where('isActive', true)
            ->orderBy('dia_semana')
            ->orderBy('hora_salida')
            ->get();
    }

    /**
     * Verificar si existe una ruta entre dos sucursales
     */
    public function existeRuta($sucursalOrigenId, $sucursalDestinoId, $excludeId = null)
    {
        $query = RutaSucursal::where('sucursal_origen_id', $sucursalOrigenId)
            ->where('sucursal_destino_id', $sucursalDestinoId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Obtener estadísticas de rutas
     */
    public function getEstadisticas()
    {
        return [
            'total' => RutaSucursal::count(),
            'activas' => RutaSucursal::where('estado_ruta', 'ACTIVA')->where('isActive', true)->count(),
            'inactivas' => RutaSucursal::where('estado_ruta', 'INACTIVA')->count(),
            'suspendidas' => RutaSucursal::where('estado_ruta', 'SUSPENDIDA')->count(),
            'completadas' => RutaSucursal::where('estado_ruta', 'COMPLETADO')->count(),
            'diarias' => RutaSucursal::where('estado_ruta', 'ACTIVA')->where('isActive', true)->count(),
        ];
    }

    /**
     * Obtener estadísticas de rutas por sucursal
     */
    public function getEstadisticasPorSucursal($sucursalId)
    {
        return [
            'total' => RutaSucursal::where('sucursal_origen_id', $sucursalId)->count(),
            'activas' => RutaSucursal::where('sucursal_origen_id', $sucursalId)
                ->where('estado_ruta', 'ACTIVA')->where('isActive', true)->count(),
            'inactivas' => RutaSucursal::where('sucursal_origen_id', $sucursalId)
                ->where('estado_ruta', 'INACTIVA')->count(),
            'suspendidas' => RutaSucursal::where('sucursal_origen_id', $sucursalId)
                ->where('estado_ruta', 'SUSPENDIDA')->count(),
            'completadas' => RutaSucursal::where('sucursal_origen_id', $sucursalId)
                ->where('estado_ruta', 'COMPLETADO')->count(),
            'diarias' => RutaSucursal::where('sucursal_origen_id', $sucursalId)
                ->where('estado_ruta', 'ACTIVA')->where('isActive', true)->count(),
        ];
    }
}
