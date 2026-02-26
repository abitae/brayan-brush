<?php

namespace App\Livewire\Caja;

use App\Livewire\Forms\CajaForm;
use App\Livewire\Forms\EntryCajaForm;
use App\Livewire\Forms\ExitCajaForm;
use App\Models\Caja\Caja;
use App\Traits\CajaTrait;
use App\Traits\UtilsTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class CajaLive extends Component
{
    use Toast, CajaTrait, UtilsTrait, WithPagination, WithoutUrlPagination;

    public CajaForm $cajaForm;
    public EntryCajaForm $entryForm;
    public ExitCajaForm $exitForm;
    public string $title = 'CAJA';
    public string $sub_title = 'Modulo de caja';

    public bool $showHistory = false;
    public bool $openCaja = false;
    public bool $modalCaja = false;
    public bool $modalEntry = false;
    public bool $modalExit = false;

    public $fechaActual;
    public $caja;
    public int $perPage = 10;
    public bool $showCajaStats = false;
    public function mount()
    {
        $this->fechaActual = $this->dateNow('Y-m-d');
        $this->caja = $this->cajaIsActive(Auth::user());
        $this->openCaja = (bool) $this->caja;
    }

    public function render()
    {
        return view('livewire.caja.caja-live', [
            'cajas' => $this->cajaListPaginate(Auth::user(), $this->perPage),
            'headersHistory' => $this->getHeadersHistory(),
            'headersIngreso' => $this->getHeadersIngreso(),
            'headersEgreso' => $this->getHeadersEgreso(),
            'tipos' => $this->getTiposIngreso(),
            'tipos2' => $this->getTiposEgreso(),
        ]);
    }

    private function getHeadersIngreso()
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'bg-green-500 w-1'],
            ['key' => 'tipo_entry', 'label' => 'Tipo', 'class' => ''],
            ['key' => 'description', 'label' => 'Descripción', 'class' => ''],
            ['key' => 'metodo_pago', 'label' => 'Metodo Pago', 'class' => ''],
            ['key' => 'monto_entry', 'label' => 'Monto', 'class' => ''],
        ];
    }

    private function getHeadersEgreso()
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'bg-red-500 w-1'],
            ['key' => 'tipo_exit', 'label' => 'Tipo', 'class' => ''],
            ['key' => 'description', 'label' => 'Descripción', 'class' => ''],
            ['key' => 'metodo_pago', 'label' => 'Metodo Pago', 'class' => ''],
            ['key' => 'monto_exit', 'label' => 'Monto', 'class' => ''],
        ];
    }

    private function getHeadersHistory()
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'bg-blue-500 w-1 text-black'],
            ['key' => 'created_at', 'label' => 'Fecha Apertura', 'class' => 'text-black'],
            ['key' => 'updated_at', 'label' => 'Fecha Cierre', 'class' => 'text-black'],
            ['key' => 'monto_apertura', 'label' => 'Apertura', 'class' => 'bg-green-500 text-black'],
            ['key' => 'ingresos', 'label' => 'Ingresos', 'class' => 'bg-blue-500 text-black'],
            ['key' => 'egresos', 'label' => 'Egresos', 'class' => 'bg-purple-500 text-black'],
            ['key' => 'monto_cierre', 'label' => 'Cierre', 'class' => 'bg-red-500 text-black'],
            ['key' => 'action', 'label' => 'Imprimir', 'class' => ''],
        ];
    }

    private function getTiposIngreso()
    {
        return [
            ['id' => 'Devolucion', 'name' => 'Devolución'],
            ['id' => 'Efectivo', 'name' => 'Efectivo'],
            ['id' => 'Ticket', 'name' => 'Ticket'],
        ];
    }

    private function getTiposEgreso()
    {
        return [
            ['id' => 'Devolucion', 'name' => 'Pago'],
            ['id' => 'Efectivo', 'name' => 'Efectivo'],
            ['id' => 'Ticket', 'name' => 'Ticket'],
        ];
    }

    public function openModal()
    {
        $this->cajaForm->reset();
        if ($this->openCaja) {
            $this->cajaForm->monto_cierre = $this->calcularMontoEfectivoActual();
        }
        $this->modalCaja = true;
    }

    public function save()
    {
        if ($this->openCaja) {
            $this->updateCaja();
        } else {
            $this->storeCaja();
        }
    }

    private function updateCaja()
    {
        $montoEfectivoActual = $this->calcularMontoEfectivoActual();

        if ($this->cajaForm->monto_cierre == $montoEfectivoActual) {
            if ($this->cajaForm->update($this->caja)) {
                $this->success('Genial, actualizado correctamente!');
                $this->modalCaja = false;
                $this->openCaja = false;
                return;
            }
            $this->error('Error, verifique los datos!');
        } else {
            $this->error('Error, el monto de cierre no coincide con el saldo actual!');
        }

        $this->modalCaja = false;
    }

    private function calcularMontoEfectivoActual()
    {
        return $this->caja->monto_apertura +
            $this->caja->entries->whereIn('metodo_pago', ['Efectivo'])->sum('monto_entry') -
            $this->caja->exits->whereIn('metodo_pago', ['Efectivo'])->sum('monto_exit');
    }

    private function storeCaja()
    {
        $this->caja = $this->cajaForm->store();

        if ($this->caja) {
            $this->success('Genial, guardado correctamente!');
            $this->modalCaja = false;
            $this->openCaja = true;
            return;
        }

        $this->error('Error, verifique los datos!');
    }

    public function entryCaja()
    {
        if (!$this->openCaja) {
            $this->error('No hay caja abierta para registrar ingresos!');
            return;
        }

        $this->entryForm->caja_id = $this->caja->id;

        if ($this->entryForm->store()) {
            $this->success('Genial, ingresado correctamente!');
            $this->modalEntry = false;
            $this->entryForm->reset();
            return;
        }

        $this->error('Error, verifique los datos!');
        $this->modalEntry = false;
    }

    public function exitCaja()
    {
        if (!$this->openCaja) {
            $this->error('No hay caja abierta para registrar egresos!');
            return;
        }

        $this->exitForm->caja_id = $this->caja->id;

        if ($this->exitForm->store()) {
            $this->success('Genial, ingresado correctamente!');
            $this->modalExit = false;
            $this->exitForm->reset();
            return;
        }

        $this->error('Error, verifique los datos!');
        $this->modalExit = false;
    }

    public function printCaja(Caja $caja)
    {
        return $this->cajaPrint($caja);
    }
}
