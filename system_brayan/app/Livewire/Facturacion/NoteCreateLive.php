<?php

namespace App\Livewire\Facturacion;

use App\Models\Configuration\Company;
use App\Models\Facturacion\Invoice;
use App\Models\Facturacion\Note;
use App\Models\Package\Customer;
use App\Models\Package\Paquete;
use App\Services\Facturacion\NoteService;
use App\Services\Shared\ServiceTableSunat;
use App\Traits\LogCustom;
use App\Traits\SearchDocument;
use App\Traits\ToastTrait;
use App\Traits\UtilsTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Luecano\NumeroALetras\NumeroALetras;

class NoteCreateLive extends Component
{
    use LogCustom, ToastTrait, WithPagination, WithoutUrlPagination, SearchDocument, UtilsTrait;
    
    public $title = 'NOTA DE CRÉDITO';
    public $sub_title = 'Crear Nota de Crédito';
    public $tipoDocAfectado = '03';
    public $numDocfectado;
    public $motivo = '01';
    public $tipoDocumento = '1';
    public $numDocumento = '';
    public $razonSocial = '';
    public $direccion = '';
    public $ubigeo = '';
    public $telefono = '';
    public $client;

    public $paquetes;
    public $cantidad;
    public $und_medida = 'ZZ';
    public $description;
    public $peso;
    public $amount;
    public $sub_total;
    public $igv;
    public $total;
    public $id;
    public $note;
    public $modalPrintNote = false;

    protected $noteService;

