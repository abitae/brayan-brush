<?php

namespace App\Livewire\Package;

use App\Models\Package\Encomienda;
use App\Models\Package\RutaSucursal;
use App\Models\Package\Customer;
use App\Services\Package\EncomiendaService;
use App\Services\Caja\CajaService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\ToastTrait;
use App\Traits\createDocumentoTrait;
use App\Traits\SearchDocument;

class EncomiendaHomeLive extends Component
{
    use WithPagination, ToastTrait, createDocumentoTrait, SearchDocument;

    protected $encomiendaService;
    protected CajaService $cajaService;

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

    // Modal de entrega
    public $showDeliverModal = false;
    public $selectedEncomienda = null;
    public $encomiendaSeleccionada = null;
    public $pin_verificacion = '';
    public $fecha_entrega = '';
    public $monto_descuento_entrega = 0;
    public $motivo_descuento_entrega = '';

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

    // Datos de facturación para el modal de entrega
    public $tipo_comprobante = 'TICKET';
    public $pinValido = true; // Siempre true para entrega a domicilio
    public $tipo_pago = 'CONTADO';
    public $metodo_pago = 'EFECTIVO';
    public $type_code_facturacion = 'DNI';
    public $code_facturacion;
    public $facturacion_id;
    public $name_facturacion;
    public $address_facturacion;
    public $ubigeo_facturacion;
    public $texto_ubigeo_facturacion;
    public $phone_facturacion;
    public $email_facturacion;
    public $ubigeos;

    public function mount(): void
    {
        if (empty($this->fecha_creacion_filter)) {
            $this->fecha_creacion_filter = Carbon::today()->format('Y-m-d');
        }
        
        // Cargar ubigeos
        $this->ubigeos = DB::table('ubigeo')
            ->select('ubigeo2', 'texto_ubigeo')
            ->orderBy('texto_ubigeo')
            ->get();
    }

