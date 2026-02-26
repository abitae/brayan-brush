<?php

namespace App\Livewire\Package;

use App\Models\Configuration\Company;
use App\Models\Package\Customer;
use App\Models\Configuration\Sucursal;
use App\Models\Facturacion\Ticket;
use App\Models\Package\Encomienda;
use App\Models\Package\RutaSucursal;
use Livewire\Component;
use App\Traits\SearchDocument;
use App\Traits\ToastTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Traits\createDocumentoTrait;
use App\Services\Caja\CajaService;

class EncomiendaCreateLive extends Component
{
    use SearchDocument, WithPagination, createDocumentoTrait, ToastTrait;
    
    protected CajaService $cajaService;
    
    // Tab inicial
    public $selectedTab = 'remitente';
    public $modalConfirmacionEncomienda = false;
    public $modalImprimirTicket = false;
    public $modalConfirmarEnvio = false;
    public $modalVerGuia = false;
    public $modalVerInvoice = false;
    public $modalVerSticker = false;
    public $modalVerDeclaracion = false;
    public $invoice_id = null;
    public $encomienda_id;
    // datos remitente
    public $type_code_remitente = 'DNI';
    public $code_remitente;
    public $name_remitente;
    public $address_remitente;
    public $ubigeo_remitente;
    public $texto_ubigeo_remitente;
    public $phone_remitente;
    public $email_remitente;

    public $remitente_id;

    // datos destinatario
    public $type_code_destinatario = 'DNI';
    public $code_destinatario;
    public $name_destinatario;
    public $address_destinatario;
    public $ubigeo_destinatario;
    public $texto_ubigeo_destinatario;
    public $phone_destinatario;
    public $email_destinatario;

    public $destinatario_id;

    // datos paquetes
    public $paquetes = [];

    public $paquete_descripcion = '';
    public $paquete_peso = 1;
    public $paquete_valor = 0;
    public $paquete_cantidad = 1;
    public $paquete_unidad = 'NIU';

    // Totales calculados
    public $total_cantidad = 0;
    public $total_peso = 0;
    public $total_valor = 0;

    // datos envio
    public $isHome = false;
    public $isReturn = false;
    public $direccion_envio;
    public $pin_seguridad;
    public $pin_seguridad_confirm;
    public $observaciones;
    public $isDocumentosTraslado = false;
    public $documentos_traslado = [];

    // Documentos de traslado
    public $documento_tipo;
    public $documento_numero;
    public $documento_ruc_emisor;

    // sucursales
    public $sucursales;
    public $sucursal_destino_filter = '';
    // rutas
    public $rutas;
    public $ruta_id;
    public $ruta;
    // facturacion
    public $tipo_comprobante = 'TICKET';
    public $tipo_pago = 'CONTADO';
    public $metodo_pago = 'EFECTIVO';
    public $estado_pago = 'ENVIO PAGADO';

    // datos de facturacion
    public $type_code_facturacion = 'DNI';
    public $code_facturacion;
    public $facturacion_id;
    public $name_facturacion;
    public $address_facturacion;
    public $ubigeo_facturacion;
    public $texto_ubigeo_facturacion;
    public $phone_facturacion;
    public $email_facturacion;

    public $encomienda;
    public $ticket;
    public $unidades;
    public $ubigeos;
    public $userSucursal;
    // busqueda de encomiendas
    public $searchEncomienda = '';
    public $fecha_creacion_filter;
    
    // Control de caja abierta
    public $tieneCajaAbierta = false;
    
    /**
     * Inicializa el servicio de caja mediante inyección de dependencias
     * 
     * @param CajaService $cajaService Servicio para gestionar operaciones de caja
     * @return void
     */
    public function boot(CajaService $cajaService)
    {
        $this->cajaService = $cajaService;
    }
    
    /**
     * Método de inicialización del componente Livewire
     * Carga datos iniciales: verifica caja abierta, unidades de medida, ubigeos y rutas disponibles
     * 
     * @return void
     */
    public function mount()
    {
        // Verificar que el usuario tenga una caja abierta
        try {
            $cajaActiva = $this->cajaService->getCajaActiva(Auth::id());
            
            if (!$cajaActiva) {
                $this->tieneCajaAbierta = false;
            } else {
                $this->tieneCajaAbierta = true;
            }
        } catch (\Exception $e) {
            $this->tieneCajaAbierta = false;
        }
        
        // Cargar sucursal del usuario
        $this->userSucursal = Auth::user()->sucursal;
        $this->sucursales = Sucursal::orderBy('name')->get();
        
        // lista e unidades de medida
        $this->unidades = DB::table('sunat_03')
            ->select('codigo', 'descripcion')
            ->orderBy('descripcion')
            ->get();
        // lista de ubigeos
        $this->ubigeos = DB::table('ubigeo')
            ->select('ubigeo2', 'texto_ubigeo')
            ->orderBy('texto_ubigeo')
            ->get();
        // Mostrar las sucursales que no sean la del usuario logueado segun las rutas activas
        $this->rutas = RutaSucursal::where('sucursal_origen_id', Auth::user()->sucursal->id)
            ->where('isActive', true)
            ->where('estado_ruta', 'ACTIVA')
            ->where('fecha_salida', '>=', Carbon::now()->format('Y-m-d'))
            ->orderBy('fecha_salida', 'asc')
            ->get();

        if ($this->rutas->isEmpty()) {
            $this->error('No hay rutas disponibles configure una ruta');
            $this->ruta_id = null;
        }
        else{
            $this->ruta_id = $this->rutas->first()->id;
        }
        $this->fecha_creacion_filter = Carbon::today()->format('Y-m-d');
    }
    /**
     * Renderiza la vista del componente con las encomiendas filtradas
     * Permite búsqueda por código, remitente o destinatario y filtrado por fecha
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $encomiendas = Encomienda::where('sucursal_id', Auth::user()->sucursal->id)
            ->where('estado_encomienda', 'REGISTRADO')
            ->when($this->sucursal_destino_filter, function ($query) {
                $query->where('sucursal_dest_id', $this->sucursal_destino_filter);
            })
            ->where(function ($query) {
                $query->where('code', 'like', '%' . $this->searchEncomienda . '%')
                    ->orWhereHas('remitente', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchEncomienda . '%');
                    })
                    ->orWhereHas('destinatario', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchEncomienda . '%');
                    });
            })
            ->when($this->fecha_creacion_filter, function ($query) {
                // Permite filtrar por rango de fechas si el filtro es un array con 'from' y 'to'
                if (is_array($this->fecha_creacion_filter) && isset($this->fecha_creacion_filter['from'], $this->fecha_creacion_filter['to'])) {
                    $query->whereBetween('fecha_creacion', [
                        $this->fecha_creacion_filter['from'] . ' 00:00:00',
                        $this->fecha_creacion_filter['to'] . ' 23:59:59'
                    ]);
                } elseif (!empty($this->fecha_creacion_filter)) {
                    // Si es solo una fecha, filtra por ese día
                    $query->whereDate('fecha_creacion', $this->fecha_creacion_filter);
                }
            })
            ->with(['remitente', 'destinatario', 'invoice'])
            ->latest()
            ->paginate(10);
        return view('livewire.package.encomienda-create-live', compact('encomiendas'));
    }

    public function updatedSucursalDestinoFilter()
    {
        $this->resetPage();
    }
    /**
     * Busca un cliente remitente por tipo y número de documento
     * Valida los campos y busca en BD o API externa
     * 
     * @return void
     */
    public function searchRemitente()
    {
        $this->validate([
            'type_code_remitente' => 'required|string|max:255',
            'code_remitente' => 'required|string|max:255',
        ], [
            'type_code_remitente.required' => 'El tipo de documento es obligatorio.',
            'type_code_remitente.max' => 'El tipo de documento no debe exceder 255 caracteres.',
            'code_remitente.required' => 'El número de documento es obligatorio.',
            'code_remitente.max' => 'El número de documento no debe exceder 255 caracteres.',
        ]);
        
        $this->searchCustomer(
            $this->type_code_remitente,
            $this->code_remitente,
            'remitente'
        );
    }

