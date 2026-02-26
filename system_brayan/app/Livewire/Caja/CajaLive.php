<?php

namespace App\Livewire\Caja;

use App\Models\Caja\Caja;
use App\Services\Caja\CajaService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Configuration\TipoEntryCaja;
use App\Models\Configuration\TipoExitCaja;

class CajaLive extends Component
{
    use WithPagination;

    protected CajaService $cajaService;

    // Modales
    public bool $showEntryModal = false;
    public bool $showExitModal = false;
    public bool $showCajaModal = false;
    public bool $showHistorialModal = false;
    public bool $showPdfModal = false;
    public $editingEntry = null;
    public $editingExit = null;
    public $editingCaja = null;
    public $pdfUrl = '';

    // Formularios de caja
    public $monto_apertura = '';
    public $monto_cierre = '';
    public $isActive = true;

    // Formularios de entrada
    public $monto_entry = '';
    public $description_entry = '';
    public $metodo_pago_entry = 'EFECTIVO';
    public $tipo_entry = '';

    // Formularios de salida
    public $monto_exit = '';
    public $description_exit = '';
    public $metodo_pago_exit = 'EFECTIVO';
    public $tipo_exit = '';

    // Filtros
    public $search = '';
    public $filterStatus = '';

    protected array $cajaRules = [
        'monto_apertura' => 'required|numeric|min:0',
        'monto_cierre' => 'nullable|numeric|min:0',
    ];
    protected array $entryRules = [
        'monto_entry' => 'required|numeric|min:0',
        'description_entry' => 'required|string|max:255',
        'metodo_pago_entry' => 'required|in:EFECTIVO,YAPE,TARJETA,CHEQUE,TRANSFERENCIA,OTRO',
        'tipo_entry' => 'required|integer|exists:tipo_entry_cajas,id',
    ];
    protected array $exitRules = [
        'monto_exit' => 'required|numeric|min:0',
        'description_exit' => 'required|string|max:255',
        'metodo_pago_exit' => 'required|in:EFECTIVO,YAPE,TARJETA,CHEQUE,TRANSFERENCIA,OTRO',
        'tipo_exit' => 'required|integer|exists:tipo_exit_cajas,id',
    ];
    protected array $cajaMessages = [
        'monto_apertura.required' => 'El monto de apertura es obligatorio.',
        'monto_apertura.numeric' => 'El monto de apertura debe ser un número.',
        'monto_apertura.min' => 'El monto de apertura no puede ser negativo.',
        'monto_cierre.numeric' => 'El monto de cierre debe ser un número.',
        'monto_cierre.min' => 'El monto de cierre no puede ser negativo.',
    ];
    protected array $entryMessages = [
        'monto_entry.required' => 'El monto de la entrada es obligatorio.',
        'monto_entry.numeric' => 'El monto de la entrada debe ser un número.',
        'monto_entry.min' => 'El monto de la entrada no puede ser negativo.',
        'description_entry.required' => 'La descripción de la entrada es obligatoria.',
        'description_entry.string' => 'La descripción de la entrada debe ser texto.',
        'description_entry.max' => 'La descripción de la entrada es demasiado larga.',
        'metodo_pago_entry.required' => 'El método de pago de la entrada es obligatorio.',
        'metodo_pago_entry.in' => 'El método de pago de la entrada no es válido.',
        'tipo_entry.required' => 'El tipo de entrada es requerido.',
        'tipo_entry.integer' => 'El tipo de entrada es inválido.',
        'tipo_entry.exists' => 'El tipo de entrada seleccionado no existe.',
    ];
    protected array $exitMessages = [
        'monto_exit.required' => 'El monto de la salida es obligatorio.',
        'monto_exit.numeric' => 'El monto de la salida debe ser un número.',
        'monto_exit.min' => 'El monto de la salida no puede ser negativo.',
        'description_exit.required' => 'La descripción de la salida es obligatoria.',
        'description_exit.string' => 'La descripción de la salida debe ser texto.',
        'description_exit.max' => 'La descripción de la salida es demasiado larga.',
        'metodo_pago_exit.required' => 'El método de pago de la salida es obligatorio.',
        'metodo_pago_exit.in' => 'El método de pago de la salida no es válido.',
        'tipo_exit.required' => 'El tipo de salida es requerido.',
        'tipo_exit.integer' => 'El tipo de salida es inválido.',
        'tipo_exit.exists' => 'El tipo de salida seleccionado no existe.',
    ];

    public function boot(CajaService $cajaService): void
    {
        $this->cajaService = $cajaService;
    }

    public function mount(): void
    {
        $this->tipo_entry = array_key_first($this->entryTypes) ?? '';
        $this->tipo_exit = array_key_first($this->exitTypes) ?? '';
    }

    public function render()
    {
        $cajaActiva = $this->getCajaActiva();
        $historialCajas = Caja::where('user_id', Auth::id())->orderByDesc('created_at')->get();
        return view('livewire.caja.caja-live', [
            'cajaActiva' => $cajaActiva,
            'historialCajas' => $historialCajas,
            'entry_types' => $this->entryTypes,
            'exit_types' => $this->exitTypes,
        ]);
    }

    // Propiedades computadas para selects
    public function getEntryTypesProperty(): array
    {
        return TipoEntryCaja::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
    }
    public function getExitTypesProperty(): array
    {
        return TipoExitCaja::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
    }

    // Métodos de utilidad
    private function getCajaActiva(): ?Caja
    {
        return $this->cajaService->getCajaActiva(Auth::id());
    }