    public function boot(CajaService $cajaService): void
    {
        $this->cajaService = $cajaService;
        // Obtener rutas disponibles desde encomiendas recibidas en esta sucursal
        $sucursalUsuario = Auth::user()->sucursal->id ?? null;
        
        $rutas = Encomienda::where('estado_encomienda', 'RECIBIDO')
            ->where('sucursal_dest_id', $sucursalUsuario)
            ->where('isHome', true) // Solo entrega a domicilio
            ->where('isReturn', false) // Excluir encomiendas de retorno
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
            'isHome' => true, // Solo encomiendas para entrega a domicilio
            'isReturn' => false, // Excluir encomiendas de retorno
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

        return view('livewire.package.encomienda-home-live', [
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
        // Forzar selección única
        if (count($this->selectedEncomiendas) > 1) {
            $lastSelected = end($this->selectedEncomiendas);
            $this->selectedEncomiendas = [(string) $lastSelected];
            $this->selectAll = false;
        }
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
            'isHome' => true, // Solo entrega a domicilio
            'isReturn' => false, // Excluir encomiendas de retorno
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

    public function openDeliverModal($encomiendaId = null): void
    {
        // Si no se proporciona ID, usar la primera encomienda seleccionada
        if ($encomiendaId === null) {
            if (empty($this->selectedEncomiendas)) {
                $this->error('Debe seleccionar al menos una encomienda para entregar.');
                return;
            }
            $encomiendaId = $this->selectedEncomiendas[0];
        }

        $encomienda = Encomienda::with([
            'remitente',
            'destinatario',
            'sucursal_remitente',
            'sucursal_destinatario',
            'paquetes',
            'facturacion'
        ])->findOrFail($encomiendaId);

        // Validar que la encomienda esté en estado RECIBIDO
        if ($encomienda->estado_encomienda !== 'RECIBIDO') {
            $this->error('Solo se pueden entregar encomiendas en estado RECIBIDO.');
            return;
        }

        // Validar que la encomienda sea de esta sucursal
        $sucursalUsuario = Auth::user()->sucursal->id ?? null;
        if ($encomienda->sucursal_dest_id !== $sucursalUsuario) {
            $this->error('Esta encomienda no corresponde a esta sucursal.');
            return;
        }

        // Validar que sea entrega a domicilio
        if (!$encomienda->isHome) {
            $this->error('Esta encomienda no es para entrega a domicilio.');
            return;
        }

        // Validar que no sea de retorno
        if ($encomienda->isReturn) {
            $this->error('Esta encomienda es de retorno y debe procesarse en la sección de retornos.');
            return;
        }

        $this->encomiendaSeleccionada = $encomienda;
        $this->selectedEncomienda = $encomiendaId;
        $this->pin_verificacion = ''; // No se requiere PIN para entrega a domicilio
        $this->fecha_entrega = Carbon::now()->format('Y-m-d\TH:i');
        $this->monto_descuento_entrega = (float) ($encomienda->monto_descuento ?? 0);
        $this->motivo_descuento_entrega = $encomienda->motivo_descuento ?? '';
        
        // Prellenar datos de facturación si existen
        if ($encomienda->facturacion) {
            $this->facturacion_id = $encomienda->facturacion->id;
            $this->type_code_facturacion = $encomienda->facturacion->type_code ?? 'DNI';
            $this->code_facturacion = $encomienda->facturacion->code ?? '';
            $this->name_facturacion = $encomienda->facturacion->name ?? '';
            $this->address_facturacion = $encomienda->facturacion->address ?? '';
            $this->ubigeo_facturacion = $encomienda->facturacion->ubigeo ?? '';
            $this->texto_ubigeo_facturacion = $encomienda->facturacion->texto_ubigeo ?? '';
            $this->phone_facturacion = $encomienda->facturacion->phone ?? '';
            $this->email_facturacion = $encomienda->facturacion->email ?? '';
        } elseif ($encomienda->customer_fact_id) {
            $customer = Customer::find($encomienda->customer_fact_id);
            if ($customer) {
                $this->facturacion_id = $customer->id;
                $this->type_code_facturacion = $customer->type_code ?? 'DNI';
                $this->code_facturacion = $customer->code ?? '';
                $this->name_facturacion = $customer->name ?? '';
                $this->address_facturacion = $customer->address ?? '';
                $this->ubigeo_facturacion = $customer->ubigeo ?? '';
                $this->texto_ubigeo_facturacion = $customer->texto_ubigeo ?? '';
                $this->phone_facturacion = $customer->phone ?? '';
                $this->email_facturacion = $customer->email ?? '';
            }
        } else {
            // Si es CONTRA ENTREGA, usar datos del destinatario
            if ($encomienda->estado_pago === 'CONTRA ENTREGA' && $encomienda->destinatario) {
                $this->type_code_facturacion = $encomienda->destinatario->type_code ?? 'DNI';
                $this->code_facturacion = $encomienda->destinatario->code ?? '';
                $this->name_facturacion = $encomienda->destinatario->name ?? '';
                $this->address_facturacion = $encomienda->destinatario->address ?? '';
                $this->ubigeo_facturacion = $encomienda->destinatario->ubigeo ?? '';
                $this->texto_ubigeo_facturacion = $encomienda->destinatario->texto_ubigeo ?? '';
                $this->phone_facturacion = $encomienda->destinatario->phone ?? '';
                $this->email_facturacion = $encomienda->destinatario->email ?? '';
            } elseif ($encomienda->estado_pago === 'ENVIO PAGADO' && $encomienda->remitente) {
                // Si es ENVIO PAGADO, usar datos del remitente
                $this->type_code_facturacion = $encomienda->remitente->type_code ?? 'DNI';
                $this->code_facturacion = $encomienda->remitente->code ?? '';
                $this->name_facturacion = $encomienda->remitente->name ?? '';
                $this->address_facturacion = $encomienda->remitente->address ?? '';
                $this->ubigeo_facturacion = $encomienda->remitente->ubigeo ?? '';
                $this->texto_ubigeo_facturacion = $encomienda->remitente->texto_ubigeo ?? '';
                $this->phone_facturacion = $encomienda->remitente->phone ?? '';
                $this->email_facturacion = $encomienda->remitente->email ?? '';
            }
        }
        
        // Prellenar tipo de comprobante, tipo de pago y método de pago
        $this->tipo_comprobante = $encomienda->tipo_comprobante ?? 'BOLETA';
        $this->tipo_pago = $encomienda->tipo_pago ?? 'CONTADO';
        $this->metodo_pago = $encomienda->metodo_pago ?? 'EFECTIVO';
        
        $this->showDeliverModal = true;
    }

    public function closeDeliverModal(): void
    {
        $this->showDeliverModal = false;
        $this->resetDeliverForm();
    }

    public function resetDeliverForm(): void
    {
        $this->reset([
            'pin_verificacion',
            'fecha_entrega',
            'selectedEncomienda',
            'encomiendaSeleccionada',
            'tipo_comprobante',
            'tipo_pago',
            'metodo_pago',
            'type_code_facturacion',
            'code_facturacion',
            'facturacion_id',
            'name_facturacion',
            'address_facturacion',
            'ubigeo_facturacion',
            'texto_ubigeo_facturacion',
            'phone_facturacion',
            'email_facturacion',
            'monto_descuento_entrega',
            'motivo_descuento_entrega',
        ]);
        $this->fecha_entrega = Carbon::now()->format('Y-m-d\TH:i');
        $this->tipo_comprobante = 'TICKET';
        $this->tipo_pago = 'CONTADO';
        $this->metodo_pago = 'EFECTIVO';
        $this->type_code_facturacion = 'DNI';
        $this->pinValido = true; // Siempre true para entrega a domicilio
        $this->monto_descuento_entrega = 0;
        $this->motivo_descuento_entrega = '';
    }

    public function searchFacturacion()
    {
        $this->validate([
            'type_code_facturacion' => 'required|string|max:255',
            'code_facturacion' => 'required|string|max:255',
        ]);
        
        $customer = Customer::where('type_code', $this->type_code_facturacion)
            ->where('code', $this->code_facturacion)
            ->first();
            
        if (!$customer) {
            // Buscar en API externa solo para DNI y RUC
            if (in_array(strtoupper($this->type_code_facturacion), ['DNI', 'RUC'])) {
                $response = $this->searchComplete($this->type_code_facturacion, $this->code_facturacion);
                if ($response['encontrado']) {
                    if (strtoupper($this->type_code_facturacion) == 'DNI') {
                        $this->name_facturacion = $response['data']->nombre ?? '';
                        $this->address_facturacion = $response['data']->direccion ?? '';
                        $this->ubigeo_facturacion = $response['data']->codigo_ubigeo ?? '';
                        $this->texto_ubigeo_facturacion = $response['texto_ubigeo'] ?? '';
                        $this->phone_facturacion = $response['data']->telefono ?? '';
                        $this->email_facturacion = $response['data']->email ?? '';
                    } else {
                        $this->name_facturacion = $response['data']->nombre_comercial ?? '';
                        $this->address_facturacion = $response['data']->direccion ?? '';
                        $this->ubigeo_facturacion = $response['data']->codigo_ubigeo ?? '';
                        $this->texto_ubigeo_facturacion = $response['texto_ubigeo'] ?? '';
                        $this->phone_facturacion = $response['data']->telefono ?? '';
                        $this->email_facturacion = $response['data']->email ?? '';
                    }
                } else {
                    $this->addError('code_facturacion', 'No se encontró el cliente. Puede ingresar los datos manualmente.');
                }
            } else {
                // Para CE y otros documentos, permitir ingreso manual
                $this->addError('code_facturacion', 'Ingrese los datos del cliente manualmente.');
            }
        } else {
            $this->facturacion_id = $customer->id;
            $this->name_facturacion = $customer->name;
            $this->address_facturacion = $customer->address;
            $this->ubigeo_facturacion = $customer->ubigeo;
            $this->texto_ubigeo_facturacion = $customer->texto_ubigeo;
            $this->phone_facturacion = $customer->phone;
            $this->email_facturacion = $customer->email;
        }
    }

    public function updatedUbigeoFacturacion($value)
    {
        if ($value) {
            $ubigeo = DB::table('ubigeo')->where('ubigeo2', $value)->first();
            if ($ubigeo) {
                $this->texto_ubigeo_facturacion = $ubigeo->texto_ubigeo ?? '';
            }
        } else {
            $this->texto_ubigeo_facturacion = '';
        }
    }

    public function updatedTipoComprobante($value)
    {
        // Si cambia a TICKET, limpiar datos de facturación
        if ($value == 'TICKET') {
            $this->reset([
                'type_code_facturacion',
                'code_facturacion',
                'facturacion_id',
                'name_facturacion',
                'address_facturacion',
                'ubigeo_facturacion',
                'texto_ubigeo_facturacion',
                'phone_facturacion',
                'email_facturacion',
            ]);
        }
    }

    public function updatedTypeCodeFacturacion($value)
    {
        // Resetear datos cuando cambia el tipo de documento
        $this->reset([
            'code_facturacion',
            'facturacion_id',
            'name_facturacion',
            'address_facturacion',
            'ubigeo_facturacion',
            'texto_ubigeo_facturacion',
            'phone_facturacion',
            'email_facturacion',
        ]);
    }

    public function updatedCodeFacturacion($value)
    {
        // Resetear datos cuando cambia el código de documento
        if (empty($value)) {
            $this->reset([
                'facturacion_id',
                'name_facturacion',
                'address_facturacion',
                'ubigeo_facturacion',
                'texto_ubigeo_facturacion',
                'phone_facturacion',
                'email_facturacion',
            ]);
        }
    }

    public function deliverEncomienda(): void
    {
        if (($this->encomiendaSeleccionada->estado_pago ?? '') === 'CONTRA ENTREGA') {
            $cajaActiva = $this->validateCajaAbierta();
            if (!$cajaActiva) {
                return;
            }
        }

        $rules = [
            'fecha_entrega' => 'required|date',
        ];

        // Validar datos de facturación si se va a crear invoice
        $necesitaInvoice = in_array($this->encomiendaSeleccionada->estado_pago ?? '', ['CONTRA ENTREGA', 'ENVIO PAGADO']) 
            && !$this->encomiendaSeleccionada->doc_factura
            && $this->tipo_comprobante != 'TICKET';
            
        $messages = [
            'fecha_entrega.required' => 'La fecha de entrega es obligatoria.',
        ];
        
        if ($this->monto_descuento_entrega > 0) {
            $rules['monto_descuento_entrega'] = 'required|numeric|min:0.01';
            $rules['motivo_descuento_entrega'] = 'required|string|max:255';
            $messages['monto_descuento_entrega.required'] = 'El monto de descuento es obligatorio.';
            $messages['monto_descuento_entrega.min'] = 'El monto de descuento debe ser mayor a 0.';
            $messages['motivo_descuento_entrega.required'] = 'El motivo del descuento es obligatorio.';
        }
            
        if ($necesitaInvoice) {
            $rules['tipo_comprobante'] = 'required|in:BOLETA,FACTURA';
            $rules['tipo_pago'] = 'required|in:CONTADO,CREDITO';
            $rules['name_facturacion'] = 'required|string|max:255';
            
            // Validación de método de pago (solo si es CONTADO)
            if ($this->tipo_pago == 'CONTADO') {
                $rules['metodo_pago'] = 'required|string';
            }
            
            // ============================================
            // VALIDACIONES ESPECÍFICAS POR TIPO DE COMPROBANTE
            // ============================================
            
            // VALIDACIONES PARA FACTURA (solo RUC)
            if ($this->tipo_comprobante == 'FACTURA') {
                $rules['type_code_facturacion'] = 'required|in:RUC';
                $rules['code_facturacion'] = [
                    'required',
                    'string',
                    'size:11', // RUC tiene 11 dígitos
                    'regex:/^(10|20)\d{9}$/' // Debe empezar con 10 o 20 seguido de 9 dígitos más
                ];
                
                $messages['type_code_facturacion.in'] = 'Para FACTURA solo se permite RUC.';
                $messages['code_facturacion.size'] = 'El RUC debe tener 11 dígitos.';
                $messages['code_facturacion.regex'] = 'El RUC debe tener 11 dígitos y comenzar con 10 o 20.';
            }
            
            // VALIDACIONES PARA BOLETA (DNI, RUC o CE)
            if ($this->tipo_comprobante == 'BOLETA') {
                $rules['type_code_facturacion'] = 'required|in:DNI,RUC,CE';
                
                // Validación de código según tipo de documento
                if ($this->type_code_facturacion == 'DNI') {
                    $rules['code_facturacion'] = 'required|string|size:8'; // DNI tiene 8 dígitos
                    $messages['code_facturacion.size'] = 'El DNI debe tener 8 dígitos.';
                } elseif ($this->type_code_facturacion == 'RUC') {
                    $rules['code_facturacion'] = 'required|string|size:11'; // RUC tiene 11 dígitos
                    $messages['code_facturacion.size'] = 'El RUC debe tener 11 dígitos.';
                } else {
                    // CE
                    $rules['code_facturacion'] = 'required|string|max:12';
                }
                
                $messages['type_code_facturacion.in'] = 'Para BOLETA se permite DNI, RUC o CE.';
            }
            
            // Mensajes comunes de facturación
            $messages['tipo_pago.required'] = 'El tipo de pago es obligatorio.';
            $messages['tipo_pago.in'] = 'El tipo de pago seleccionado no es válido. Debe ser CONTADO o CREDITO.';
            $messages['metodo_pago.required'] = 'El método de pago es obligatorio cuando el tipo de pago es CONTADO.';
            $messages['name_facturacion.required'] = 'El nombre o razón social es obligatorio.';
            $messages['type_code_facturacion.required'] = 'El tipo de documento es obligatorio.';
            $messages['code_facturacion.required'] = 'El número de documento es obligatorio.';
        } else {
            // Si necesita comprobante pero no invoice (TICKET), validar solo tipo_comprobante
            if (in_array($this->encomiendaSeleccionada->estado_pago ?? '', ['CONTRA ENTREGA', 'ENVIO PAGADO']) 
                && !$this->encomiendaSeleccionada->doc_factura) {
                $rules['tipo_comprobante'] = 'required|in:TICKET,BOLETA,FACTURA';
                $messages['tipo_comprobante.required'] = 'El tipo de comprobante es obligatorio.';
            }
        }

        $this->validate($rules, $messages);

        try {
            // Crear o actualizar cliente de facturación si no existe
            if ($necesitaInvoice && !$this->facturacion_id) {
                $customer = Customer::updateOrCreate([
                    'type_code' => $this->type_code_facturacion,
                    'code' => $this->code_facturacion,
                ], [
                    'name' => $this->name_facturacion,
                    'address' => $this->address_facturacion ?? '',
                    'ubigeo' => $this->ubigeo_facturacion ?? '',
                    'texto_ubigeo' => $this->texto_ubigeo_facturacion ?? '',
                    'phone' => $this->phone_facturacion ?? '',
                    'email' => $this->email_facturacion ?? '',
                ]);
                $this->facturacion_id = $customer->id;
            }

            // Actualizar datos de facturación en la encomienda
            if ($necesitaInvoice) {
                $this->encomiendaSeleccionada->tipo_comprobante = $this->tipo_comprobante;
                $this->encomiendaSeleccionada->tipo_pago = $this->tipo_pago;
                $this->encomiendaSeleccionada->metodo_pago = $this->metodo_pago;
                $this->encomiendaSeleccionada->customer_fact_id = $this->facturacion_id;
                $this->encomiendaSeleccionada->save();
                
                // Crear invoice usando el trait
                $invoice = $this->createInvoice($this->encomiendaSeleccionada);
                
                // Actualizar doc_factura y estado_credito
                $this->encomiendaSeleccionada->doc_factura = $invoice->id;
                $this->encomiendaSeleccionada->estado_credito = 'CANCELADO';
                $this->encomiendaSeleccionada->save();
            } elseif (in_array($this->encomiendaSeleccionada->estado_pago ?? '', ['CONTRA ENTREGA', 'ENVIO PAGADO']) 
                && !$this->encomiendaSeleccionada->doc_factura 
                && $this->tipo_comprobante == 'TICKET') {
                // Si es TICKET, solo actualizar tipo_comprobante, no crear invoice
                $this->encomiendaSeleccionada->tipo_comprobante = 'TICKET';
                $this->encomiendaSeleccionada->save();
            }
            
            $this->encomiendaSeleccionada->monto_descuento = $this->monto_descuento_entrega > 0
                ? $this->monto_descuento_entrega
                : 0;
            $this->encomiendaSeleccionada->motivo_descuento = $this->monto_descuento_entrega > 0
                ? trim($this->motivo_descuento_entrega)
                : '';
            $this->encomiendaSeleccionada->save();

            // Entrega a domicilio no requiere PIN
            $this->encomiendaService->entregarEncomienda(
                $this->selectedEncomienda,
                null, // No hay PIN para entrega a domicilio
                Carbon::parse($this->fecha_entrega)
            );

            // Remover de seleccionadas
            $this->selectedEncomiendas = array_values(array_diff($this->selectedEncomiendas, [(string)$this->selectedEncomienda]));
            
            $mostrarTicket = ($this->encomiendaSeleccionada->estado_pago ?? '') === 'ENVIO PAGADO';
            $encomiendaId = $this->selectedEncomienda;

            $this->closeDeliverModal();
            $mensaje = 'Encomienda entregada exitosamente.';
            if ($necesitaInvoice) {
                $mensaje .= ' ' . ($this->tipo_comprobante === 'BOLETA' ? 'Boleta' : 'Factura') . ' creada.';
            }
            $this->success($mensaje);

            if ($mostrarTicket) {
                $this->encomienda_id = $encomiendaId;
                $this->modalImprimirTicket = true;
            }
        } catch (\Exception $e) {
            $this->error('Error al entregar la encomienda: ' . $e->getMessage());
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
        $cajaActiva = $this->validateCajaAbierta();
        if (!$cajaActiva) {
            return;
        }

        $encomienda = Encomienda::with([
            'remitente',
            'destinatario',
            'sucursal_remitente',
            'sucursal_destinatario',
            'paquetes',
            'facturacion'
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

    private function validateCajaAbierta()
    {
        $cajaActiva = $this->cajaService->getCajaActiva(Auth::id());
        if (!$cajaActiva) {
            $this->error('Es necesario aperturar caja para esta operacion');
            return null;
        }
        return $cajaActiva;
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

