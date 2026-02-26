<?php

namespace App\Services\Configuration;

use App\Models\Configuration\Vehiculo;

class VehiculoService
{
    public function getVehiculos($search = '', $filterStatus = '')
    {
        return Vehiculo::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                      ->orWhere('placa', 'like', "%$search%")
                      ->orWhere('marca', 'like', "%$search%")
                      ->orWhere('modelo', 'like', "%$search%") ;
            })
            ->when($filterStatus !== '', function ($query) use ($filterStatus) {
                $query->where('isActive', $filterStatus);
            })
            ->orderBy('name')
            ->paginate(10);
    }

    public function crearVehiculo($data)
    {
        return Vehiculo::create($data);
    }

    public function actualizarVehiculo($vehiculoId, $data)
    {
        $vehiculo = Vehiculo::findOrFail($vehiculoId);
        $vehiculo->update($data);
        return $vehiculo;
    }

    public function eliminarVehiculo($vehiculoId)
    {
        $vehiculo = Vehiculo::findOrFail($vehiculoId);
        $vehiculo->delete();
        return true;
    }
}
