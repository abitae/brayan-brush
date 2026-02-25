<?php

namespace App\Livewire\Cobrar;

use App\Models\Package\Customer;
use App\Models\Package\Encomienda;
use App\Traits\CajaTrait;
use App\Traits\InvoiceTrait;
use App\Traits\LogCustom;
use App\Traits\SearchDocument;
use App\Traits\ToastTrait;
use App\Traits\UtilsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class EncomiendaCobrar extends Component
{
    use ToastTrait, UtilsTrait, WithPagination, WithoutUrlPagination, CajaTrait, SearchDocument, InvoiceTrait, LogCustom;

    const DEFAULT_PER_PAGE = 10;
    
    public $title = 'CUENTAS POR COBRAR';
    public $sub_title = 'Cuentas por cobrar';
    
    public ?int $filtroSucursal = null;
    public ?string $filtroFechaInicio = null;
    public ?string $filtroFechaFin = null;
    public ?string $search = null;
    public ?string $FiltroEstadoEncomienda = null;
    public ?string $FiltroEstadoCredito = 'Pendiente';
    public int $perPage = self::DEFAULT_PER_PAGE;

    public bool $showDrawer = false;
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
    public $caja;

    public function mount(): void
    {
        $this->resetFilters();
        $this->caja = $this->cajaIsActive(Auth::user());
    }

    public function resetFilters(): void
    {
        $this->filtroFechaInicio = Carbon::now()->startOfDay()->format('Y-m-d H:i');
        $this->filtroFechaFin = $this->dateNow('Y-m-d H:i:s');
        $this->FiltroEstadoEncomienda = null;
        $this->FiltroEstadoCredito = 'Pendiente';
        $this->filtroSucursal = null;
        $this->search = null;
    }

    public function render()
    {
        $encomiendas = Encomienda::query()
            ->with(['remitente', 'destinatario', 'sucursal_remitente'])
            ->where('estado_credito', $this->FiltroEstadoCredito)
            ->when($this->search, function (Builder $query) {
                return $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('pin', 'like', '%' . $this->search . '%')
                        ->orWhereHas('remitente', function ($subQuery) {
                            $subQuery->where('code', 'like', '%' . $this->search . '%')
                                ->orWhere('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filtroSucursal, function (Builder $query) {
                return $query->where('sucursal_id', $this->filtroSucursal);
            })
            ->when($this->FiltroEstadoEncomienda, function (Builder $query) {
                return $query->where('estado_encomienda', $this->FiltroEstadoEncomienda);
            })
            ->when($this->filtroFechaInicio && $this->filtroFechaFin, function (Builder $query) {
                return $query->whereBetween('fecha_creacion', [
                    Carbon::parse($this->filtroFechaInicio)->startOfDay(),
                    Carbon::parse($this->filtroFechaFin)->endOfDay()
                ]);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.cobrar.encomienda-cobrar', compact('encomiendas'));
    }

    public function openCobrarModal(Encomienda $encomienda)
    {
        if (!$this->caja) {
            $this->error('Debe abrir una caja primero');
            return;
        }
        $this->encomienda = $encomienda;
        $this->cliFacturacion = $encomienda->facturacion;
        $this->cliFacturacion_type_code = $this->cliFacturacion->type_code;
        $this->cliFacturacion_code = $this->cliFacturacion->code;
        $this->cliFacturacion_name = $this->cliFacturacion->name;
        $this->cliFacturacion_address = $this->cliFacturacion->address;
        $this->cliFacturacion_phone = $this->cliFacturacion->phone;
        $this->cliFacturacion_ubigeo = $this->cliFacturacion->ubigeo;
        $this->modalCobrar = true;
    }

    public function cobrarEncomienda()
    {
        if (!$this->caja) {
            $this->error('Debe abrir una caja primero');
            return;
        }

        try {
            $monto = $this->encomienda->monto - ($this->monto_descuento ?? 0);
            
            $this->cajaEntry(
                $this->caja->id,
                $monto,
                'COBRO ENCOMIENDA ' . $this->encomienda->code,
                $this->metodo_pago,
                $this->encomienda->code
            );

            $this->encomienda->estado_credito = 'Cancelado';
            $this->encomienda->estado_pago = 'PAGADO';
            $this->encomienda->monto_descuento = $this->monto_descuento ?? 0;
            $this->encomienda->motivo_descuento = $this->motivo_descuento;
            $this->encomienda->tipo_pago = $this->tipo_pago;
            $this->encomienda->metodo_pago = $this->metodo_pago;
            $this->encomienda->tipo_comprobante = $this->tipo_comprobante;
            $this->encomienda->save();

            $this->storeInvoce($this->encomienda);
            $this->modalCobrar = false;
            $this->reset(['monto_descuento', 'motivo_descuento']);
            $this->success('Encomienda cobrada correctamente');
        } catch (\Exception $e) {
            $this->error('Error al cobrar encomienda: ' . $e->getMessage());
        }
    }
}

