<?php

namespace App\Livewire\Report;

use App\Exports\ReportEncomiendaExport;
use App\Models\Configuration\Sucursal;
use App\Models\Package\Encomienda;
use App\Traits\ToastTrait;
use App\Traits\UtilsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class EncomiendasReport extends Component
{
    use ToastTrait, UtilsTrait, WithPagination, WithoutUrlPagination;

    const DEFAULT_PER_PAGE = 10;
    const ESTADOS_ENCOMIENDA = [
        ['id' => 'REGISTRADO', 'name' => 'REGISTRADO'],
        ['id' => 'ENVIADO', 'name' => 'ENVIADO'],
        ['id' => 'RECIBIDO', 'name' => 'RECIBIDO'],
        ['id' => 'ENTREGADO', 'name' => 'ENTREGADO']
    ];
    const ESTADOS_PAGO = [
        ['id' => 'CONTADO', 'name' => 'CONTADO'],
        ['id' => 'CREDITO', 'name' => 'CREDITO'],
    ];
    const METODOS_PAGO = [
        ['id' => 'EFECTIVO', 'name' => 'EFECTIVO'],
        ['id' => 'YAPE', 'name' => 'YAPE'],
        ['id' => 'TARJETA', 'name' => 'TARJETA'],
        ['id' => 'CHEQUE', 'name' => 'CHEQUE'],
        ['id' => 'TRANSFERENCIA', 'name' => 'TRANSFERENCIA'],
        ['id' => 'OTRO', 'name' => 'OTRO'],
    ];

    public string $title = 'REPORTE ENCOMIENDAS';
    public string $sub_title = 'Módulo de reporte de encomiendas detallado';

    public ?int $filtroSucursal = null;
    public ?string $filtroFechaInicio = null;
    public ?string $filtroFechaFin = null;
    public ?string $search = null;
    public ?string $FiltroEstadoEncomienda = null;
    public ?string $FiltroEstadoPago = null;
    public ?string $filtroMetodoPago = null;
    public int $perPage = self::DEFAULT_PER_PAGE;

    public bool $showDrawer = false;
    public array $ids = [];
    public Encomienda $encomienda;

    public function mount(): void
    {
        $this->resetFilters();
    }

    public function resetFilters(): void
    {
        $this->filtroFechaInicio = Carbon::now()->startOfDay()->format('Y-m-d H:i');
        $this->filtroFechaFin = $this->dateNow('Y-m-d H:i:s');
        $this->FiltroEstadoEncomienda = null;
        $this->FiltroEstadoPago = null;
        $this->filtroMetodoPago = null;
        $this->filtroSucursal = null;
        $this->search = null;
    }

    public function render()
    {
        $encomiendas = Encomienda::query()
            ->with(['remitente', 'destinatario', 'sucursal_remitente', 'sucursal_destinatario', 'user', 'ticket', 'invoice', 'despatche'])
            ->when($this->search, function (Builder $query) {
                return $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('pin', 'like', '%' . $this->search . '%')
                        ->orWhereHas('remitente', function ($subQuery) {
                            $subQuery->where('code', 'like', '%' . $this->search . '%')
                                ->orWhere('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('destinatario', function ($subQuery) {
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
            ->when($this->FiltroEstadoPago, function (Builder $query) {
                // Normalizar el filtro a mayúsculas para comparar
                $filtroNormalizado = strtoupper($this->FiltroEstadoPago);
                return $query->whereRaw('UPPER(tipo_pago) = ?', [$filtroNormalizado]);
            })
            ->when($this->filtroMetodoPago, function (Builder $query) {
                // Normalizar el filtro a mayúsculas para comparar
                $filtroNormalizado = strtoupper($this->filtroMetodoPago);
                return $query->whereRaw('UPPER(metodo_pago) = ?', [$filtroNormalizado]);
            })
            ->when($this->filtroFechaInicio && $this->filtroFechaFin, function (Builder $query) {
                return $query->whereBetween('fecha_creacion', [
                    Carbon::parse($this->filtroFechaInicio)->startOfDay(),
                    Carbon::parse($this->filtroFechaFin)->endOfDay()
                ]);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        $sucursales = Sucursal::where('isActive', true)->get();

        return view('livewire.report.encomiendas-report', compact('encomiendas', 'sucursales'));
    }

    public function exportExcel()
    {
        try {
            $encomiendas = Encomienda::query()
                ->with(['remitente', 'destinatario', 'sucursal_remitente', 'sucursal_destinatario', 'user', 'ticket', 'invoice', 'despatche', 'paquetes'])
                ->when($this->search, function (Builder $query) {
                    return $query->where(function ($q) {
                        $q->where('code', 'like', '%' . $this->search . '%')
                            ->orWhere('pin', 'like', '%' . $this->search . '%')
                            ->orWhereHas('remitente', function ($subQuery) {
                                $subQuery->where('code', 'like', '%' . $this->search . '%')
                                    ->orWhere('name', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('destinatario', function ($subQuery) {
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
                ->when($this->FiltroEstadoPago, function (Builder $query) {
                    // Normalizar el filtro a mayúsculas para comparar
                    $filtroNormalizado = strtoupper($this->FiltroEstadoPago);
                    return $query->whereRaw('UPPER(tipo_pago) = ?', [$filtroNormalizado]);
                })
                ->when($this->filtroMetodoPago, function (Builder $query) {
                    // Normalizar el filtro a mayúsculas para comparar
                    $filtroNormalizado = strtoupper($this->filtroMetodoPago);
                    return $query->whereRaw('UPPER(metodo_pago) = ?', [$filtroNormalizado]);
                })
                ->when($this->filtroFechaInicio && $this->filtroFechaFin, function (Builder $query) {
                    return $query->whereBetween('fecha_creacion', [
                        Carbon::parse($this->filtroFechaInicio)->startOfDay(),
                        Carbon::parse($this->filtroFechaFin)->endOfDay()
                    ]);
                })
                ->orderBy('id', 'desc')
                ->get();

            return Excel::download(new ReportEncomiendaExport($encomiendas), 'reporte-encomiendas-' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            $this->error('Error al exportar: ' . $e->getMessage());
        }
    }
}