    /**
     * Busca un cliente destinatario por tipo y número de documento
     * Valida los campos y busca en BD o API externa
     * 
     * @return void
     */
    public function searchDestinatario()
    {
        $this->validate([
            'type_code_destinatario' => 'required|string|max:255',
            'code_destinatario' => 'required|string|max:255',
        ], [
            'type_code_destinatario.required' => 'El tipo de documento es obligatorio.',
            'type_code_destinatario.max' => 'El tipo de documento no debe exceder 255 caracteres.',
            'code_destinatario.required' => 'El número de documento es obligatorio.',
            'code_destinatario.max' => 'El número de documento no debe exceder 255 caracteres.',
        ]);
        
        $this->searchCustomer(
            $this->type_code_destinatario,
            $this->code_destinatario,
            'destinatario'
        );
    }
    
    /**
     * Busca un cliente para facturación por tipo y número de documento
     * Valida según el tipo de comprobante (FACTURA requiere RUC, BOLETA permite DNI/RUC/CE)
     * 
     * @return void
     */
    public function searchFacturacion()
    {
        $rules = $this->getFacturacionValidationRules();
        $this->validate($rules['rules'], $rules['messages']);
        
        $this->searchCustomer(
            $this->type_code_facturacion,
            $this->code_facturacion,
            'facturacion',
            true
        );
    }
    
    /**
     * Método genérico para buscar cliente en BD o API externa
     * Primero busca en la base de datos local, si no existe busca en la API
     * Solo busca en API para documentos DNI y RUC
     * 
     * @param string $typeCode Tipo de documento (DNI, RUC, CE, etc.)
     * @param string $code Número de documento
     * @param string $tipo Tipo de búsqueda: 'remitente', 'destinatario' o 'facturacion'
     * @param bool $isFacturacion Indica si es búsqueda para facturación
     * @return void
     */
    private function searchCustomer(string $typeCode, string $code, string $tipo, bool $isFacturacion = false)
    {
        $customer = Customer::where('type_code', $typeCode)
            ->where('code', $code)
            ->first();
            
        if ($customer) {
            $this->loadCustomerData($customer, $tipo);
            return;
        }
        
        // Buscar en API externa solo para DNI y RUC
        if (!in_array(strtoupper($typeCode), ['DNI', 'RUC'])) {
            $errorField = $isFacturacion ? 'code_facturacion' : "code_{$tipo}";
            $this->addError($errorField, 'Ingrese los datos del cliente manualmente.');
            return;
        }
        
        $response = $this->searchComplete($typeCode, $code);
        
        if ($response['encontrado']) {
            $this->loadCustomerDataFromApi($response, $typeCode, $tipo);
        } else {
            $errorField = $isFacturacion ? 'code_facturacion' : "code_{$tipo}";
            $message = $isFacturacion 
                ? 'No se encontró el cliente. Puede ingresar los datos manualmente.'
                : 'No se encontró el cliente';
            $this->addError($errorField, $message);
        }
    }
    
    /**
     * Carga los datos del cliente desde la base de datos a las propiedades del componente
     * Asigna nombre, dirección, ubigeo, teléfono y email según el tipo (remitente/destinatario/facturacion)
     * 
     * @param Customer $customer Cliente encontrado en la BD
     * @param string $tipo Tipo de cliente: 'remitente', 'destinatario' o 'facturacion'
     * @return void
     */
    private function loadCustomerData(Customer $customer, string $tipo)
    {
        $prefix = $tipo === 'remitente' ? 'remitente' : ($tipo === 'destinatario' ? 'destinatario' : 'facturacion');
        
        $this->{"{$prefix}_id"} = $customer->id;
        $this->{"name_{$prefix}"} = $customer->name;
        $this->{"address_{$prefix}"} = $customer->address;
        $this->{"ubigeo_{$prefix}"} = $customer->ubigeo;
        $this->{"texto_ubigeo_{$prefix}"} = $customer->texto_ubigeo;
        $this->{"phone_{$prefix}"} = $customer->phone;
        $this->{"email_{$prefix}"} = $customer->email;
    }
    
    /**
     * Carga los datos del cliente desde la respuesta de la API externa
     * Extrae nombre (o nombre comercial para RUC), dirección, ubigeo, teléfono y email
     * 
     * @param array $response Respuesta de la API con los datos del cliente
     * @param string $typeCode Tipo de documento (DNI o RUC)
     * @param string $tipo Tipo de cliente: 'remitente', 'destinatario' o 'facturacion'
     * @return void
     */
    private function loadCustomerDataFromApi(array $response, string $typeCode, string $tipo)
    {
        $prefix = $tipo === 'remitente' ? 'remitente' : ($tipo === 'destinatario' ? 'destinatario' : 'facturacion');
        $data = $response['data'];
        
        $this->{"name_{$prefix}"} = $typeCode === 'DNI' 
            ? ($data->nombre ?? '')
            : ($data->nombre_comercial ?? '');
        $this->{"address_{$prefix}"} = $data->direccion ?? '';
        $this->{"ubigeo_{$prefix}"} = $data->codigo_ubigeo ?? '';
        $this->{"texto_ubigeo_{$prefix}"} = $response['texto_ubigeo'] ?? '';
        $this->{"phone_{$prefix}"} = $data->telefono ?? '';
        $this->{"email_{$prefix}"} = $data->email ?? '';
    }
    
