<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\Transportista;
use App\Services\Configuration\TransportistaService;
use Livewire\Component;
use Livewire\WithPagination;

class TransportistaLive extends Component
{
    use WithPagination;

    protected TransportistaService $transportistaService;

    public $showTransportistaModal = false;
    public $showDeleteModal = false;
    public $editingTransportista = null;
    public $transportistaToDelete = null;
    public $search = '';
    public $filterStatus = '';

    // Campos del formulario
    public $type_code = '';
    public $licencia = '';
    public $dni = '';
    public $name = '';
    public $tipo = '';
    public $isActive = true;

    protected $rules = [
        'type_code' => 'nullable|string|max:20',
        'licencia' => 'required|string|max:20',
        'dni' => 'required|string|max:20',
        'name' => 'required|string|max:255',
        'tipo' => 'nullable|string|max:50',
        'isActive' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'dni.required' => 'El DNI es obligatorio.',
        'licencia.required' => 'La licencia es obligatoria.',
    ];

    public function boot(TransportistaService $transportistaService): void
    {
        $this->transportistaService = $transportistaService;
    }

    public function render()
    {
        $transportistas = $this->transportistaService->getTransportistas($this->search, $this->filterStatus);
        return view('livewire.configuration.transportista-live', [
            'transportistas' => $transportistas,
        ]);
    }

    public function openTransportistaModal($transportistaId = null): void
    {
        if ($transportistaId) {
            $this->editingTransportista = Transportista::find($transportistaId);
            foreach ($this->editingTransportista->getAttributes() as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        } else {
            $this->resetTransportistaForm();
        }
        $this->showTransportistaModal = true;
    }

    public function saveTransportista(): void
    {
        $data = $this->validate($this->rules);
        try {
            if ($this->editingTransportista) {
                $this->transportistaService->actualizarTransportista($this->editingTransportista->id, $data);
            } else {
                $this->transportistaService->crearTransportista($data);
            }
            $this->closeTransportistaModal();
            session()->flash('message', 'Transportista guardado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('message', $e->getMessage());
        }
    }

    public function closeTransportistaModal(): void
    {
        $this->showTransportistaModal = false;
        $this->resetTransportistaForm();
        $this->editingTransportista = null;
        $this->resetValidation();
    }

    public function resetTransportistaForm(): void
    {
        $this->reset([
            'type_code', 'licencia', 'dni', 'name', 'tipo', 'isActive'
        ]);
        $this->isActive = true;
    }

    public function confirmDelete($transportistaId): void
    {
        $this->transportistaToDelete = $transportistaId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->transportistaToDelete = null;
    }

    public function deleteTransportista(): void
    {
        try {
            $this->transportistaService->eliminarTransportista($this->transportistaToDelete);
            $this->showDeleteModal = false;
            $this->transportistaToDelete = null;
            session()->flash('message', 'Transportista eliminado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('message', $e->getMessage());
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