    // Caja
    public function openCajaModal($cajaId = null): void
    {
        if (is_null($cajaId) && !$this->cajaService->puedeCrearCaja(Auth::id())) {
            session()->flash('message', 'Ya tienes una caja activa. Debes cerrarla antes de abrir una nueva.');
            return;
        }
        if ($cajaId) {
            $this->editingCaja = Caja::with(['user', 'entries.tipoEntry', 'exits.tipoExit'])->find($cajaId);
            $this->monto_apertura = $this->editingCaja->monto_apertura;
            $this->monto_cierre = '';
            $this->isActive = $this->editingCaja->isActive;
        } else {
            $this->resetCajaForm();
        }
        $this->showCajaModal = true;
    }
    public function saveCaja(): void
    {
        if ($this->editingCaja) {
            // El monto de cierre debe ser igual al balance calculado
            $balance = $this->getBalance($this->editingCaja);
            $this->monto_cierre = $balance;
            // Validar solo que sea numérico (opcional, ya que lo asignamos)
            $this->validate(['monto_cierre' => $this->cajaRules['monto_cierre']], $this->cajaMessages);
        } else {
            $this->validate(['monto_apertura' => $this->cajaRules['monto_apertura']], $this->cajaMessages);
        }
        try {
            if ($this->editingCaja) {
                $cajaId = $this->editingCaja->id;
                $this->cajaService->cerrarCaja($cajaId, $this->monto_cierre);
                $this->closeCajaModal();
                session()->flash('message', 'Caja cerrada exitosamente.');
                // Mostrar automáticamente el PDF después de cerrar
                $this->previewPdf($cajaId);
            } else {
                $this->cajaService->crearCaja(Auth::id(), $this->monto_apertura);
                $this->closeCajaModal();
                session()->flash('message', 'Caja abierta exitosamente.');
            }
        } catch (\Exception $e) {
            session()->flash('message', $e->getMessage());
        }
    }
    public function closeCajaModal($cajaId = null): void
    {
        if ($cajaId) {
            try {
                $this->cajaService->cerrarCaja($cajaId);
                session()->flash('message', 'Caja cerrada exitosamente.');
            } catch (\Exception $e) {
                session()->flash('message', $e->getMessage());
            }
        }
        $this->showCajaModal = false;
        $this->resetCajaForm();
        $this->editingCaja = null;
    }
    public function resetCajaForm(): void
    {
        $this->reset(['monto_apertura', 'monto_cierre', 'isActive']);
    }

    // Entradas
    public function openEntryModal($entryId = null): void
    {
        $this->resetEntryForm();
        $this->showEntryModal = true;
    }
    public function saveEntry(): void
    {
        $this->validate($this->entryRules, $this->entryMessages);
        try {
            $cajaActiva = $this->getCajaActiva();
            if (!$cajaActiva) {
                throw new \Exception('No tienes una caja activa para agregar entradas.');
            }
            $this->cajaService->crearEntrada(
                $cajaActiva->id,
                $this->monto_entry,
                $this->description_entry,
                $this->tipo_entry,
                $this->metodo_pago_entry
            );
            $this->closeEntryModal();
            session()->flash('message', 'Entrada guardada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('message', $e->getMessage());
        }
    }
    public function closeEntryModal(): void
    {
        $this->showEntryModal = false;
        $this->resetEntryForm();
        $this->editingEntry = null;
    }
    public function resetEntryForm(): void
    {
        $this->reset(['monto_entry', 'description_entry', 'metodo_pago_entry']);
        $this->tipo_entry = array_key_first($this->entryTypes) ?? '';
    }

    // Salidas
    public function openExitModal($exitId = null): void
    {
        $this->resetExitForm();
        $this->showExitModal = true;
    }
    public function saveExit(): void
    {
        $this->validate($this->exitRules, $this->exitMessages);
        try {
            $cajaActiva = $this->getCajaActiva();
            if (!$cajaActiva) {
                throw new \Exception('No tienes una caja activa para agregar salidas.');
            }
            $this->cajaService->crearSalida(
                $cajaActiva->id,
                $this->monto_exit,
                $this->description_exit,
                $this->tipo_exit,
                $this->metodo_pago_exit
            );
            $this->closeExitModal();
            session()->flash('message', 'Salida guardada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('message', $e->getMessage());
        }
    }
    public function closeExitModal(): void
    {
        $this->showExitModal = false;
        $this->resetExitForm();
        $this->editingExit = null;
    }
    public function resetExitForm(): void
    {
        $this->reset(['monto_exit', 'description_exit', 'metodo_pago_exit']);
        $this->tipo_exit = array_key_first($this->exitTypes) ?? '';
    }

    // Utilidades
    public function getTotalEntries($caja): float
    {
        return $this->cajaService->getTotalEntradas($caja);
    }
    public function getTotalExits($caja): float
    {
        return $this->cajaService->getTotalSalidas($caja);
    }
    public function getBalance($caja): float
    {
        return $this->cajaService->calcularBalance($caja);
    }
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    // PDF
    public function previewPdf($cajaId): void
    {
        $caja = Caja::findOrFail($cajaId);
        
        // Verificar que el usuario tenga acceso a esta caja
        if ($caja->user_id !== Auth::id()) {
            session()->flash('message', 'No tienes permisos para ver esta caja.');
            return;
        }
        
        $this->pdfUrl = route('pdf.caja', $caja->id);
        $this->showPdfModal = true;
    }

    public function closePdfModal(): void
    {
        $this->showPdfModal = false;
        $this->pdfUrl = '';
    }
}
