<?php

namespace App\Livewire\Package;

use App\Models\Package\Encomienda;
use App\Models\Package\RutaSucursal;
use App\Services\Package\EncomiendaService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\ToastTrait;
use App\Traits\createDocumentoTrait;

class EncomiendaReturnLive extends Component
{
    use WithPagination, ToastTrait, createDocumentoTrait;

    protected $encomiendaService;

    public function __construct()
    {
        $this->encomiendaService = app(EncomiendaService::class);
    }

    // Estados del componente
    public $search = '';
    public $fecha_creacion_filter = '';
    public $filterRuta = '';
    public $perPage = 20;
    public $selectedEncomiendas = [];
    public $selectAll = false;
    public $rutasDisponibles = [];
    protected $updatingSelectAll = false;

    // Modal de retorno
    public $showReturnModal = false;
    public $selectedEncomienda = null;
    public $encomiendaSeleccionada = null;
    public $motivo_retorno = '';
    public $fecha_retorno = '';

    // Modal de ticket
    public $modalImprimirTicket = false;
    public $encomienda_id = null;

    // Modal de invoice
    public $modalVerInvoice = false;
    public $invoice_id = null;

    // Modal de guía
    public $modalVerGuia = false;
    public $guia_id = null;

    // Modal de detalles
    public $showDetailsModal = false;
    public $encomiendaDetalle = null;

    // Modal de cobro (para CONTRA ENTREGA)
    public $showCobroModal = false;
    public $encomiendaCobro = null;
    public $tipoComprobante = 'BOLETA'; // BOLETA o FACTURA
    public $metodoPago = 'EFECTIVO';

    public function mount(): void
    {
        if (empty($this->fecha_creacion_filter)) {
            $this->fecha_creacion_filter = Carbon::today()->format('Y-m-d');
        }
    }

    public function boot(): void
    {
        // Obtener rutas disponibles desde encomiendas recibidas en esta sucursal
        $sucursalUsuario = Auth::user()->sucursal->id ?? null;
        
        $rutas = Encomienda::where('estado_encomienda', 'RECIBIDO')
            ->where('sucursal_dest_id', $sucursalUsuario)
            ->where('isReturn', true) // Solo encomiendas de retorno
            ->whereNotNull('ruta_id')
            ->with(['ruta.sucursalOrigen', 'ruta.sucursalDestino', 'ruta.transportista', 'ruta.vehiculo'])
            ->get()
            ->pluck('ruta')
            ->filter()
            ->unique('id')
            ->values();
            
        $this->rutasDisponibles = $rutas;

        $firstRuta = $this->rutasDisponibles[0] ?? null;
        if (empty($this->filterRuta) && $firstRuta) {
            $this->filterRuta = $firstRuta->id;
        }
    }

    public function render()
    {
        $sucursalUsuario = Auth::user()->sucursal->id ?? null;

        $filters = [
            'estado_encomienda' => 'RECIBIDO',
            'sucursal_dest_id' => $sucursalUsuario,
            'isReturn' => true, // Solo encomiendas de retorno
        ];

        // Filtrar por ruta si está seleccionada
        if (!empty($this->filterRuta)) {
            $filters['ruta_id'] = $this->filterRuta;
        }

        // Filtrar por fecha de creación si está seleccionada
        if (!empty($this->fecha_creacion_filter)) {
            $filters['fecha_creacion'] = $this->fecha_creacion_filter;
        }

        $encomiendas = $this->encomiendaService->getAll($this->search, $filters, $this->perPage);

        // Actualizar selectAll basado en las encomiendas seleccionadas
        if (!$this->updatingSelectAll) {
            if ($encomiendas->count() > 0) {
                $currentPageIds = $encomiendas->pluck('id')->map(fn($id) => (string) $id)->toArray();
                $this->selectAll = !empty($currentPageIds) && count(array_intersect($this->selectedEncomiendas, $currentPageIds)) === count($currentPageIds);
            } else {
                $this->selectAll = false;
            }
        }

        return view('livewire.package.encomienda-return-live', [
            'encomiendas' => $encomiendas,
        ]);
    }

    public function getSelectedEncomiendasCountProperty()
    {
        return count($this->selectedEncomiendas);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectedEncomiendas = [];
        $this->selectAll = false;
    }

    public function updatedFilterRuta(): void
    {
        $this->resetPage();
        $this->selectedEncomiendas = [];
        $this->selectAll = false;
    }

    public function updatedFechaCreacionFilter(): void
    {
        $this->resetPage();
        $this->selectedEncomiendas = [];
        $this->selectAll = false;
    }

    public function updatedSelectedEncomiendas(): void
    {
        // No hacer nada especial, solo permitir que Livewire maneje el estado
    }