    /**
     * Obtiene las reglas de validación para facturación según el tipo de comprobante
     * FACTURA: solo permite RUC de 11 dígitos que inicie con 10 o 20
     * BOLETA: permite DNI (8 dígitos), RUC (11 dígitos) o CE (hasta 12 caracteres)
     * 
     * @return array Array con 'rules' y 'messages' para la validación
     */
    private function getFacturacionValidationRules(): array
    {
        $rules = [
            'type_code_facturacion' => 'required|string|max:255',
            'code_facturacion' => 'required|string|max:255',
        ];
        
        $messages = [
            'type_code_facturacion.required' => 'El tipo de documento es obligatorio.',
            'code_facturacion.required' => 'El número de documento es obligatorio.',
        ];
        
        if ($this->tipo_comprobante == 'FACTURA') {
            $rules['type_code_facturacion'] = 'required|in:RUC';
            $rules['code_facturacion'] = [
                'required',
                'string',
                'size:11',
                'regex:/^(10|20)\d{9}$/'
            ];
            $messages['type_code_facturacion.in'] = 'Para FACTURA solo se permite RUC.';
            $messages['code_facturacion.size'] = 'El RUC debe tener 11 dígitos.';
            $messages['code_facturacion.regex'] = 'El RUC debe tener 11 dígitos y comenzar con 10 o 20.';
        } elseif ($this->tipo_comprobante == 'BOLETA') {
            $rules['type_code_facturacion'] = 'required|in:DNI,RUC,CE';
            if ($this->type_code_facturacion == 'DNI') {
                $rules['code_facturacion'] = 'required|string|size:8';
                $messages['code_facturacion.size'] = 'El DNI debe tener 8 dígitos.';
            } elseif ($this->type_code_facturacion == 'RUC') {
                $rules['code_facturacion'] = 'required|string|size:11';
                $messages['code_facturacion.size'] = 'El RUC debe tener 11 dígitos.';
            } else {
                $rules['code_facturacion'] = 'required|string|max:12';
            }
            $messages['type_code_facturacion.in'] = 'Para BOLETA se permite DNI, RUC o CE.';
        }
        
        return ['rules' => $rules, 'messages' => $messages];
    }
    /**
     * Valida los campos del tab actual y avanza al siguiente tab si la validación es exitosa
     * Distribuye la validación según el tab seleccionado
     * 
     * @return void
     */
    public function validateTabs()
    {
        switch ($this->selectedTab) {
            case 'remitente':

                $this->validateRemitenteTab();
                break;
            case 'destinatario':
                $this->validateDestinatarioTab();
                break;
            case 'paquetes':
                $this->validatePaquetesTab();
                break;
            case 'envio':
                $this->validateEnvioTab();
                break;
            case 'facturacion':
                $this->validateFacturacionTab();
                break;
            default:
                dd('finalizar');
                break;
        }
    }
    
    /**
     * Valida y procesa el tab de remitente
     */
    private function validateRemitenteTab()
    {
        // Validar que el código no sea null o vacío
        if (empty($this->code_remitente)) {
            $this->error('El número de documento es requerido');
            return;
        }
        
        $tipe_validate = $this->getDocumentValidationRule($this->type_code_remitente, $this->code_remitente);
        
        if ($tipe_validate === false) {
            return; // Error ya mostrado en getDocumentValidationRule
        }

        $this->validate([
            'type_code_remitente' => 'required|string|max:255',
            'code_remitente' => 'required|string|max:11',
            'name_remitente' => 'required|string|max:255',
            'address_remitente' => $tipe_validate . '|string',
            'ubigeo_remitente' => $tipe_validate . '|string|max:255',
            'texto_ubigeo_remitente' => $tipe_validate . '|string|max:255',
            'phone_remitente' => 'nullable|string|max:255',
            'email_remitente' => 'nullable|string|email',
        ], [
            'type_code_remitente.required' => 'El tipo de documento es obligatorio.',
            'type_code_remitente.max' => 'El tipo de documento no debe exceder 255 caracteres.',
            'code_remitente.required' => 'El número de documento es obligatorio.',
            'code_remitente.max' => 'El número de documento debe tener como máximo 11 caracteres.',
            'name_remitente.required' => 'El nombre del remitente es obligatorio.',
            'name_remitente.max' => 'El nombre del remitente no debe exceder 255 caracteres.',
            'address_remitente.required' => 'La dirección del remitente es obligatoria.',
            'ubigeo_remitente.required' => 'El ubigeo del remitente es obligatorio.',
            'ubigeo_remitente.max' => 'El ubigeo del remitente no debe exceder 255 caracteres.',
            'texto_ubigeo_remitente.required' => 'El ubigeo del remitente es obligatorio.',
            'texto_ubigeo_remitente.max' => 'El ubigeo del remitente no debe exceder 255 caracteres.',
            'phone_remitente.max' => 'El teléfono del remitente no debe exceder 255 caracteres.',
            'email_remitente.email' => 'El correo del remitente no tiene un formato válido.',
        ]);
        
        $this->saveOrUpdateCustomer('remitente');
        $this->selectedTab = 'destinatario';
    }
    
    /**
     * Valida y procesa el tab de destinatario
     */
    private function validateDestinatarioTab()
    {
        $tipe_validate = $this->type_code_destinatario == 'RUC' && strpos($this->code_destinatario, '20') === 0
            ? 'required'
            : 'nullable';
            
        $this->validate([
            'type_code_destinatario' => 'required|string|max:255',
            'code_destinatario' => 'required|string|max:255',
            'name_destinatario' => 'required|string|max:255',
            'address_destinatario' => $tipe_validate . '|string',
            'ubigeo_destinatario' => $tipe_validate . '|string|max:255',
            'texto_ubigeo_destinatario' => $tipe_validate . '|string|max:255',
            'phone_destinatario' => 'nullable|string|max:255',
            'email_destinatario' => 'nullable|string|email',
        ], [
            'type_code_destinatario.required' => 'El tipo de documento es obligatorio.',
            'type_code_destinatario.max' => 'El tipo de documento no debe exceder 255 caracteres.',
            'code_destinatario.required' => 'El número de documento es obligatorio.',
            'code_destinatario.max' => 'El número de documento no debe exceder 255 caracteres.',
            'name_destinatario.required' => 'El nombre del destinatario es obligatorio.',
            'name_destinatario.max' => 'El nombre del destinatario no debe exceder 255 caracteres.',
            'address_destinatario.required' => 'La dirección del destinatario es obligatoria.',
            'ubigeo_destinatario.required' => 'El ubigeo del destinatario es obligatorio.',
            'ubigeo_destinatario.max' => 'El ubigeo del destinatario no debe exceder 255 caracteres.',
            'texto_ubigeo_destinatario.required' => 'El ubigeo del destinatario es obligatorio.',
            'texto_ubigeo_destinatario.max' => 'El ubigeo del destinatario no debe exceder 255 caracteres.',
            'phone_destinatario.max' => 'El teléfono del destinatario no debe exceder 255 caracteres.',
            'email_destinatario.email' => 'El correo del destinatario no tiene un formato válido.',
        ]);
        
        $this->saveOrUpdateCustomer('destinatario');
        $this->selectedTab = 'paquetes';
    }
    
    /**
     * Valida y procesa el tab de paquetes
     */
    private function validatePaquetesTab()
    {
        $this->validate([
            'paquetes' => 'required|array',
        ], [
            'paquetes.required' => 'Debe agregar al menos un paquete.',
            'paquetes.array' => 'Los paquetes deben ser un arreglo válido.',
        ]);
        
        $this->direccion_envio = $this->address_destinatario;
        $this->selectedTab = 'envio';
    }
    
