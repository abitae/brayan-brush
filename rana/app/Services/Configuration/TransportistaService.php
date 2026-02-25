<?php

namespace App\Services\Configuration;

use App\Models\Configuration\Transportista;

class TransportistaService
{
    public function getTransportistas($search = '', $filterStatus = '')
    {
        return Transportista::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                      ->orWhere('dni', 'like', "%$search%")
                      ->orWhere('licencia', 'like', "%$search%")
                      ->orWhere('tipo', 'like', "%$search%") ;
            })
            ->when($filterStatus !== '', function ($query) use ($filterStatus) {
                $query->where('isActive', $filterStatus);
            })
            ->orderBy('name')
            ->paginate(10);
    }

    public function crearTransportista($data)
    {
        return Transportista::create($data);
    }

    public function actualizarTransportista($transportistaId, $data)
    {
        $transportista = Transportista::findOrFail($transportistaId);
        $transportista->update($data);
        return $transportista;
    }

    public function eliminarTransportista($transportistaId)
    {
        $transportista = Transportista::findOrFail($transportistaId);
        $transportista->delete();
        return true;
    }
}
