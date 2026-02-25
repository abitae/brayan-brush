<?php

namespace App\Livewire\Package;

use App\Models\Package\Encomienda;
use App\Models\Package\RutaSucursal;
use App\Models\Package\Manifiesto;
use App\Services\Package\EncomiendaService;
use App\Exports\ManifiestoExport;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\ToastTrait;
use App\Traits\createDocumentoTrait;
use Maatwebsite\Excel\Facades\Excel;

class EncomiendaSendLive extends Component
{
    use WithPagination, ToastTrait, createDocumentoTrait;

    protected $encomiendaService;

    public function __construct()
    {
        $this->encomiendaService = app(EncomiendaService::class);
    }

    // Estados del componente
    public $showSendModal = false;
    public $search = '';
    public $filterRuta = '';
    public $fecha_creacion_filter = '';
    public $perPage = 20;
    public $selectedEncomiendas = [];
    public $selectAll = false;
    public $rutasDisponibles;
    protected $updatingSelectAll = false;

    // Datos para envío
    public $fecha_envio = '';
    public $transportista_envio_id = '';
    public $vehiculo_envio_id = '';
    public $observacion_envio = '';

    // Modal de manifiestos
    public $showManifiestoModal = false;
    public $manifiestosCreados = [];

    // Modal de ticket
    public $modalImprimirTicket = false;
    public $encomienda_id = null;

    // Modal de invoice
    public $modalVerInvoice = false;
    public $invoice_id = null;

    // Modal de guía
    public $modalVerGuia = false;
    public $guia_id = null;

    // Modal de sticker
    public $modalVerSticker = false;

    // Modal de declaración
    public $modalVerDeclaracion = false;

    // Modal de detalles
    public $showDetailsModal = false;
    public $encomiendaDetalle = null;

    // Carbon para fechas
    protected $casts = [
        'fecha_envio' => 'datetime',
    ];

    public function mount(): void
    {
        if (empty($this->fecha_creacion_filter)) {
            $this->fecha_creacion_filter = Carbon::today()->format('Y-m-d');
        }
    }

    public function boot(): void
    {
        // Obtener rutas activas desde la sucursal actual
        $rutas = RutaSucursal::where('sucursal_origen_id', Auth::user()->sucursal->id)
            ->where('isActive', true)
            ->where('estado_ruta', 'ACTIVA')
            ->where('fecha_salida', '>=', Carbon::now()->format('Y-m-d'))
            ->with(['sucursalOrigen', 'sucursalDestino', 'transportista', 'vehiculo'])
            ->orderBy('fecha_salida', 'asc')
            ->get();

        $this->rutasDisponibles = $rutas;

        if (empty($this->filterRuta) && $this->rutasDisponibles->isNotEmpty()) {
            $this->filterRuta = $this->rutasDisponibles->first()->id;
        }

    }