    /**
     * Valida y procesa el tab de envío
     */
    private function validateEnvioTab()
    {
        $validationRules = $this->getEnvioValidationRules();
        
        $this->validate($validationRules, [
            'ruta_id.required' => 'La ruta es obligatoria.',
            'ruta_id.integer' => 'La ruta seleccionada no es válida.',
            'pin_seguridad.required' => 'El PIN de seguridad es obligatorio.',
            'pin_seguridad.max' => 'El PIN de seguridad no debe exceder 3 caracteres.',
            'pin_seguridad.same' => 'El PIN de seguridad no coincide con la confirmación.',
            'pin_seguridad_confirm.required' => 'La confirmación del PIN es obligatoria.',
            'pin_seguridad_confirm.max' => 'La confirmación del PIN no debe exceder 3 caracteres.',
            'observaciones.max' => 'Las observaciones no deben exceder 255 caracteres.',
            'direccion_envio.required' => 'La dirección de envío es obligatoria.',
            'direccion_envio.max' => 'La dirección de envío no debe exceder 255 caracteres.',
        ]);
        $this->selectedTab = 'facturacion';
    }
    
    /**
     * Valida y procesa el tab de facturación
     */
    private function validateFacturacionTab()
    {
        if ($this->estado_pago == 'CONTRA ENTREGA') {
            $this->facturacion_id = $this->destinatario_id;
        } else {
            $this->facturacion_id = $this->remitente_id;
        }
        if ($this->tipo_comprobante == 'FACTURA') {
            if ($this->type_code_facturacion != 'RUC') {
                $this->addError('type_code_facturacion', 'El tipo de documento debe ser RUC');
                return;
            }
            if (strlen($this->code_facturacion) != 11) {
                $this->addError('code_facturacion', 'El número de RUC debe tener 11 dígitos');
                return;
            }
            if (strpos($this->code_facturacion, '10') !== 0 && strpos($this->code_facturacion, '20') !== 0) {
                $this->addError('code_facturacion', 'El número de RUC debe iniciar con 10 o 20');
                return;
            }
            if (empty($this->name_facturacion)) {
                $this->addError('name_facturacion', 'El nombre o razón social es requerido');
                return;
            }
        }
        $this->validate([
            'remitente_id' => 'required|integer',
            'destinatario_id' => 'required|integer',
            'facturacion_id' => 'required|integer',
            'tipo_comprobante' => 'required|string',
            'tipo_pago' => 'required|string',
            'metodo_pago' => 'required|string',
            'estado_pago' => 'required|string',
        ], [
            'remitente_id.required' => 'El remitente es obligatorio.',
            'remitente_id.integer' => 'El remitente seleccionado no es válido.',
            'destinatario_id.required' => 'El destinatario es obligatorio.',
            'destinatario_id.integer' => 'El destinatario seleccionado no es válido.',
            'facturacion_id.required' => 'El cliente de facturación es obligatorio.',
            'facturacion_id.integer' => 'El cliente de facturación seleccionado no es válido.',
            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio.',
            'tipo_pago.required' => 'El tipo de pago es obligatorio.',
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'estado_pago.required' => 'El estado de pago es obligatorio.',
        ]);
        $this->confirmarEnvio();
    }
    
    /**
     * Obtiene la regla de validación según el tipo de documento
     * RUC: debe iniciar con 10 o 20 y tener 11 dígitos (20 requiere dirección obligatoria)
     * DNI: debe tener 8 dígitos
     * CE/OTRO: sin restricciones de formato
     * 
     * @param string $typeCode Tipo de documento (RUC, DNI, CE, OTRO)
     * @param string|null $code Número de documento
     * @return string|bool Retorna 'required' o 'nullable' si es válido, false si hay error
     */
    private function getDocumentValidationRule(string $typeCode, ?string $code): string|bool
    {
        // Validar que el código no sea null o vacío
        if (empty($code)) {
            $this->error('El número de documento es requerido');
            return false;
        }
        
        $tipo = strtoupper($typeCode);

        if ($tipo === 'RUC') {
            if ((strpos($code, '10') === 0 || strpos($code, '20') === 0) && strlen($code) == 11) {
                return (strpos($code, '20') === 0) ? 'required' : 'nullable';
            }
            $this->error('El número de RUC debe iniciar con 10 o 20 y tener 11 dígitos.');
            return false;
        }
        
        if ($tipo === 'DNI') {
            if (strlen($code) == 8) {
                return 'nullable';
            }
            $this->error('El número de DNI debe tener 8 dígitos.');
            return false;
        }
        
        if ($tipo === 'CE' || $tipo === 'OTRO') {
            return 'nullable';
        }
        
        $this->error('El tipo de documento no es válido');
        return false;
    }
    
    /**
     * Guarda o actualiza un cliente en la base de datos
     * Si el cliente ya existe (tiene ID), no hace nada
     * Si no existe, lo crea o actualiza según el tipo y código de documento
     * 
     * @param string $tipo Tipo de cliente: 'remitente' o 'destinatario'
     * @return void
     */
    private function saveOrUpdateCustomer(string $tipo)
    {
        $prefix = $tipo === 'remitente' ? 'remitente' : 'destinatario';
        $idProperty = "{$prefix}_id";
        
        $customer = Customer::updateOrCreate([
            'type_code' => $this->{"type_code_{$prefix}"},
            'code' => $this->{"code_{$prefix}"},
        ], [
            'name' => $this->{"name_{$prefix}"},
            'address' => $this->{"address_{$prefix}"} ?? '',
            'ubigeo' => $this->{"ubigeo_{$prefix}"} ?? '',
            'texto_ubigeo' => $this->{"texto_ubigeo_{$prefix}"} ?? '',
            'phone' => $this->{"phone_{$prefix}"} ?? '',
            'email' => $this->{"email_{$prefix}"} ?? '',
        ]);
        
        $this->$idProperty = $customer->id;
    }
    