    public function boot(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    public function mount($id = null)
    {
        $this->paquetes = collect([])->keyBy('id');
        if ($id) {
            $this->numDocfectado = $id;
            $this->processInvoice();
        }
    }
    
    public function processInvoice()
    {
        $this->paquetes = collect([]);
        $invoice = Invoice::findOrFail($this->numDocfectado);
        $this->tipoDocAfectado = $invoice->tipoDoc;
        $this->numDocfectado = $invoice->id;
        $this->motivo = '01';
        $this->client = $invoice->client;
        $this->tipoDocumento = $invoice->client->type_code;
        $this->numDocumento = $invoice->client->code;
        $this->razonSocial = $invoice->client->name;
        $this->direccion = $invoice->client->address;
        $this->ubigeo = $invoice->client->ubigeo;
        $this->telefono = $invoice->client->phone;
        foreach ($invoice->details as $paqueteInvoice) {
            $paquete = new Paquete();
            $paquete->id = $paqueteInvoice->id;
            $paquete->cantidad = $paqueteInvoice->cantidad;
            $paquete->und_medida = $paqueteInvoice->unidad;
            $paquete->description = $paqueteInvoice->descripcion;
            $paquete->peso = 10;
            $paquete->amount = $paqueteInvoice->mtoPrecioUnitario;
            $paquete->sub_total = $paqueteInvoice->mtoPrecioUnitario * $paqueteInvoice->cantidad;
            $this->paquetes->push($paquete->toArray());
        }
        $this->calculateTotals();
    }
    
    public function updatedNumDocfectado()
    {
        $this->processInvoice();
    }
    
    public function updatedTipoDocAfectado()
    {
        $this->paquetes = collect([]);
        $this->calculateTotals();
        $this->resetValidation();
        $this->numDocfectado = '';
        $this->client = null;
        $this->tipoDocumento = '1';
        $this->numDocumento = '';
        $this->razonSocial = '';
        $this->direccion = '';
        $this->ubigeo = '';
        $this->telefono = '';
    }
    
    public function render()
    {
        $service = new ServiceTableSunat();
        $tipoDocs = [
            ['codigo' => '01', 'descripcion' => 'Factura (01)'],
            ['codigo' => '03', 'descripcion' => 'Boleta (03)'],
        ];
        $motivos = $service->getAll('sunat_09');
        $tipoDocuments = [
            ['codigo' => '0', 'sigla' => 'OTRO DOCUMENTO cod(0)'],
            ['codigo' => '1', 'sigla' => 'DNI cod(1)'],
            ['codigo' => '6', 'sigla' => 'RUC cod(6)'],
        ];
        $ubigeos = $service->getAll('ubigeo');
        $unidadMedidas = $service->getAll('sunat_03');
        
        $invoices = Invoice::where('tipoDoc', $this->tipoDocAfectado)
            ->where('sucursal_id', Auth::user()->sucursal->id)
            ->latest()
            ->limit(50)
            ->get();

        return view('livewire.facturacion.note-create-live', compact('tipoDocuments', 'ubigeos', 'tipoDocs', 'motivos', 'unidadMedidas', 'invoices'));
    }
    
    public function emitNote()
    {
        $rules = [
            'client' => 'required',
            'tipoDocAfectado' => 'required',
            'numDocfectado' => 'required',
            'motivo' => 'required',
            'tipoDocumento' => 'required',
            'numDocumento' => 'required',
            'paquetes' => 'required',
            'sub_total' => 'required',
            'igv' => 'required',
            'total' => 'required',
        ];
        $messages = [
            'client.required' => 'Error, es necesario seleccionar un cliente!',
            'tipoDocAfectado.required' => 'Error, es necesario seleccionar un tipo de documento afectado!',
            'numDocfectado.required' => 'Error, es necesario seleccionar un documento afectado!',
            'motivo.required' => 'Error, es necesario seleccionar un motivo!',
            'tipoDocumento.required' => 'Error, es necesario seleccionar un tipo de documento!',
            'numDocumento.required' => 'Error, es necesario seleccionar un número de documento!',
            'paquetes.required' => 'Error, es necesario seleccionar un paquete!',
            'sub_total.required' => 'Error, es necesario seleccionar un subtotal!',
            'igv.required' => 'Error, es necesario seleccionar un igv!',
            'total.required' => 'Error, es necesario seleccionar un total!',
        ];
        $this->validate($rules, $messages);

        $invoice = Invoice::findOrFail($this->numDocfectado);
        $formatter = new NumeroALetras();
        $company = Company::first();
        
        $serie = 'RC01';
        $correlativo = Note::where('tipoDoc', '07')->where('serie', $serie)->count() + 1;
        
        $service = new ServiceTableSunat();
        $desMotivo = $service->findById('sunat_09', 'codigo', $this->motivo);
        
        $noteData = [
            'company_id' => $company->id,
            'customer_id' => $this->client->id,
            'sucursal_id' => Auth::user()->sucursal->id,
            'ublVersion' => '2.1',
            'tipoDoc' => '07',
            'serie' => $serie,
            'correlativo' => $correlativo,
            'fechaEmision' => $this->dateNow('Y-m-d H:i:m'),
            'tipoDocAfectado' => $this->tipoDocAfectado,
            'numDocfectado' => $invoice->serie . '-' . $invoice->correlativo,
            'codMotivo' => $this->motivo,
            'desMotivo' => $desMotivo->descripcion ?? 'Anulación de la operación',
            'tipoMoneda' => 'PEN',
            'mtoOperGravadas' => $this->sub_total,
            'mtoIGV' => $this->igv,
            'totalImpuestos' => $this->igv,
            'mtoImpVenta' => $this->total,
            'monto_letras' => $formatter->toInvoice($this->total, 2, 'SOLES'),
        ];

        $legends = [
            ['code' => '1000', 'value' => $formatter->toInvoice($this->total, 2, 'SOLES')],
        ];
        $noteData['legends'] = json_encode($legends);

        $detailsData = [];
        foreach ($this->paquetes as $paquete) {
            $mtoValorUnitario = round($paquete['amount'] / 1.18, 2);
            $detailsData[] = [
                'tipAfeIgv' => '10',
                'codProducto' => $paquete['id'],
                'unidad' => $paquete['und_medida'],
                'descripcion' => $paquete['description'],
                'cantidad' => $paquete['cantidad'],
                'mtoValorUnitario' => $mtoValorUnitario,
                'mtoValorVenta' => $mtoValorUnitario * $paquete['cantidad'],
                'mtoBaseIgv' => $mtoValorUnitario * $paquete['cantidad'],
                'porcentajeIgv' => 18,
                'igv' => ($paquete['amount'] - $mtoValorUnitario) * $paquete['cantidad'],
                'totalImpuestos' => ($paquete['amount'] - $mtoValorUnitario) * $paquete['cantidad'],
                'mtoPrecioUnitario' => $paquete['amount'],
            ];
        }

        try {
            $note = $this->noteService->createNote($noteData, $detailsData);
            $this->note = $note;
            $this->modalPrintNote = true;
            $this->resetForm();
            $this->success('Nota de crédito emitida correctamente');
        } catch (\Exception $e) {
            $this->error('Error al emitir nota: ' . $e->getMessage());
        }
    }
    
    public function closePrintNote()
    {
        $this->modalPrintNote = false;
        $this->note = null;
        $this->resetForm();
    }
    
    private function resetForm()
    {
        $this->client = null;
        $this->numDocumento = '';
        $this->razonSocial = '';
        $this->direccion = '';
        $this->ubigeo = '';
        $this->telefono = '';
        $this->paquetes = collect([]);
        $this->calculateTotals();
        $this->resetValidation();
        $this->motivo = '01';
        $this->tipoDocumento = '1';
    }
    
    public function buscarDocumento()
    {
        $rules = [
            'tipoDocumento' => 'required',
            'numDocumento' => 'required|min:8|max:11',
        ];
        $messages = [
            'tipoDocumento.required' => 'El tipo de documento es requerido',
            'numDocumento.required' => 'El número de documento es requerido',
            'numDocumento.min' => 'El número de documento debe tener 8 dígitos',
            'numDocumento.max' => 'El número de documento debe tener 11 dígitos',
        ];
        $this->validate($rules, $messages);

        $customer = Customer::where('type_code', $this->tipoDocumento)->where('code', $this->numDocumento)->first();
        if ($customer) {
            $this->fillCustomerData($customer);
            return;
        }

        $tipo = $this->tipoDocumento == '6' ? 'ruc' : 'dni';
        $respuesta = $this->searchComplete($tipo, $this->numDocumento);

        if (!$respuesta['encontrado']) {
            $this->resetCustomerData();
            $this->error('El cliente no existe!, verifique el número de documento!');
            return;
        }

        $this->fillCustomerDataFromResponse($respuesta, $tipo);
    }
    
    private function fillCustomerData($customer)
    {
        $this->razonSocial = $customer->name;
        $this->direccion = $customer->address;
        $this->ubigeo = $customer->ubigeo;
        $this->telefono = $customer->phone;
        $this->client = $customer;
    }
    
    private function resetCustomerData()
    {
        $this->razonSocial = '';
        $this->direccion = '';
        $this->ubigeo = '';
    }
    
    private function fillCustomerDataFromResponse($respuesta, $tipo)
    {
        if ($tipo == 'ruc') {
            $this->razonSocial = $respuesta['data']->razon_social;
            $this->direccion = $respuesta['data']->direccion;
            $this->ubigeo = $respuesta['data']->codigo_ubigeo;
        } else {
            $this->razonSocial = $respuesta['data']->nombre;
            $this->direccion = '';
            $this->ubigeo = '';
        }

        $this->client = Customer::firstOrCreate(
            ['type_code' => $this->tipoDocumento, 'code' => $this->numDocumento],
            ['name' => $this->razonSocial, 'address' => $this->direccion, 'ubigeo' => $this->ubigeo, 'phone' => $this->telefono]
        );
    }
    
    public function addPaquete()
    {
        if ($this->validatePaquete()) {
            $paquete = new Paquete();
            $paquete->id = $this->paquetes->count() + 1;
            $paquete->cantidad = $this->cantidad;
            $paquete->und_medida = $this->und_medida;
            $paquete->description = $this->description;
            $paquete->peso = $this->peso;
            $paquete->amount = $this->amount;
            $paquete->sub_total = $this->amount * $this->cantidad;
            $this->paquetes->push($paquete->toArray());
            $this->calculateTotals();
        } else {
            $this->error('Error, verifique los datos!');
        }
    }
    
    private function validatePaquete()
    {
        $validations = [
            'cantidad' => 'required|numeric',
            'und_medida' => 'required',
            'description' => 'required',
            'peso' => 'required|numeric',
            'amount' => 'required|numeric',
        ];
        $errorMessage = [
            'cantidad.required' => 'Error, es necesario ingresar la cantidad!',
            'cantidad.numeric' => 'Error, la cantidad debe ser un número!',
            'und_medida.required' => 'Error, es necesario ingresar la unidad de medida!',
            'description.required' => 'Error, es necesario ingresar la descripción!',
            'peso.required' => 'Error, es necesario ingresar el peso!',
            'peso.numeric' => 'Error, el peso debe ser un número!',
            'amount.required' => 'Error, es necesario ingresar el precio unitario!',
            'amount.numeric' => 'Error, el precio unitario debe ser un número!',
        ];
        $this->validate($validations, $errorMessage);
        return true;
    }
    
    public function restPaquete($id)
    {
        $this->paquetes->pull($id - 1);
        $this->calculateTotals();
    }

    public function resetPaquete()
    {
        $this->paquetes = collect([]);
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->total = round($this->paquetes->sum('sub_total'), 2);
        $this->sub_total = round($this->total / 1.18, 2);
        $this->igv = round($this->total - $this->sub_total, 2);
    }
}

