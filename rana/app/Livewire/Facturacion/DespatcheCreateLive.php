<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;

class DespatcheCreateLive extends Component
{
    public string $title = 'GUÍA DE REMISIÓN TRANSPORTISTA';
    public string $sub_title = 'Crear guía de remisión';

    public function mount($id = null)
    {
        // Opcional: en el futuro se puede cargar una encomienda por $id para generar la guía
    }

    public function render()
    {
        return view('livewire.facturacion.despatche-create-live');
    }
}