    /**
     * Obtiene las reglas de validación para el tab de envío
     * Si es entrega a domicilio (isHome): dirección requerida, PIN opcional
     * Si no es domicilio: PIN requerido con confirmación, dirección opcional
     * 
     * @return array Reglas de validación para el formulario de envío
     */
    private function getEnvioValidationRules(): array
    {
        if ($this->isHome) {
            return [
                'ruta_id' => 'required|integer',
                'pin_seguridad' => 'nullable|string|max:3',
                'pin_seguridad_confirm' => 'nullable|string|max:3',
                'observaciones' => 'nullable|string|max:255',
                'direccion_envio' => 'required|string|max:255',
            ];
        }
        
        return [
            'ruta_id' => 'required|integer',
            'pin_seguridad' => 'required|string|max:3|same:pin_seguridad_confirm',
            'pin_seguridad_confirm' => 'required|string|max:3',
            'observaciones' => 'nullable|string|max:255',
            'direccion_envio' => 'nullable|string|max:255',
        ];
    }
    /**
     * Hook de Livewire que se ejecuta cuando cambia el estado de pago
     * Si es CONTRA ENTREGA: facturación al destinatario, TICKET, CREDITO, OTRO
     * Si es ENVIO PAGADO: facturación al remitente, TICKET, CONTADO, EFECTIVO
     * 
     * @param string $value Nuevo valor del estado de pago
     * @return void
     */
    public function updatedEstadoPago($value)
    {
        if ($value == 'CONTRA ENTREGA') {
            $this->facturacion_id = $this->destinatario_id;
            $this->tipo_comprobante = 'TICKET';
            $this->tipo_pago = 'CREDITO';
            $this->metodo_pago = 'OTRO';
            
        } else {
            $this->facturacion_id = $this->remitente_id;
            $this->tipo_comprobante = 'TICKET';
            $this->tipo_pago = 'CONTADO';
            $this->metodo_pago = 'EFECTIVO';
            
        }
    }
    /**
     * Hook de Livewire que se ejecuta cuando cambia el tipo de comprobante
     * FACTURA: establece RUC como tipo de documento
     * BOLETA: establece DNI como tipo de documento por defecto
     * TICKET: usa el remitente como cliente de facturación
     * 
     * @param string $value Nuevo tipo de comprobante (FACTURA, BOLETA, TICKET)
     * @return void
     */
    public function updatedTipoComprobante($value)
    {
        $customer = Customer::find($this->remitente_id);
        
        switch ($value) {
            case 'FACTURA':
                $this->handleFacturaType($customer);
                break;
            case 'BOLETA':
                $this->handleBoletaType($customer);
                break;
            case 'TICKET':
                $this->handleTicketType($customer);
                break;
        }
    }
    
    /**
     * Maneja el cambio a tipo FACTURA
     * Establece el tipo de documento a RUC y carga los datos del cliente si existe
     * 
     * @param Customer|null $customer Cliente remitente (puede ser null)
     * @return void
     */
    private function handleFacturaType(?Customer $customer): void
    {
        $this->type_code_facturacion = 'RUC';
        if ($customer) {
            $this->loadFacturacionDataFromCustomer($customer);
        }
    }
    
    /**
     * Maneja el cambio a tipo BOLETA
     * Establece el tipo de documento a DNI por defecto y carga los datos del cliente si existe
     * 
     * @param Customer|null $customer Cliente remitente (puede ser null)
     * @return void
     */
    private function handleBoletaType(?Customer $customer): void
    {
        $this->type_code_facturacion = 'DNI';
        if ($customer) {
            $this->loadFacturacionDataFromCustomer($customer);
        }
    }
    
    /**
     * Maneja el cambio a tipo TICKET
     * Establece el cliente de facturación como el remitente
     * 
     * @param Customer|null $customer Cliente remitente (puede ser null)
     * @return void
     */
    private function handleTicketType(?Customer $customer): void
    {
        if ($customer) {
            $this->facturacion_id = $customer->id;
        }
    }
    
    /**
     * Carga los datos de facturación desde un cliente
     * Asigna todos los campos de facturación (ID, código, nombre, dirección, etc.) desde el cliente
     * 
     * @param Customer $customer Cliente del cual se cargarán los datos
     * @return void
     */
    private function loadFacturacionDataFromCustomer(Customer $customer): void
    {
        $this->facturacion_id = $customer->id;
        $this->code_facturacion = $customer->code;
        $this->name_facturacion = $customer->name;
        $this->address_facturacion = $customer->address;
        $this->ubigeo_facturacion = $customer->ubigeo;
        $this->texto_ubigeo_facturacion = $customer->texto_ubigeo;
        $this->phone_facturacion = $customer->phone;
        $this->email_facturacion = $customer->email;
    }
    /**
     * Hook de Livewire que se ejecuta cuando cambia el valor de isHome
     * Limpia los campos relacionados cuando se desactiva
     * 
     * @param bool $value Nuevo valor de isHome
     * @return void
     */
    public function updatedIsHome($value)
    {
        if (!$value && $this->isReturn) {
            // Si se desactiva isHome pero isReturn está activo, no permitir desactivar
            $this->isHome = true;
            return;
        }
        
        if (!$value) {
            // Limpiar dirección de envío si se desactiva
            $this->direccion_envio = null;
        }
    }
    
    /**
     * Hook de Livewire que se ejecuta cuando cambia el valor de isReturn
     * Si es encomienda de retorno, automáticamente activa la entrega a domicilio
     * 
     * @param bool $value Nuevo valor de isReturn
     * @return void
     */
    public function updatedIsReturn($value)
    {
        if ($value) {
            $this->isHome = true;
        }
    }
    
    /**
     * Hook de Livewire que se ejecuta cuando cambia el valor de isDocumentosTraslado
     * Limpia los documentos cuando se desactiva
     * 
     * @param bool $value Nuevo valor de isDocumentosTraslado
     * @return void
     */
    public function updatedIsDocumentosTraslado($value)
    {
        if (!$value) {
            // Limpiar documentos de traslado si se desactiva
            $this->documentos_traslado = [];
            $this->documento_tipo = null;
            $this->documento_numero = null;
            $this->documento_ruc_emisor = null;
        }
    }
    
    /**
     * Agrega un nuevo paquete al array de paquetes en memoria
     * Valida los campos, calcula subtotal y peso total, y recalcula los totales generales
     * 
     * @return void
     */
    public function addPaquete()
    {
        $rules = [
            'paquete_descripcion' => 'required|string|max:255',
            'paquete_peso' => 'required|numeric|min:0.01',
            'paquete_valor' => 'required|numeric|min:0.01',
            'paquete_cantidad' => 'required|integer|min:1',
            'paquete_unidad' => 'required|string|max:10',
        ];
        $messages = [
            'paquete_descripcion.required' => 'La descripción del paquete es requerida',
            'paquete_peso.required' => 'El peso del paquete es requerido',
            'paquete_peso.min' => 'El peso del paquete debe ser mayor a 0.01',
            'paquete_valor.required' => 'El valor del paquete es requerido',
            'paquete_valor.min' => 'El valor del paquete debe ser mayor a 0.01',
            'paquete_cantidad.required' => 'La cantidad de paquetes es requerida',
            'paquete_unidad.required' => 'La unidad de medida es requerida',
        ];
        $this->validate($rules, $messages);

        $paquete = [
            'descripcion' => trim($this->paquete_descripcion),
            'peso' => round($this->paquete_peso, 2),
            'valor' => round($this->paquete_valor, 2),
            'cantidad' => (int)$this->paquete_cantidad,
            'unidad' => $this->paquete_unidad,
            'subtotal' => round($this->paquete_valor * $this->paquete_cantidad, 2),
            'peso_total' => round($this->paquete_peso * $this->paquete_cantidad, 2),
        ];

        $this->paquetes[] = $paquete;
        $this->calcularTotales();
        $this->resetPaqueteForm();
    }