    public function updatedSelectAll($value): void
    {
        // Guardar el valor actual de fecha_creacion_filter antes de cualquier cambio
        $fechaGuardada = $this->fecha_creacion_filter ?? Carbon::today()->format('Y-m-d');
        
        // Marcar que estamos actualizando selectAll para evitar que render() lo sobrescriba
        $this->updatingSelectAll = true;
        
        $filters = [
            'estado_encomienda' => 'RECIBIDO',
            'sucursal_dest_id' => Auth::user()->sucursal->id ?? null,
            'isReturn' => true, // Solo encomiendas de retorno
        ];

        // Filtrar por ruta si está seleccionada
        if (!empty($this->filterRuta)) {
            $filters['ruta_id'] = $this->filterRuta;
        }

        // Filtrar por fecha de creación si está seleccionada - usar fecha guardada
        if (!empty($fechaGuardada)) {
            $filters['fecha_creacion'] = $fechaGuardada;
        }

        // Obtener TODAS las encomiendas que cumplen con los filtros (sin paginación)
        $encomiendas = $this->encomiendaService->getAll($this->search, $filters, 0);
        $allIds = $encomiendas->pluck('id')->map(fn($id) => (string) $id)->toArray();

        if ($value) {
            // Seleccionar TODAS las encomiendas que cumplen con los filtros
            $this->selectedEncomiendas = array_unique(array_merge($this->selectedEncomiendas, $allIds));
        } else {
            // Deseleccionar TODAS las encomiendas que cumplen con los filtros
            $this->selectedEncomiendas = array_values(array_diff($this->selectedEncomiendas, $allIds));
        }
        
        // CRÍTICO: Restaurar el valor de fecha_creacion_filter ANTES de cualquier re-render
        $this->fecha_creacion_filter = $fechaGuardada;
        
        // Permitir que render() actualice selectAll nuevamente
        $this->updatingSelectAll = false;
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function openReturnModal($encomiendaId = null): void
    {
        // Si no se proporciona ID, usar la primera encomienda seleccionada
        if ($encomiendaId === null) {
            if (empty($this->selectedEncomiendas)) {
                $this->error('Debe seleccionar al menos una encomienda para retornar.');
                return;
            }
            $encomiendaId = $this->selectedEncomiendas[0];
        }

        $encomienda = Encomienda::with([
            'remitente',
            'destinatario',
            'sucursal_remitente',
            'sucursal_destinatario',
            'paquetes'
        ])->findOrFail($encomiendaId);

        // Validar que la encomienda esté en estado RECIBIDO
        if ($encomienda->estado_encomienda !== 'RECIBIDO') {
            $this->error('Solo se pueden retornar encomiendas en estado RECIBIDO.');
            return;
        }

        // Validar que la encomienda sea de esta sucursal
        $sucursalUsuario = Auth::user()->sucursal->id ?? null;
        if ($encomienda->sucursal_dest_id !== $sucursalUsuario) {
            $this->error('Esta encomienda no corresponde a esta sucursal.');
            return;
        }

        // Validar que sea de retorno
        if (!$encomienda->isReturn) {
            $this->error('Esta encomienda no es de retorno.');
            return;
        }

        $this->encomiendaSeleccionada = $encomienda;
        $this->selectedEncomienda = $encomiendaId;
        $this->motivo_retorno = '';
        $this->fecha_retorno = Carbon::now()->format('Y-m-d\TH:i');
        $this->showReturnModal = true;
    }

    public function closeReturnModal(): void
    {
        $this->showReturnModal = false;
        $this->resetReturnForm();
    }

    public function resetReturnForm(): void
    {
        $this->reset(['motivo_retorno', 'fecha_retorno', 'selectedEncomienda', 'encomiendaSeleccionada']);
        $this->fecha_retorno = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function returnEncomienda(): void
    {
        $this->validate([
            'motivo_retorno' => 'required|string|min:10|max:500',
            'fecha_retorno' => 'required|date',
        ], [
            'motivo_retorno.required' => 'El motivo del retorno es obligatorio.',
            'motivo_retorno.min' => 'El motivo debe tener al menos 10 caracteres.',
            'motivo_retorno.max' => 'El motivo no puede exceder 500 caracteres.',
            'fecha_retorno.required' => 'La fecha de retorno es obligatoria.',
        ]);

        try {
            $this->encomiendaService->retornarEncomienda(
                $this->selectedEncomienda,
                $this->motivo_retorno,
                Carbon::parse($this->fecha_retorno)
            );

            // Remover de seleccionadas
            $this->selectedEncomiendas = array_values(array_diff($this->selectedEncomiendas, [(string)$this->selectedEncomienda]));
            
            $this->closeReturnModal();
            $this->success('Encomienda retornada exitosamente.');
        } catch (\Exception $e) {
            $this->error('Error al retornar la encomienda: ' . $e->getMessage());
        }
    }

    public function verTicketPDF($encomiendaId)
    {
        $this->encomienda_id = $encomiendaId;
        $this->modalImprimirTicket = true;
    }

    public function refreshTicket()
    {
        $this->dispatch('ticket-refreshed');
    }

    public function verInvoicePDF($encomiendaId)
    {
        $encomienda = Encomienda::find($encomiendaId);
        if ($encomienda && $encomienda->doc_factura) {
            $this->invoice_id = $encomienda->doc_factura;
            $this->modalVerInvoice = true;
        } else {
            $this->error('Esta encomienda no tiene una factura asociada.');
        }
    }

    public function refreshInvoice()
    {
        $this->dispatch('invoice-refreshed');
    }

    public function verGuiaPDF($encomiendaId)
    {
        $encomienda = Encomienda::find($encomiendaId);
        if ($encomienda && $encomienda->doc_guia) {
            $this->guia_id = $encomiendaId;
            $this->modalVerGuia = true;
        } else {
            $this->error('Esta encomienda no tiene una guía asociada.');
        }
    }

    public function refreshGuia()
    {
        $this->dispatch('guia-refreshed');
    }

    public function showEncomiendaDetails($encomiendaId)
    {
        $this->encomiendaDetalle = Encomienda::with([
            'remitente',
            'destinatario',
            'sucursal_remitente',
            'sucursal_destinatario',
            'facturacion',
            'invoice',
            'ruta.sucursalOrigen',
            'ruta.sucursalDestino',
            'ruta.transportista',
            'ruta.vehiculo',
            'transportista',
            'vehiculo',
            'paquetes'
        ])->find($encomiendaId);

        if ($this->encomiendaDetalle) {
            $this->showDetailsModal = true;
        } else {
            $this->error('No se pudo cargar la información de la encomienda.');
        }
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->encomiendaDetalle = null;
    }

    public function openCobroModal($encomiendaId)
    {
        $encomienda = Encomienda::with([
            'remitente',
            'destinatario',
            'sucursal_remitente',
            'sucursal_destinatario',
            'paquetes'
        ])->findOrFail($encomiendaId);

        // Validar que sea CONTRA ENTREGA
        if ($encomienda->estado_pago !== 'CONTRA ENTREGA') {
            $this->error('Esta encomienda no es de contra entrega.');
            return;
        }

        // Validar que no tenga invoice ya creado
        if ($encomienda->doc_factura) {
            $this->error('Esta encomienda ya tiene un comprobante de pago asociado.');
            return;
        }

        $this->encomiendaCobro = $encomienda;
        $this->tipoComprobante = 'BOLETA'; // Por defecto boleta
        $this->metodoPago = 'EFECTIVO';
        $this->showCobroModal = true;
    }

    public function closeCobroModal()
    {
        $this->showCobroModal = false;
        $this->encomiendaCobro = null;
        $this->tipoComprobante = 'BOLETA';
        $this->metodoPago = 'EFECTIVO';
    }

    public function procesarCobro()
    {
        if (!$this->encomiendaCobro) {
            $this->error('No hay encomienda seleccionada para cobrar.');
            return;
        }

        $this->validate([
            'tipoComprobante' => 'required|in:BOLETA,FACTURA',
            'metodoPago' => 'required|string',
        ], [
            'tipoComprobante.required' => 'Debe seleccionar un tipo de comprobante.',
            'tipoComprobante.in' => 'El tipo de comprobante debe ser BOLETA o FACTURA.',
            'metodoPago.required' => 'Debe seleccionar un método de pago.',
        ]);

        try {
            // Actualizar tipo_comprobante en la encomienda
            $this->encomiendaCobro->tipo_comprobante = $this->tipoComprobante;
            $this->encomiendaCobro->metodo_pago = $this->metodoPago;
            $this->encomiendaCobro->save();

            // Crear el invoice usando el trait
            $invoice = $this->createInvoice($this->encomiendaCobro);

            // Actualizar doc_factura y estado_credito
            $this->encomiendaCobro->doc_factura = $invoice->id;
            $this->encomiendaCobro->estado_credito = 'CANCELADO';
            $this->encomiendaCobro->save();

            $this->closeCobroModal();
            $this->success('Cobro procesado exitosamente. ' . ($this->tipoComprobante === 'BOLETA' ? 'Boleta' : 'Factura') . ' creada.');
        } catch (\Exception $e) {
            $this->error('Error al procesar el cobro: ' . $e->getMessage());
        }
    }
}
