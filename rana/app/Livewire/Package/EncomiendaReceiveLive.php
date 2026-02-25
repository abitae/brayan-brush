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

class EncomiendaReceiveLive extends Component
{
    use WithPagination, ToastTrait;

    protected $encomiendaService;

    public function __construct()
    {
        $this->encomiendaService = app(EncomiendaService::class);
    }

    // Estados del componente
    public $search = '';
    public $fecha_creacion_filter = '';
    public $fecha_envio_filter = '';
    public $filterRuta = '';
    public $perPage = 20;
    public $selectedEncomiendas = [];
    public $selectAll = false;
    public $showReceiveModal = false;
    public $fecha_recepcion = '';
    public $rutasDisponibles = [];
    protected $updatingSelectAll = false;

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

    public function mount(): void
    {
        if (empty($this->fecha_creacion_filter)) {
            $this->fecha_creacion_filter = Carbon::today()->format('Y-m-d');
        }
    }

    protected $listeners = ['show-encomienda-details' => 'handleShowDetails'];

    public function handleShowDetails($id)
    {
        $this->showEncomiendaDetails($id);
    }

    public function boot(): void
    {
        // Obtener rutas disponibles desde encomiendas enviadas a esta sucursal
        $sucursalUsuario = Auth::user()->sucursal->id ?? null;
        
        $rutas = Encomienda::where('estado_encomienda', 'ENVIADO')
            ->where('sucursal_dest_id', $sucursalUsuario)
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
            'estado_encomienda' => 'ENVIADO',
            'sucursal_dest_id' => $sucursalUsuario,
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

        // Actualizar el estado de selectAll basado en la página actual
        if (!$this->updatingSelectAll) {
            if ($encomiendas->count() > 0) {
                $currentPageIds = $encomiendas->pluck('id')->map(fn($id) => (string) $id)->toArray();
                $selectedInCurrentPage = array_intersect($this->selectedEncomiendas, $currentPageIds);
                $newSelectAllValue = count($selectedInCurrentPage) === count($currentPageIds) && count($currentPageIds) > 0;
                if ($this->selectAll !== $newSelectAllValue) {
                    $this->selectAll = $newSelectAllValue;
                }
            } else {
                if ($this->selectAll !== false) {
                    $this->selectAll = false;
                }
            }
        }

        return view('livewire.package.encomienda-receive-live', [
            'encomiendas' => $encomiendas,
            'rutasDisponibles' => $this->rutasDisponibles ?? collect(),
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFechaCreacionFilter(): void
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

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedEncomiendas(): void
    {
        $sucursalUsuario = Auth::user()->sucursal->id ?? null;
        $filters = [
            'estado_encomienda' => 'ENVIADO',
            'sucursal_dest_id' => $sucursalUsuario,
        ];

        // Filtrar por ruta si está seleccionada
        if (!empty($this->filterRuta)) {
            $filters['ruta_id'] = $this->filterRuta;
        }

        if (!empty($this->fecha_creacion_filter)) {
            $filters['fecha_creacion'] = $this->fecha_creacion_filter;
        }

        $encomiendas = $this->encomiendaService->getAll($this->search, $filters, $this->perPage);
        $currentPageIds = $encomiendas->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $selectedInCurrentPage = array_intersect($this->selectedEncomiendas, $currentPageIds);

        $this->selectAll = count($selectedInCurrentPage) === count($currentPageIds) && count($currentPageIds) > 0;
    }

    public function updatedSelectAll($value): void
    {
        // Guardar el valor actual de fecha_creacion_filter para evitar que se pierda
        $fechaGuardada = $this->fecha_creacion_filter ?? Carbon::today()->format('Y-m-d');
        
        // Marcar que estamos actualizando selectAll para evitar que render() lo sobrescriba
        $this->updatingSelectAll = true;
        
        $sucursalUsuario = Auth::user()->sucursal->id ?? null;
        $filters = [
            'estado_encomienda' => 'ENVIADO',
            'sucursal_dest_id' => $sucursalUsuario,
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
        // Usar perPage = 0 para obtener todos los registros
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

    public function openReceiveModal(): void
    {
        if (empty($this->selectedEncomiendas)) {
            $this->error('Debe seleccionar al menos una encomienda para recibir.');
            return;
        }

        // Verificar que todas las encomiendas seleccionadas estén en estado ENVIADO
        $encomiendasInvalidas = Encomienda::whereIn('id', $this->selectedEncomiendas)
            ->where('estado_encomienda', '!=', 'ENVIADO')
            ->count();

        if ($encomiendasInvalidas > 0) {
            $this->error('Solo se pueden recibir encomiendas en estado ENVIADO.');
            return;
        }

        // Verificar que todas las encomiendas sean para esta sucursal
        $sucursalUsuario = Auth::user()->sucursal->id ?? null;
        $encomiendasSucursalIncorrecta = Encomienda::whereIn('id', $this->selectedEncomiendas)
            ->where('sucursal_dest_id', '!=', $sucursalUsuario)
            ->count();

        if ($encomiendasSucursalIncorrecta > 0) {
            $this->error('Algunas encomiendas no corresponden a esta sucursal destino.');
            return;
        }

        // Inicializar fecha y hora con Carbon
        $this->fecha_recepcion = Carbon::now()->format('Y-m-d\TH:i');
        $this->showReceiveModal = true;
    }

    public function closeReceiveModal(): void
    {
        $this->showReceiveModal = false;
        $this->resetReceiveForm();
    }

    public function resetReceiveForm(): void
    {
        $this->reset(['fecha_recepcion']);
        // Reinicializar fecha y hora con Carbon
        $this->fecha_recepcion = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function receiveEncomiendas(): void
    {
        $this->validate([
            'fecha_recepcion' => 'required|date',
        ], [
            'fecha_recepcion.required' => 'La fecha de recepción es obligatoria.',
        ]);

        try {
            // Obtener todas las encomiendas seleccionadas
            $encomiendas = Encomienda::whereIn('id', $this->selectedEncomiendas)
                ->where('estado_encomienda', 'ENVIADO')
                ->with('ruta')
                ->get();

            if ($encomiendas->isEmpty()) {
                $this->error('No hay encomiendas válidas para recibir.');
                return;
            }

            // Agrupar encomiendas por ruta_id
            $encomiendasPorRuta = $encomiendas->groupBy('ruta_id');
            $totalRecibidas = 0;
            $errores = [];
            $resumenPorRuta = [];

            foreach ($encomiendasPorRuta as $rutaId => $encomiendasRuta) {
                $rutaNombre = 'Sin ruta';
                if ($rutaId) {
                    $ruta = $encomiendasRuta->first()->ruta;
                    if ($ruta) {
                        $origenNombre = $ruta->sucursalOrigen->name ?? 'N/A';
                        $destinoNombre = $ruta->sucursalDestino->name ?? 'N/A';
                        $rutaNombre = "Ruta #{$ruta->id} ({$origenNombre} → {$destinoNombre})";
                    }
                }

                $recibidasRuta = 0;
                $erroresRuta = [];

                foreach ($encomiendasRuta as $encomienda) {
                    try {
                        $fechaRecepcion = Carbon::parse($this->fecha_recepcion);
                        $this->encomiendaService->recibirEncomienda($encomienda->id, $fechaRecepcion);
                        $recibidasRuta++;
                        $totalRecibidas++;
                    } catch (\Exception $e) {
                        $erroresRuta[] = "Encomienda {$encomienda->code}: " . $e->getMessage();
                        $errores[] = "{$rutaNombre} - Encomienda {$encomienda->code}: " . $e->getMessage();
                    }
                }

                if ($recibidasRuta > 0) {
                    $resumenPorRuta[] = "{$rutaNombre}: {$recibidasRuta} encomienda(s)";
                }
            }

            $this->closeReceiveModal();
            $this->selectedEncomiendas = [];
            $this->selectAll = false;

            if ($totalRecibidas > 0) {
                $mensaje = "Se recibieron {$totalRecibidas} encomienda(s) exitosamente.";
                if (!empty($resumenPorRuta)) {
                    $mensaje .= " Por ruta: " . implode(', ', $resumenPorRuta) . ".";
                }
                if (!empty($errores)) {
                    $mensaje .= " Errores: " . implode('; ', array_slice($errores, 0, 3));
                    if (count($errores) > 3) {
                        $mensaje .= " y " . (count($errores) - 3) . " más.";
                    }
                }
                $this->success($mensaje);
            } else {
                $this->error('No se pudo recibir ninguna encomienda. ' . implode('; ', array_slice($errores, 0, 3)));
            }
        } catch (\Exception $e) {
            $this->error('Error al recibir las encomiendas: ' . $e->getMessage());
        }
    }

    public function getSelectedEncomiendasCountProperty()
    {
        return count($this->selectedEncomiendas);
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
}