    /**
     * Resetea el formulario de paquete a sus valores por defecto
     * Limpia descripción, restablece peso a 1, valor a 0, cantidad a 1 y unidad a 'NIU'
     * 
     * @return void
     */
    private function resetPaqueteForm()
    {
        $this->paquete_descripcion = '';
        $this->paquete_peso = 1;
        $this->paquete_valor = 0;
        $this->paquete_cantidad = 1;
        $this->paquete_unidad = 'NIU';
    }

    /**
     * Calcula los totales de cantidad, peso y valor de todos los paquetes
     * Actualiza las propiedades total_cantidad, total_peso y total_valor
     * 
     * @return void
     */
    private function calcularTotales()
    {
        $this->total_cantidad = 0;
        $this->total_peso = 0;
        $this->total_valor = 0;

        foreach ($this->paquetes as $paquete) {
            $this->total_cantidad += $paquete['cantidad'];
            $this->total_peso += $paquete['peso_total'];
            $this->total_valor += $paquete['subtotal'];
        }

        $this->total_peso = round($this->total_peso, 2);
        $this->total_valor = round($this->total_valor, 2);
    }

    /**
     * Hook de Livewire que se ejecuta cuando cambia la cantidad del paquete
     * Asegura que la cantidad sea al menos 1
     * 
     * @return void
     */
    public function updatedPaqueteCantidad()
    {
        $this->paquete_cantidad = max(1, (int)$this->paquete_cantidad);
    }

    /**
     * Hook de Livewire que se ejecuta cuando cambia el peso del paquete
     * Asegura que el peso sea al menos 0.01
     * 
     * @return void
     */
    public function updatedPaquetePeso()
    {
        $this->paquete_peso = max(0.01, (float)$this->paquete_peso);
    }

    /**
     * Hook de Livewire que se ejecuta cuando cambia el valor del paquete
     * Asegura que el valor sea al menos 0.01
     * 
     * @return void
     */
    public function updatedPaqueteValor()
    {
        $this->paquete_valor = max(0.01, (float)$this->paquete_valor);
    }

    /**
     * Valida que haya al menos un paquete agregado a la encomienda
     * 
     * @return bool Retorna true si hay paquetes, false si está vacío
     */
    public function validarPaquetes()
    {
        if (empty($this->paquetes)) {
            $this->addError('paquetes', 'Debe agregar al menos un paquete a la encomienda.');
            return false;
        }
        return true;
    }



    /**
     * Elimina un paquete del array de paquetes por su índice
     * Reindexa el array y recalcula los totales
     * Emite un evento Livewire para notificar la eliminación
     * 
     * @param int $index Índice del paquete a eliminar
     * @return void
     */
    public function removePaquete($index)
    {
        if (isset($this->paquetes[$index])) {
            $paqueteEliminado = $this->paquetes[$index];
            unset($this->paquetes[$index]);
            $this->paquetes = array_values($this->paquetes); // Reindexar el array

            $this->calcularTotales();

            $this->info("Paquete '{$paqueteEliminado['descripcion']}' eliminado correctamente");
        }
    }

    /**
     * Limpia todos los paquetes del array y resetea el formulario
     * Recalcula los totales (quedan en 0) y emite un evento Livewire
     * 
     * @return void
     */
    public function limpiarPaquetes()
    {
        $this->paquetes = [];
        $this->calcularTotales();
        $this->resetPaqueteForm();

        $this->warning('Todos los paquetes han sido eliminados');
    }

    /**
     * Agrega un documento de traslado al array de documentos
     * Valida tipo, número y RUC del emisor, luego limpia el formulario
     * 
     * @return void
     */
    public function addDocumentoTraslado()
    {
        $this->validate([
            'documento_tipo' => 'required|string|max:50',
            'documento_numero' => 'required|string|max:50',
            'documento_ruc_emisor' => 'required|string|max:11',
        ], [
            'documento_tipo.required' => 'El tipo de documento es obligatorio.',
            'documento_tipo.max' => 'El tipo de documento no debe exceder 50 caracteres.',
            'documento_numero.required' => 'El número de documento es obligatorio.',
            'documento_numero.max' => 'El número de documento no debe exceder 50 caracteres.',
            'documento_ruc_emisor.required' => 'El RUC del emisor es obligatorio.',
            'documento_ruc_emisor.max' => 'El RUC del emisor no debe exceder 11 caracteres.',
        ]);

        $this->documentos_traslado[] = [
            'tipo' => $this->documento_tipo,
            'numero' => $this->documento_numero,
            'ruc_emisor' => $this->documento_ruc_emisor,
        ];

        // Limpiar los campos del formulario de documento
        $this->documento_tipo = null;
        $this->documento_numero = null;
        $this->documento_ruc_emisor = null;
    }

    /**
     * Elimina un documento de traslado del array por su índice
     * Reindexa el array después de la eliminación
     * 
     * @param int $index Índice del documento a eliminar
     * @return void
     */
    public function removeDocumentoTraslado($index)
    {
        if (isset($this->documentos_traslado[$index])) {
            unset($this->documentos_traslado[$index]);
            $this->documentos_traslado = array_values($this->documentos_traslado); // Reindexar el array
        }
    }
    // funcion para guardar la encomienda
    public function leftTabs()
    {
        switch ($this->selectedTab) {
            case 'remitente':
                break;
            case 'destinatario':
                $this->selectedTab = 'remitente';
                break;
            case 'paquetes':
                $this->selectedTab = 'destinatario';
                break;
            case 'envio':
                $this->selectedTab = 'paquetes';
                break;
            case 'facturacion':
                $this->selectedTab = 'envio';
                break;
        }
    }
    /**
     * Confirma y crea la encomienda en el sistema
     * Valida caja abierta, datos requeridos, crea encomienda, paquetes, documentos y registra en caja
     * 
     * @return void
     */
    public function confirmacionEncomienda()
    {
        
        $cajaActiva = $this->validateCajaAbierta();
        if (!$cajaActiva) {
            return;
        }

        $this->validateEncomiendaData();
        $ruta = RutaSucursal::findOrFail($this->ruta_id);
        $monto = $this->calculateMontoTotal();
        $encomienda = $this->createEncomienda($ruta, $monto);
        
        if ($encomienda) {
            $this->createPaquetes($encomienda);
            $this->createDocuments($encomienda);
            $this->registerCajaEntry($cajaActiva, $encomienda, $monto);
            $this->openTicketModal($encomienda);
        }
    }
    
    /**
     * Valida que el usuario tenga una caja abierta
     * Retorna la caja activa si existe, null si no hay caja abierta
     * 
     * @return object|null Caja activa del usuario o null si no existe
     */
    private function validateCajaAbierta()
    {
        $cajaActiva = $this->cajaService->getCajaActiva(Auth::id());
        if (!$cajaActiva) {
            $this->error('Es necesario aperturar caja para esta operacion');
            return null;
        }
        return $cajaActiva;
    }
    
