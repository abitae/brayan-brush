<?php

namespace App\Livewire\Cobrar;

use App\Exports\ReportEncomiendaExport;
use App\Models\Configuration\Sucursal;
use App\Models\Package\Customer;
use App\Models\Package\Encomienda;
use App\Traits\CajaTrait;
use App\Traits\InvoiceTrait;
use App\Traits\LogCustom;
use App\Traits\SearchDocument;
use App\Traits\UtilsTrait;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Excel;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EncomiendaCobrar extends Component
{
    use Toast, UtilsTrait, WithPagination, WithoutUrlPagination;
    use CajaTrait, SearchDocument;
    use InvoiceTrait, LogCustom;
    // Constantes para valores predeterminados y opciones
    const DEFAULT_PER_PAGE = 10;
    const ESTADOS_ENCOMIENDA = [
        ['id' => 'REGISTRADO', 'name' => 'REGISTRADO'],
        ['id' => 'ENVIADO', 'name' => 'ENVIADO'],
        ['id' => 'RECIBIDO', 'name' => 'RECIBIDO'],
        ['id' => 'ENTREGADO', 'name' => 'ENTREGADO']
    ];
    const ESTADOS_CREDITO = [
        ['id' => 'Pendiente', 'name' => 'PENDIENTE'],
        ['id' => 'Cancelado', 'name' => 'CANCELADO'],
    ];
    const METODOS_PAGO = [
        ['id' => 'Efectivo', 'name' => 'Efectivo'],
        ['id' => 'Transferencia', 'name' => 'Transferencia'],
        ['id' => 'Tarjeta', 'name' => 'Tarjeta'],
    ];
    public $title = 'CUENTAS POR COBRAR';
    public $sub_title = 'Cuentas por cobrar';
    // Propiedades para filtros
    public ?int $filtroSucursal = null;
    public ?string $filtroFechaInicio = null;
    public ?string $filtroFechaFin = null;
    public ?string $search = null;
    public ?string $FiltroEstadoEncomienda = null;
    public ?string $FiltroEstadoCredito = 'Pendiente';
    public ?string $filtroMetodoPago = null;
    public int $perPage = self::DEFAULT_PER_PAGE;

    // Propiedades para la interfaz
    public bool $showDrawer = false;
    public array $ids = [];
    public Encomienda $encomienda;
    public $cliFacturacion;
    public $cliFacturacion_type_code;
    public $cliFacturacion_code;
    public $cliFacturacion_name;
    public $cliFacturacion_address;
    public $cliFacturacion_phone;
    public $cliFacturacion_ubigeo;
    public $monto_descuento;
    public $motivo_descuento;
    public $tipo_pago = 'Contado';
    public $tipo_comprobante = 'TICKET';
    public $metodo_pago = 'Efectivo';
    public $modalCobrar = false;
    public function mount(): void
    {
        $this->resetFilters();
    }
    public function resetFilters(): void
    {
        $this->filtroFechaInicio = Carbon::now()->startOfDay()->format('Y-m-d H:i');
        $this->filtroFechaFin = $this->dateNow('Y-m-d H:i:s');
        $this->FiltroEstadoEncomienda = null;
        $this->FiltroEstadoCredito = 'Pendiente';
        $this->filtroMetodoPago = null;
        $this->filtroSucursal = null;
        $this->search = null;
    }
    public function render()
    {
        $encomiendas = $this->getEncomiendasQuery();
        $this->ids = $encomiendas->pluck('id')->toArray();
        return view('livewire.cobrar.encomienda-cobrar', [
            'encomiendas' => $encomiendas->latest()->paginate($this->perPage),
            'sucursals' => $this->getSucursales(),
            'estados' => self::ESTADOS_ENCOMIENDA,
            'estadosCredito' => self::ESTADOS_CREDITO,
            'metodosPago' => self::METODOS_PAGO,
            'totalRegistros' => $encomiendas->count(),
        ]);
    }
    /**
     * Obtiene la consulta base de encomiendas con filtros aplicados
     *
     * @return Builder
     */
    private function getEncomiendasQuery(): Builder
    {
        $query = Encomienda::query();

        // Aplicar filtro por sucursal
        if ($this->filtroSucursal) {
            $query->where('sucursal_id', $this->filtroSucursal);
        }

        // Aplicar filtro por rango de fechas
        if ($this->filtroFechaInicio && $this->filtroFechaFin) {
            $query->whereBetween('created_at', [
                $this->filtroFechaInicio,
                $this->filtroFechaFin
            ]);
        }

        // Aplicar búsqueda global
        if ($this->search) {
            $query->where(function (Builder $query) {
                $query->where('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('remitente', function (Builder $query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('code', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('destinatario', function (Builder $query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('code', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Aplicar filtro por estado de encomienda
        if ($this->FiltroEstadoEncomienda) {
            $query->where('estado_encomienda', $this->FiltroEstadoEncomienda);
        }

        // Aplicar filtro por estado de pago
        if ($this->FiltroEstadoCredito) {
            $query->where('estado_credito', $this->FiltroEstadoCredito);
        }

        // Aplicar filtro por método de pago
        if ($this->filtroMetodoPago) {
            $query->where('metodo_pago', $this->filtroMetodoPago);
        }

        return $query;
    }

    /**
     * Obtiene las sucursales activas
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getSucursales()
    {
        return Sucursal::where('isActive', true)->get();
    }

    /**
     * Muestra el detalle de una encomienda
     *
     * @param Encomienda $encomienda
     * @return void
     */
    public function showEncomienda(Encomienda $encomienda): void
    {
        $this->encomienda = $encomienda;
        $this->showDrawer = true;
    }

    /**
     * Redirige a la creación de boleta/factura
     *
     * @param Encomienda $encomienda
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createBoleta(Encomienda $encomienda)
    {
        return $this->redirectRoute(
            'facturacion.create-invoice',
            ['id' => $encomienda->id],
            false,
            false
        );
    }

    /**
     * Genera y descarga el reporte en Excel
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function excelGenerate()
    {
        if (empty($this->ids)) {
            $this->warning('No hay datos para exportar');
            return null;
        }

        $filename = 'reporte_encomiendas_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $this->success('Reporte generado con éxito');

        return Excel::download(new ReportEncomiendaExport($this->ids), $filename);
    }

    /**
     * Actualiza la cantidad de registros por página
     *
     * @param int $value
     * @return void
     */
    public function updatedPerPage($value): void
    {
        $this->resetPage();
    }

    /**
     * Actualiza cualquier filtro y reinicia la paginación
     *
     * @param mixed $value
     * @param string $property
     * @return void
     */
    public function updated($property): void
    {
        if (in_array($property, [
            'filtroSucursal',
            'filtroFechaInicio',
            'filtroFechaFin',
            'FiltroEstadoEncomienda',
            'FiltroEstadoCredito',
            'filtroMetodoPago',
            'search'
        ])) {
            $this->resetPage();
        }
    }
    public function modalCobrarOpen(Encomienda $encomienda)
    {
        $this->encomienda = $encomienda;
        $this->cliFacturacion = $this->encomienda->facturacion;
        $this->cliFacturacion_type_code = $this->encomienda->facturacion->type_code == 'dni' ? 1 : 6;
        $this->cliFacturacion_code = $this->encomienda->facturacion->code;
        $this->cliFacturacion_name = $this->encomienda->facturacion->name;
        $this->cliFacturacion_address = $this->encomienda->facturacion->address;
        $this->cliFacturacion_phone = $this->encomienda->facturacion->phone;
        $this->cliFacturacion_ubigeo = $this->encomienda->facturacion->ubigeo;
        $this->modalCobrar = true;
    }
    public function cobrarEncomienda()
    {
        if ($this->tipo_comprobante == 'TICKET') {
            $this->cliFacturacion = $this->encomienda->facturacion;
            if (is_numeric($this->monto_descuento) && $this->monto_descuento < $this->encomienda->monto) {
                $this->descuentoCreate();
            }
        }
        if ($this->tipo_comprobante == 'FACTURA' && $this->cliFacturacion_type_code != '6') {
            $this->error('Ops', 'El cliente de Facturacion debe ser un RUC!');
            return;
        }
        $rules = [
            'cliFacturacion' => 'required',
            'tipo_comprobante' => 'required',
            'tipo_pago' => 'required',
            'metodo_pago' => 'required',
        ];
        $message = [
            'cliFacturacion.required' => 'El cliente de facturacion es requerido',
            'tipo_comprobante.required' => 'El tipo de comprobante es requerido',
            'tipo_pago.required' => 'El tipo de pago es requerido',
            'metodo_pago.required' => 'El método de pago es requerido',
        ];
        $this->validate($rules, $message);
        $this->encomienda->customer_fact_id = $this->cliFacturacion->id;
        $this->encomienda->tipo_comprobante = $this->tipo_comprobante;
        $this->encomienda->tipo_pago = $this->tipo_pago;
        $this->encomienda->metodo_pago = $this->metodo_pago;
        $this->encomienda->estado_encomienda = 'ENTREGADO';

        if ($this->tipo_pago == 'Contado') {
            $this->cajaEntry(
                $this->cajaIsActive(Auth::user())->id,
                $this->encomienda->monto,
                'ENTREGA ' . $this->encomienda->tipo_comprobante,
                $this->metodo_pago,
                $this->encomienda->code
            );
            $this->encomienda->estado_credito = 'Cancelado';
        } else {
            return;
        }
        $this->encomienda->save();
        if ($this->tipo_comprobante != 'TICKET') {
            $this->setInvoice($this->encomienda, $this->tipo_comprobante);
        }
        $this->modalCobrar = false;
    }
    private function descuentoCreate()
    {
        $this->encomienda->monto_descuento = $this->monto_descuento;
        $this->encomienda->save();
        if ($this->encomienda->ticket) {
            $this->encomienda->ticket->monto_descuento = $this->monto_descuento;
            $this->encomienda->ticket->save();
        }
        $this->cajaExit(
            $this->cajaIsActive(Auth::user())->id,
            $this->monto_descuento,
            ' DESCUENTO ' . $this->tipo_comprobante,
            $this->metodo_pago,
            $this->encomienda->code
        );
    }
    public function searchFacturacion()
    {

        $rules = [
            'cliFacturacion_type_code' => 'required',
            'cliFacturacion_code' => 'required|min:8|max:11',
        ];
        $messages = [
            'cliFacturacion_type_code.required' => 'El tipo de documento es requerido',
            'cliFacturacion_code.required' => 'El número de documento es requerido',
            'cliFacturacion_code.min' => 'El número de documento debe tener 8 dígitos',
            'cliFacturacion_code.max' => 'El número de documento debe tener 11 dígitos',
        ];
        $this->validate($rules, $messages);
        $cliFacturacion = Customer::where('type_code', $this->cliFacturacion_type_code)
            ->where('code', $this->cliFacturacion_code)
            ->first();
        if ($cliFacturacion) {
            $this->cliFacturacion = $cliFacturacion;
            $this->cliFacturacion_name = $cliFacturacion->name;
            $this->cliFacturacion_address = $cliFacturacion->address;
            $this->cliFacturacion_phone = $cliFacturacion->phone;
            $this->cliFacturacion_ubigeo = $cliFacturacion->ubigeo;
            return;
        }
        $tipo = $this->cliFacturacion_type_code == '6' ? 'ruc' : 'dni';
        $respuesta = $this->searchComplete($tipo, $this->cliFacturacion_code);
        if (!$respuesta['encontrado']) {
            $this->cliFacturacion = null;
            $this->cliFacturacion_name = '';
            $this->cliFacturacion_address = '';
            $this->cliFacturacion_phone = '';
            $this->cliFacturacion_ubigeo = '';
            $this->error('El cliente de Facturacion no existe!, verifique el número de documento!');
            return;
        }
        if ($tipo == 'ruc') {
            $this->cliFacturacion_name = $respuesta['data']->razon_social;
            $this->cliFacturacion_address = $respuesta['data']->direccion;
            $this->cliFacturacion_ubigeo = $respuesta['data']->codigo_ubigeo;
        } else {
            $this->cliFacturacion_name = $respuesta['data']->nombre;
            $this->cliFacturacion_phone = '';
            $this->cliFacturacion_ubigeo = '';
        }

        $this->cliFacturacion = Customer::firstOrCreate(
            [
                'type_code' => $this->cliFacturacion_type_code,
                'code' => $this->cliFacturacion_code
            ],
            [
                'name' => $this->cliFacturacion_name,
                'address' => $this->cliFacturacion_address,
                'ubigeo' => $this->cliFacturacion_ubigeo
            ]
        );
    }
}
