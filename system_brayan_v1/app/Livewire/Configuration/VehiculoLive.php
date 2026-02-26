<?php

namespace App\Livewire\Configuration;

use App\Livewire\Forms\VehiculoForm;
use App\Models\Configuration\Vehiculo;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class VehiculoLive extends Component
{
    use Toast;
    use WithPagination, WithoutUrlPagination;
    public VehiculoForm $vehiculoForm;
    public $title = 'Vehiculos';
    public $sub_title = 'Modulo de vehiculos';
    public int $perPage = 10;
    public bool $modalVehiculo = false;
    public function render()
    {
        $vehiculos = Vehiculo::latest()->paginate($this->perPage);
        return view('livewire.configuration.vehiculo-live',compact('vehiculos'));
    }
    public function openModal()
    {
        $this->vehiculoForm->reset();
        $this->modalVehiculo = !$this->modalVehiculo;
    }
    public function create()
    {
        if ($this->vehiculoForm->store()) {
            $this->success('Genial, guardado correctamente!');
        } else {
            $this->error('Error, verifique los datos!');
        }
        $this->openModal();
    }
    public function update(Vehiculo $Vehiculo)
    {
        $this->openModal();
        $this->vehiculoForm->setVehiculo($Vehiculo);
    }
    public function edit()
    {
        if ($this->vehiculoForm->update()) {
            $this->success('Genial, guardado correctamente!');
        } else {
            $this->error('Error, verifique los datos!');
        }
        $this->openModal();
    }
    public function delete(Vehiculo $Vehiculo)
    {
        if ($this->vehiculoForm->delete($Vehiculo)) {
            $this->success('Genial, guardado correctamente!');
        } else {
            $this->error('Error, verifique los datos!');
        }
    }
    public function estado(Vehiculo $Vehiculo)
    {
        if ($this->vehiculoForm->estado($Vehiculo)) {
            $this->success('Genial, guardado correctamente!');
        } else {
            $this->error('Error, verifique los datos!');
        }
    }
}