    /**
     * Valida los datos requeridos antes de crear la encomienda
     * Verifica que existan remitente_id, destinatario_id, facturacion_id y paquetes
     * 
     * @return void
     * @throws \Illuminate\Validation\ValidationException Si la validación falla
     */
    private function validateEncomiendaData()
    {
        $this->validate([
            'remitente_id' => 'required',
            'destinatario_id' => 'required',
            'facturacion_id' => 'required',
            'paquetes' => 'required|array',
        ], [
            'remitente_id.required' => 'El remitente es obligatorio.',
            'destinatario_id.required' => 'El destinatario es obligatorio.',
            'facturacion_id.required' => 'El cliente de facturación es obligatorio.',
            'paquetes.required' => 'Debe agregar al menos un paquete.',
            'paquetes.array' => 'Los paquetes deben ser un arreglo válido.',
        ]);
    }
    
    /**
     * Calcula el monto total sumando el valor de todos los paquetes multiplicado por su cantidad
     * 
     * @return float Monto total calculado
     */
    private function calculateMontoTotal(): float
    {
        $monto = 0;
        foreach ($this->paquetes as $paquete) {
            $monto += $paquete['valor'] * $paquete['cantidad'];
        }
        return $monto;
    }
    
    /**
     * Crea el registro de encomienda en la base de datos
     * Genera código único, PIN de seguridad y estado de crédito según el tipo de pago
     * 
     * @param RutaSucursal $ruta Ruta seleccionada para el envío
     * @param float $monto Monto total de la encomienda
     * @return Encomienda|null Instancia de la encomienda creada o null si falla
     */
    private function createEncomienda(RutaSucursal $ruta, float $monto): ?Encomienda
    {
        $encomiendaService = app(\App\Services\Package\EncomiendaService::class);
        $code = $encomiendaService->generateUniqueCode($ruta->sucursal_origen_id);
        $pin = $this->generatePin();
        $estadoCredito = $this->tipo_pago == 'CREDITO' ? 'PENDIENTE' : 'CANCELADO';
        
        return Encomienda::create($this->getEncomiendaData($ruta, $monto, $code, $pin, $estadoCredito));
    }
    
    /**
     * Prepara y retorna el array con todos los datos necesarios para crear la encomienda
     * Incluye información de clientes, ruta, fechas, pagos, documentos y configuración de entrega
     * 
     * @param RutaSucursal $ruta Ruta seleccionada
     * @param float $monto Monto total
     * @param string $code Código único generado
     * @param string $pin PIN de seguridad
     * @param string $estadoCredito Estado del crédito (PENDIENTE o CANCELADO)
     * @return array Array con todos los datos de la encomienda
     */
    private function getEncomiendaData(RutaSucursal $ruta, float $monto, string $code, string $pin, string $estadoCredito): array
    {
        return [
            'code' => $code,
            'user_id' => Auth::id(),
            'transportista_id' => $ruta->transportista_id,
            'vehiculo_id' => $ruta->vehiculo_id,
            'customer_id' => $this->remitente_id,
            'sucursal_id' => $ruta->sucursal_origen_id,
            'customer_dest_id' => $this->destinatario_id,
            'sucursal_dest_id' => $ruta->sucursal_destino_id,
            'customer_fact_id' => $this->facturacion_id,
            'cantidad' => count($this->paquetes),
            'monto' => $monto,
            'monto_descuento' => 0,
            'motivo_descuento' => '',
            'doc_ticket' => 0,
            'doc_guia' => 0,
            'doc_factura' => 0,
            'fecha_creacion' => Carbon::now(),
            'fecha_envio' => $ruta->fecha_salida,
            'fecha_recepcion' => null,
            'fecha_entrega' => null,
            'fecha_retorno' => null,
            'estado_pago' => $this->estado_pago,
            'tipo_pago' => $this->tipo_pago,
            'metodo_pago' => $this->metodo_pago,
            'tipo_comprobante' => $this->tipo_comprobante,
            'estado_credito' => $estadoCredito,
            'docsTraslado' => json_encode($this->documentos_traslado),
            'glosa' => $this->observaciones,
            'observation' => $this->observaciones,
            'estado_encomienda' => 'REGISTRADO',
            'pin' => $pin,
            'isTransbordo' => false,
            'isHome' => $this->isHome,
            'direccion_envio' => $this->direccion_envio,
            'isReturn' => $this->isReturn,
            'isActive' => true,
            'ruta_id' => $this->ruta_id,
        ];
    }
    
    /**
     * Genera el PIN de seguridad para la encomienda
     * Si es entrega a domicilio, genera un PIN aleatorio de 3 dígitos
     * Si no es domicilio, usa el PIN ingresado por el usuario
     * 
     * @return string PIN de seguridad (3 dígitos)
     */
    private function generatePin(): string
    {
        return $this->isHome ? (string)rand(100, 999) : $this->pin_seguridad;
    }
    
    /**
     * Crea los registros de paquetes asociados a la encomienda en la base de datos
     * Itera sobre el array de paquetes y crea un registro por cada uno
     * Actualiza las propiedades encomienda y encomienda_id del componente
     * 
     * @param Encomienda $encomienda Instancia de la encomienda creada
     * @return void
     */
    private function createPaquetes(Encomienda $encomienda): void
    {
        foreach ($this->paquetes as $paquete) {
            $encomienda->paquetes()->create([
                'description' => $paquete['descripcion'],
                'peso' => $paquete['peso'],
                'amount' => $paquete['valor'],
                'cantidad' => $paquete['cantidad'],
                'und_medida' => $paquete['unidad'],
                'sub_total' => $paquete['valor'] * $paquete['cantidad'],
            ]);
        }
        
        $this->encomienda = $encomienda;
        $this->encomienda_id = $encomienda->id;
    }
    
    /**
     * Crea los documentos según el tipo de comprobante
     */
    private function createDocuments(Encomienda $encomienda): void
    {
        if ($encomienda->tipo_comprobante == 'TICKET') {
            $this->createTicketDocument($encomienda);
        } elseif (in_array($encomienda->tipo_comprobante, ['FACTURA', 'BOLETA'])) {
            $this->createInvoiceAndTicketDocuments($encomienda);
        }
    }
    
    /**
     * Crea únicamente el ticket para la encomienda
     * Actualiza el campo doc_ticket de la encomienda con el ID del ticket creado
     * 
     * @param Encomienda $encomienda Instancia de la encomienda
     * @return void
     */
    private function createTicketDocument(Encomienda $encomienda): void
    {
        $ticket = $this->createTicket($encomienda);
        if ($ticket) {
            $encomienda->doc_ticket = $ticket->id;
            $encomienda->save();
        }
    }
    
    /**
     * Crea factura/boleta y ticket
     */
    private function createInvoiceAndTicketDocuments(Encomienda $encomienda): void
    {
        $invoice = $this->createInvoice($encomienda);
        $ticket = $this->createTicket($encomienda);
        
        if ($invoice) {
            $encomienda->doc_factura = $invoice->id;
            $encomienda->doc_ticket = $ticket->id;
            $encomienda->save();
        }
    }
    
