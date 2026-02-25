<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\Vehiculo;
use App\Services\Configuration\VehiculoService;
use Livewire\Component;
use Livewire\WithPagination;

class VehiculoLive extends Component
{
    use WithPagination;

    protected VehiculoService $vehiculoService;

    public $showVehiculoModal = false;
    public $showDeleteModal = false;
    public $editingVehiculo = null;
    public $vehiculoToDelete = null;
    public $search = '';
    public $filterStatus = '';

    // Campos del formulario
    public $name = '';
    public $marca = '';
    public $modelo = '';
    public $tipo = '';
    public $color = '';
    public $largo = '';
    public $ancho = '';
    public $alto = '';
    public $pesoBruto = '';
    public $pesoNeto = '';
    public $images = '';
    public $mtc = '';
    public $placa = '';
    public $nroCirculacion = '';
    public $codEmisor = '';
    public $nroAutorizacion = '';
    public $isActive = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'marca' => 'required|string|max:100',
        'modelo' => 'required|string|max:100',
        'tipo' => 'required|string|max:100',
        'color' => 'nullable|string|max:50',
        'largo' => 'nullable|string|max:20',
        'ancho' => 'nullable|string|max:20',
        'alto' => 'nullable|string|max:20',
        'pesoBruto' => 'nullable|string|max:20',
        'pesoNeto' => 'nullable|string|max:20',
        'images' => 'nullable|string|max:255',
        'mtc' => 'nullable|string|max:50',
        'placa' => 'required|string|max:20',
        'nroCirculacion' => 'nullable|string|max:20',
        'codEmisor' => 'nullable|string|max:50',
        'nroAutorizacion' => 'nullable|string|max:50',
        'isActive' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'El nombre del vehículo es obligatorio.',
        'placa.required' => 'La placa es obligatoria.',
        'marca.required' => 'La marca es obligatoria.',
        'modelo.required' => 'El modelo es obligatorio.',
        'tipo.required' => 'El tipo de vehículo es obligatorio.',
    ];

    public function boot(VehiculoService $vehiculoService): void
    {
        $this->vehiculoService = $vehiculoService;
    }

    public function render()
    {
        $vehiculos = $this->vehiculoService->getVehiculos($this->search, $this->filterStatus);
        return view('livewire.configuration.vehiculo-live', [
            'vehiculos' => $vehiculos,
        ]);
    }

    public function openVehiculoModal($vehiculoId = null): void
    {
        if ($vehiculoId) {
            $this->editingVehiculo = Vehiculo::find($vehiculoId);
            foreach ($this->editingVehiculo->getAttributes() as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        } else {
            $this->resetVehiculoForm();
            $this->color = '#000000';
        }
        $this->showVehiculoModal = true;
    }

    public function saveVehiculo(): void
    {
        $data = $this->validate($this->rules);
        try {
            if ($this->editingVehiculo) {
                $this->vehiculoService->actualizarVehiculo($this->editingVehiculo->id, $data);
            } else {
                $this->vehiculoService->crearVehiculo($data);
            }
            $this->closeVehiculoModal();
            session()->flash('message', 'Vehículo guardado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('message', $e->getMessage());
        }
    }

    public function closeVehiculoModal(): void
    {
        $this->showVehiculoModal = false;
        $this->resetVehiculoForm();
        $this->editingVehiculo = null;
        $this->resetValidation();
    }

    public function resetVehiculoForm(): void
    {
        $this->reset([
            'name', 'marca', 'modelo', 'tipo', 'color', 'largo', 'ancho', 'alto', 'pesoBruto', 'pesoNeto',
            'images', 'mtc', 'placa', 'nroCirculacion', 'codEmisor', 'nroAutorizacion', 'isActive'
        ]);
        $this->isActive = true;
        $this->color = '#000000';
    }

    public function confirmDelete($vehiculoId): void
    {
        $this->vehiculoToDelete = $vehiculoId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->vehiculoToDelete = null;
    }

    public function deleteVehiculo(): void
    {
        try {
            $this->vehiculoService->eliminarVehiculo($this->vehiculoToDelete);
            $this->showDeleteModal = false;
            $this->vehiculoToDelete = null;
            session()->flash('message', 'Vehículo eliminado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('message', $e->getMessage());
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
