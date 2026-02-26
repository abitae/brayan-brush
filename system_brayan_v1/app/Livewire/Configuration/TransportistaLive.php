<?php

namespace App\Livewire\Configuration;

use App\Livewire\Forms\TransportistaForm;
use App\Models\Configuration\Transportista;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class TransportistaLive extends Component
{
    use Toast;
    use WithPagination, WithoutUrlPagination;
    public TransportistaForm $transportistaForm;
    public $title = 'CHOFERES';
    public $sub_title = 'Modulo de choferes';
    public int $perPage = 10;
    public bool $modalTransportista = false;
    public function render()
    {
        $transportistas = Transportista::latest()->paginate($this->perPage);
        return view('livewire.configuration.transportista-live', compact('transportistas'));
    }
    public function openModal()
    {
        $this->transportistaForm->reset();
        $this->modalTransportista = !$this->modalTransportista;
    }
    public function create()
    {
        if ($this->transportistaForm->store()) {
            $this->success('Genial, guardado correctamente!');
        } else {
            $this->error('Error, verifique los datos!');
        }
        $this->openModal();
    }
    public function update(Transportista $Transportista)
    {
        $this->openModal();
        $this->transportistaForm->setTransportista($Transportista);
    }
    public function edit()
    {
        if ($this->transportistaForm->update()) {
            $this->success('Genial, guardado correctamente!');
        } else {
            $this->error('Error, verifique los datos!');
        }
        $this->openModal();
    }
    public function delete(Transportista $Transportista)
    {
        if ($this->transportistaForm->delete($Transportista)) {
            $this->success('Genial, guardado correctamente!');
        } else {
            $this->error('Error, verifique los datos!');
        }
    }
    public function estado(Transportista $Transportista)
    {
        if ($this->transportistaForm->estado($Transportista)) {
            $this->success('Genial, guardado correctamente!');
        } else {
            $this->error('Error, verifique los datos!');
        }
    }
}
