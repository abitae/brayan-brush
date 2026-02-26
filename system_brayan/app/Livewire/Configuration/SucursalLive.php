<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\Sucursal;
use App\Services\Configuration\SucursalService;
use Livewire\WithPagination;
use Livewire\Component;

class SucursalLive extends Component
{
    use WithPagination;

    protected SucursalService $sucursalService;

    public $showSucursalModal = false;
    public $showDeleteModal = false;
    public $editingSucursal = null;
    public $sucursalToDelete = null;
    public $search = '';
    public $filterStatus = '';

    // Campos del formulario
    public $name = '';
    public $code = '';
    public $codeSunat = '';
    public $igv = 18.00;
    public $serieFactura = '';
    public $serieBoleta = '';
    public $serieGuiaRemision = '';
    public $serieNotaCreditoFactura = '';
    public $serieNotaCreditoBoleta = '';
    public $serieNotaDebitoFactura = '';
    public $serieNotaDebitoBoleta = '';
    public $color = '#3B82F6';
    public $address = '';
    public $phone = '';
    public $email = '';
    public $departamento = '';
    public $provincia = '';
    public $distrito = '';
    public $urbanizacion = '';
    public $ubigeo = '';
    public $isActive = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255|unique:sucursals,code',
        'codeSunat' => 'required|string|max:255',
        'igv' => 'required|numeric|min:0|max:100',
        'serieFactura' => 'required|string|max:255',
        'serieBoleta' => 'required|string|max:255',
        'serieGuiaRemision' => 'required|string|max:255',
        'serieNotaCreditoFactura' => 'required|string|max:255',
        'serieNotaCreditoBoleta' => 'required|string|max:255',
        'serieNotaDebitoFactura' => 'required|string|max:255',
        'serieNotaDebitoBoleta' => 'required|string|max:255',
        'color' => 'nullable|string|max:7',
        'address' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'departamento' => 'required|string|max:255',
        'provincia' => 'required|string|max:255',
        'distrito' => 'required|string|max:255',
        'urbanizacion' => 'required|string|max:255',
        'ubigeo' => 'required|string|max:255',
        'isActive' => 'boolean',
    ];
    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'code.required' => 'El código es obligatorio.',
        'code.unique' => 'El código ya existe.',
        'codeSunat.required' => 'El código SUNAT es obligatorio.',
        'igv.required' => 'El IGV es obligatorio.',
        'igv.numeric' => 'El IGV debe ser un número.',
        'igv.min' => 'El IGV no puede ser negativo.',
        'igv.max' => 'El IGV no puede ser mayor a 100%.',
        'serieFactura.required' => 'La serie de factura es obligatoria.',
        'serieBoleta.required' => 'La serie de boleta es obligatoria.',
        'serieGuiaRemision.required' => 'La serie de guía de remisión es obligatoria.',
        'serieNotaCreditoFactura.required' => 'La serie de nota crédito factura es obligatoria.',
        'serieNotaCreditoBoleta.required' => 'La serie de nota crédito boleta es obligatoria.',
        'serieNotaDebitoFactura.required' => 'La serie de nota débito factura es obligatoria.',
        'serieNotaDebitoBoleta.required' => 'La serie de nota débito boleta es obligatoria.',
        'address.required' => 'La dirección es obligatoria.',
        'departamento.required' => 'El departamento es obligatorio.',
        'provincia.required' => 'La provincia es obligatoria.',
        'distrito.required' => 'El distrito es obligatorio.',
        'urbanizacion.required' => 'La urbanización es obligatoria.',
        'ubigeo.required' => 'El código UBIGEO es obligatorio.',
        'email.email' => 'El correo electrónico no es válido.',
    ];

    public function boot(SucursalService $sucursalService): void
    {
        $this->sucursalService = $sucursalService;
    }

    public function render()
    {
        $sucursales = $this->sucursalService->getSucursales($this->search, $this->filterStatus);
        return view('livewire.configuration.sucursal-live', [
            'sucursales' => $sucursales,
        ]);
    }

    public function openSucursalModal($sucursalId = null): void
    {
        if ($sucursalId) {
            $this->editingSucursal = Sucursal::find($sucursalId);
            $this->name = $this->editingSucursal->name;
            $this->code = $this->editingSucursal->code;
            $this->codeSunat = $this->editingSucursal->codeSunat ?? '';
            $this->igv = $this->editingSucursal->igv ?? 18.00;
            $this->serieFactura = $this->editingSucursal->serieFactura ?? '';
            $this->serieBoleta = $this->editingSucursal->serieBoleta ?? '';
            $this->serieGuiaRemision = $this->editingSucursal->serieGuiaRemision ?? '';
            $this->serieNotaCreditoFactura = $this->editingSucursal->serieNotaCreditoFactura ?? '';
            $this->serieNotaCreditoBoleta = $this->editingSucursal->serieNotaCreditoBoleta ?? '';
            $this->serieNotaDebitoFactura = $this->editingSucursal->serieNotaDebitoFactura ?? '';
            $this->serieNotaDebitoBoleta = $this->editingSucursal->serieNotaDebitoBoleta ?? '';
            $this->color = $this->editingSucursal->color ?? '#3B82F6';
            $this->address = $this->editingSucursal->address;
            $this->phone = $this->editingSucursal->phone ?? '';
            $this->email = $this->editingSucursal->email ?? '';
            $this->departamento = $this->editingSucursal->departamento ?? '';
            $this->provincia = $this->editingSucursal->provincia ?? '';
            $this->distrito = $this->editingSucursal->distrito ?? '';
            $this->urbanizacion = $this->editingSucursal->urbanizacion ?? '';
            $this->ubigeo = $this->editingSucursal->ubigeo ?? '';
            $this->isActive = $this->editingSucursal->isActive;
        } else {
            $this->resetSucursalForm();
        }
        $this->showSucursalModal = true;
    }

    public function saveSucursal(): void
    {
        // Si está editando, quitar la validación unique del code
        if ($this->editingSucursal) {
            $this->rules['code'] = 'required|string|max:255';
        }
        $data = $this->validate($this->rules, $this->messages);

        try {
            if ($this->editingSucursal) {
                $this->sucursalService->actualizarSucursal($this->editingSucursal->id, $data);
            } else {
                $this->sucursalService->crearSucursal($data);
            }
            $this->closeSucursalModal();
            session()->flash('message', 'Sucursal guardada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('message', $e->getMessage());
        }
    }

    public function closeSucursalModal(): void
    {
        $this->showSucursalModal = false;
        $this->resetSucursalForm();
        $this->editingSucursal = null;
        $this->resetValidation();
    }

    public function resetSucursalForm(): void
    {
        $this->reset([
            'name', 'code', 'codeSunat', 'igv', 'serieFactura', 'serieBoleta',
            'serieGuiaRemision', 'serieNotaCreditoFactura', 'serieNotaCreditoBoleta',
            'serieNotaDebitoFactura', 'serieNotaDebitoBoleta', 'color', 'address',
            'phone', 'email', 'departamento', 'provincia', 'distrito', 'urbanizacion',
            'ubigeo', 'isActive'
        ]);
        $this->igv = 18.00;
        $this->color = '#3B82F6';
    }

    public function confirmDelete($sucursalId): void
    {
        $this->sucursalToDelete = $sucursalId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->sucursalToDelete = null;
    }

    public function deleteSucursal(): void
    {
        try {
            $this->sucursalService->eliminarSucursal($this->sucursalToDelete);
            $this->showDeleteModal = false;
            $this->sucursalToDelete = null;
            session()->flash('message', 'Sucursal eliminada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('message', $e->getMessage());
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }
}