    /**
     * Registra una entrada en caja si la encomienda está pagada al contado
     * Solo registra si el estado de pago es 'ENVIO PAGADO' y el tipo de pago es 'CONTADO'
     * 
     * @param object $cajaActiva Instancia de la caja activa
     * @param Encomienda $encomienda Instancia de la encomienda
     * @param float $monto Monto a registrar en caja
     * @return void
     */
    private function registerCajaEntry($cajaActiva, Encomienda $encomienda, float $monto): void
    {
        if ($encomienda->estado_pago === 'ENVIO PAGADO' && $encomienda->tipo_pago === 'CONTADO') {
            $tipoEntry = \App\Models\Configuration\TipoEntryCaja::where('is_active', true)->first();
            $tipoEntryId = $tipoEntry ? $tipoEntry->id : 1;
            $this->cajaService->crearEntrada(
                $cajaActiva->id,
                $monto,
                $encomienda->code,
                $tipoEntryId,
                $encomienda->metodo_pago
            );
        }
    }
    
    /**
     * Abre el modal de impresión de ticket
     * Establece el ID de la encomienda para poder generar el PDF del ticket
     * 
     * @param Encomienda $encomienda Encomienda creada
     * @return void
     */
    private function openTicketModal(Encomienda $encomienda): void
    {
        $this->encomienda_id = $encomienda->id;
        $this->modalConfirmacionEncomienda = false;
        $this->modalImprimirTicket = true;
    }

    /**
     * Prepara y abre el modal de confirmación de envío
     * Valida que haya caja abierta, carga datos de facturación y obtiene la ruta seleccionada
     * 
     * @return void
     */
    public function confirmarEnvio()
    {
        $cajaActiva = $this->cajaService->getCajaActiva(Auth::id());
        if (!$cajaActiva) {
            $this->error('Es necesario aperturar caja para esta operacion');
            return;
        }
        
        $this->loadFacturacionData();
        $this->ruta = RutaSucursal::find($this->ruta_id);
        $this->modalConfirmacionEncomienda = true;
    }
    
    /**
     * Carga los datos de facturación si existe facturacion_id
     */
    private function loadFacturacionData(): void
    {
        if (!$this->facturacion_id) {
            return;
        }
        
        $customer = Customer::find($this->facturacion_id);
        if ($customer) {
            $this->type_code_facturacion = $customer->type_code;
            $this->code_facturacion = $customer->code;
            $this->loadFacturacionDataFromCustomer($customer);
        }
    }


    /**
     * Abre el modal para visualizar el ticket PDF de una encomienda
     * 
     * @param int $encomiendaId ID de la encomienda
     * @return void
     */
    public function verTicketPDF($encomiendaId)
    {
        $this->encomienda_id = $encomiendaId;
        $this->modalImprimirTicket = true;
    }
    
    /**
     * Refresca la visualización del ticket
     * Emite un evento Livewire para recargar el PDF
     * 
     * @return void
     */
    public function refreshTicket()
    {
        $this->dispatch('ticket-refreshed');
    }
    
    /**
     * Crea la guía de remisión PDF para una encomienda
     * Muestra mensaje de éxito o error según el resultado
     * 
     * @param int $encomiendaId ID de la encomienda
     * @return void
     */
    public function crearGuiaPDF($encomiendaId)
    {
        $encomienda = Encomienda::findOrFail($encomiendaId);
        $guia = $this->createGuiTrans($encomienda);
        if ($guia) {
            // El doc_guia ya se guarda en el método createGuiTrans del trait
            
        } else {
            $this->error('Error al crear la guía de remisión');
        }
    }
    public function verGuiaPDF($encomiendaId)
    {
        $this->encomienda_id = $encomiendaId;
        $this->modalVerGuia = true;
    }
    
    public function refreshGuia()
    {
        $this->dispatch('guia-refreshed');
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

    /**
     * Refresca la visualización de la factura/boleta
     * Emite un evento Livewire para recargar el PDF
     * 
     * @return void
     */
    public function refreshInvoice()
    {
        $this->dispatch('invoice-refreshed');
    }
    
    /**
     * Obtiene los datos de personas (remitente y destinatario) formateados para el modal de confirmación
     * 
     * @return array Array con datos estructurados de remitente y destinatario
     */
    public function getPersonasModalData(): array
    {
        return [
            [
                'tipo' => 'Remitente',
                'type_code' => $this->type_code_remitente,
                'code' => $this->code_remitente,
                'name' => $this->name_remitente,
                'address' => $this->address_remitente,
                'texto_ubigeo' => $this->texto_ubigeo_remitente,
                'ubigeo' => $this->ubigeo_remitente,
                'phone' => $this->phone_remitente,
                'email' => $this->email_remitente,
                'id' => $this->remitente_id,
            ],
            [
                'tipo' => 'Destinatario',
                'type_code' => $this->type_code_destinatario,
                'code' => $this->code_destinatario,
                'name' => $this->name_destinatario,
                'address' => $this->address_destinatario,
                'texto_ubigeo' => $this->texto_ubigeo_destinatario,
                'ubigeo' => $this->ubigeo_destinatario,
                'phone' => $this->phone_destinatario,
                'email' => $this->email_destinatario,
                'id' => $this->destinatario_id,
            ],
        ];
    }
    
    /**
     * Resetea todos los campos del componente a sus valores iniciales
     * Limpia formularios, modales, validaciones y errores
     * Vuelve al tab inicial (remitente)
     * 
     * @return void
     */
    public function resetComponent()
    {
        $this->selectedTab = 'remitente';
        $this->modalConfirmacionEncomienda = false;
        $this->modalVerSticker = false;
        $this->modalVerDeclaracion = false;
        $this->reset(
            'encomienda_id',
            'encomienda',
            'remitente_id',
            'type_code_remitente',
            'code_remitente',
            'name_remitente',
            'address_remitente',
            'ubigeo_remitente',
            'texto_ubigeo_remitente',
            'phone_remitente',
            'destinatario_id',
            'type_code_destinatario',
            'code_destinatario',
            'name_destinatario',
            'address_destinatario',
            'ubigeo_destinatario',
            'texto_ubigeo_destinatario',
            'phone_destinatario',
            'facturacion_id',
            'type_code_facturacion',
            'code_facturacion',
            'name_facturacion',
            'address_facturacion',
            'ubigeo_facturacion',
            'texto_ubigeo_facturacion',
            'phone_facturacion',
            'paquete_descripcion',
            'paquete_peso',
            'paquete_valor',
            'paquete_cantidad',
            'paquete_unidad',
            'paquetes',
            'pin_seguridad',
            'pin_seguridad_confirm',
            'direccion_envio',
            'observaciones',
            'documentos_traslado',
            'documento_tipo',
            'documento_numero',
            'documento_ruc_emisor',
            'isHome',
            'isReturn',
            'email_facturacion',
            'tipo_comprobante',
            'tipo_pago',
            'metodo_pago',
            'estado_pago',
        );
        $this->resetValidation();
        $this->resetErrorBag();
    }
}