    public function render()
    {
        $filters = [
            'estado_encomienda' => 'REGISTRADO',
            'sucursal_id' => Auth::user()->sucursal_id
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
        $transportistas = $this->encomiendaService->getTransportistas();
        $vehiculos = $this->encomiendaService->getVehiculos();

        // Actualizar el estado de selectAll basado en la página actual
        // Solo actualizar si no se está ejecutando updatedSelectAll para evitar conflictos
        if (!$this->updatingSelectAll) {
            if ($encomiendas->count() > 0) {
                $currentPageIds = $encomiendas->pluck('id')->map(fn($id) => (string) $id)->toArray();
                $selectedInCurrentPage = array_intersect($this->selectedEncomiendas, $currentPageIds);
                // Solo actualizar selectAll si realmente cambió para evitar re-renders innecesarios
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

        return view('livewire.package.encomienda-send-live', [
            'encomiendas' => $encomiendas,
            'rutasDisponibles' => $this->rutasDisponibles ?? collect(),
            'transportistas' => $transportistas,
            'vehiculos' => $vehiculos,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSucursalDest(): void
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


    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedEncomiendas(): void
    {
        $filters = [
            'estado_encomienda' => 'REGISTRADO',
            'sucursal_id' => Auth::user()->sucursal_id
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
        
        $filters = [
            'estado_encomienda' => 'REGISTRADO',
            'sucursal_id' => Auth::user()->sucursal_id
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

    public function openSendModal(): void
    {
        if (empty($this->selectedEncomiendas)) {
            $this->error('Debe seleccionar al menos una encomienda para enviar.');
            return;
        }

        // Verificar que todas las encomiendas seleccionadas estén en estado REGISTRADO
        $encomiendasInvalidas = Encomienda::whereIn('id', $this->selectedEncomiendas)
            ->where('estado_encomienda', '!=', 'REGISTRADO')
            ->count();

        if ($encomiendasInvalidas > 0) {
            $this->error('Solo se pueden enviar encomiendas en estado REGISTRADO.');
            return;
        }

        // Inicializar fecha y hora con Carbon
        $this->fecha_envio = Carbon::now()->format('Y-m-d\TH:i');
        $this->showSendModal = true;
    }

    public function closeSendModal(): void
    {
        $this->showSendModal = false;
        $this->resetSendForm();
    }

    public function resetSendForm(): void
    {
        $this->reset([
            'fecha_envio'
        ]);
        // Reinicializar fecha y hora con Carbon
        $this->fecha_envio = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function sendEncomiendas(): void
    {
        $this->validate([
            'fecha_envio' => 'required|date',
        ], [
            'fecha_envio.required' => 'La fecha de envío es obligatoria.',
        ]);

        // Validar que todas las encomiendas tengan ruta_id
        $encomiendasSinRuta = Encomienda::whereIn('id', $this->selectedEncomiendas)
            ->whereNull('ruta_id')
            ->count();

        if ($encomiendasSinRuta > 0) {
            $this->error('Todas las encomiendas deben tener una ruta asignada.');
            return;
        }

        DB::beginTransaction();
        try {
            // Obtener todas las encomiendas seleccionadas
            $encomiendas = Encomienda::whereIn('id', $this->selectedEncomiendas)
                ->where('estado_encomienda', 'REGISTRADO')
                ->get();

            if ($encomiendas->isEmpty()) {
                $this->error('No hay encomiendas válidas para enviar.');
                DB::rollBack();
                return;
            }

            // Agrupar encomiendas por ruta_id
            $encomiendasPorRuta = $encomiendas->groupBy('ruta_id');
            $manifiestosCreados = [];
            $rutasCompletadas = [];

            foreach ($encomiendasPorRuta as $rutaId => $encomiendasRuta) {
                $ruta = RutaSucursal::find($rutaId);
                
                if (!$ruta) {
                    continue;
                }

                $idsEncomiendas = $encomiendasRuta->pluck('id')->toArray();

                // Actualizar estado de encomiendas a ENVIADO
                foreach ($encomiendasRuta as $encomienda) {
                    $this->encomiendaService->updateEstado($encomienda->id, 'ENVIADO', $this->fecha_envio);
                }

                // Crear manifiesto
                $manifiesto = Manifiesto::create([
                    'sucursal_id' => Auth::user()->sucursal->id,
                    'sucursal_destino_id' => $ruta->sucursal_destino_id,
                    'ids' => json_encode($idsEncomiendas),
                ]);

                $manifiestosCreados[] = $manifiesto;

                // Actualizar estado de la ruta a COMPLETADO
                if (!in_array($rutaId, $rutasCompletadas)) {
                    $ruta->update([
                        'estado_ruta' => 'COMPLETADO',
                        'isActive' => false,
                    ]);
                    $rutasCompletadas[] = $rutaId;
                }
            }

            DB::commit();

            $this->closeSendModal();
            $this->selectedEncomiendas = [];
            $this->selectAll = false;

            // Guardar manifiestos creados y mostrar modal
            $this->manifiestosCreados = $manifiestosCreados;
            $this->showManifiestoModal = true;

            // Crear mensaje con resumen por ruta
            $totalEnviadas = $encomiendas->count();
            $resumenPorRuta = [];
            
            foreach ($encomiendasPorRuta as $rutaId => $encomiendasRuta) {
                $ruta = RutaSucursal::find($rutaId);
                if ($ruta) {
                    $origenNombre = $ruta->sucursalOrigen->name ?? 'N/A';
                    $destinoNombre = $ruta->sucursalDestino->name ?? 'N/A';
                    $rutaNombre = "Ruta #{$ruta->id} ({$origenNombre} → {$destinoNombre})";
                    $cantidad = $encomiendasRuta->count();
                    $resumenPorRuta[] = "{$rutaNombre}: {$cantidad} encomienda(s)";
                }
            }

            $mensaje = "Se enviaron {$totalEnviadas} encomienda(s) exitosamente. Se crearon " . count($manifiestosCreados) . " manifiesto(s).";
            if (!empty($resumenPorRuta)) {
                $mensaje .= " Por ruta: " . implode(', ', $resumenPorRuta) . ".";
            }
            
            $this->success($mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error al enviar las encomiendas: ' . $e->getMessage());
        }
    }

    public function getSelectedEncomiendasCountProperty()
    {
        return count($this->selectedEncomiendas);
    }

    public function getSelectAllTextProperty()
    {
        $filters = [
            'estado_encomienda' => 'REGISTRADO',
            'sucursal_id' => Auth::user()->sucursal_id
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

        $currentPageIds = $encomiendas->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $selectedInCurrentPage = array_intersect($this->selectedEncomiendas, $currentPageIds);

        return count($selectedInCurrentPage) . '/' . count($currentPageIds);
    }

    public function closeManifiestoModal(): void
    {
        $this->showManifiestoModal = false;
        $this->manifiestosCreados = [];
    }

    public function downloadManifiesto($manifiestoId)
    {
        try {
            $manifiesto = Manifiesto::with(['sucursal', 'destino'])->find($manifiestoId);
            
            if (!$manifiesto) {
                $this->error('Manifiesto no encontrado.');
                return null;
            }

            $ids = json_decode($manifiesto->ids, true);
            
            if (empty($ids) || !is_array($ids)) {
                $this->error('El manifiesto no tiene encomiendas asociadas.');
                return null;
            }

            $sucursalOrigen = $manifiesto->sucursal->name ?? 'Origen';
            $sucursalDestino = $manifiesto->destino->name ?? 'Destino';
            
            // Limpiar el nombre del archivo de caracteres especiales
            $sucursalOrigen = preg_replace('/[^a-zA-Z0-9_-]/', '_', $sucursalOrigen);
            $sucursalDestino = preg_replace('/[^a-zA-Z0-9_-]/', '_', $sucursalDestino);
            
            $nombreArchivo = 'manifiesto-' . $sucursalOrigen . '-' . $sucursalDestino . '-' . $manifiesto->id . '.xlsx';
            
            return Excel::download(new ManifiestoExport($ids), $nombreArchivo);
        } catch (\Exception $e) {
            $this->error('Error al descargar el manifiesto: ' . $e->getMessage());
            return null;
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

    public function crearGuiaPDF($encomiendaId)
    {
        $encomienda = Encomienda::findOrFail($encomiendaId);
        $guia = $this->createGuiTrans($encomienda);
        if (!$guia) {
            $this->error('Error al crear la guía de remisión');
        }
    }

    public function verStickerPDF($encomiendaId)
    {
        $this->encomienda_id = $encomiendaId;
        $this->modalVerSticker = true;
    }

    public function refreshSticker()
    {
        $this->dispatch('sticker-refreshed');
    }

    public function verDeclaracionPDF($encomiendaId)
    {
        $this->encomienda_id = $encomiendaId;
        $this->modalVerDeclaracion = true;
    }

    public function refreshDeclaracion()
    {
        $this->dispatch('declaracion-refreshed');
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

    protected $listeners = ['show-encomienda-details' => 'handleShowDetails'];

    public function handleShowDetails($id)
    {
        $this->showEncomiendaDetails($id);
    }
}
