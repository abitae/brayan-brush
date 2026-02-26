<?php

namespace App\Livewire\Report;

use App\Exports\ReportFacturacionExport;
use App\Models\Facturacion\Invoice;
use App\Models\Facturacion\Note;
use App\Models\Facturacion\Ticket;
use App\Traits\ToastTrait;
use App\Traits\UtilsTrait;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class FacturacionReport extends Component
{
    use ToastTrait, UtilsTrait, WithPagination, WithoutUrlPagination;

    public string $title = 'REPORTE DE FACTURACIÓN';
    public string $sub_title = 'Módulo de reporte de facturación';
    public int $perPage = 20;

    public $filtroFechaInicio;
    public $filtroFechaFin;
    public $search;
    public $tipoDocumento = 'Todos';
    public $tiposDocumento = [
        ['id' => 'Todos', 'name' => 'Todos'],
        ['id' => 'Factura', 'name' => 'Facturas'],
        ['id' => 'Boleta', 'name' => 'Boletas'],
        ['id' => 'Ticket', 'name' => 'Tickets'],
        ['id' => 'Nota', 'name' => 'Notas'],
    ];

    public function mount()
    {
        $this->filtroFechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->filtroFechaFin = Carbon::now()->endOfDay()->format('Y-m-d');
    }

    public function render()
    {
        $invoices = collect();
        $notes = collect();
        $tickets = collect();

        if ($this->tipoDocumento == 'Todos' || $this->tipoDocumento == 'Factura' || $this->tipoDocumento == 'Boleta') {
            $invoices = Invoice::query()
                ->with(['client', 'company', 'sucursal'])
                ->when($this->search, function ($query) {
                    return $query->where(function ($q) {
                        $q->where('serie', 'like', '%' . $this->search . '%')
                            ->orWhere('correlativo', 'like', '%' . $this->search . '%')
                            ->orWhereHas('client', function ($query) {
                                $query->where('code', 'like', '%' . $this->search . '%')
                                    ->orWhere('name', 'like', '%' . $this->search . '%');
                            });
                    });
                })
                ->when($this->filtroFechaInicio && $this->filtroFechaFin, function ($query) {
                    return $query->whereBetween('created_at', [
                        Carbon::parse($this->filtroFechaInicio)->startOfDay(),
                        Carbon::parse($this->filtroFechaFin)->endOfDay()
                    ]);
                })
                ->when($this->tipoDocumento == 'Factura', function ($query) {
                    return $query->where('tipoDoc', '01');
                })
                ->when($this->tipoDocumento == 'Boleta', function ($query) {
                    return $query->where('tipoDoc', '03');
                })
                ->latest()
                ->get();
        }

        if ($this->tipoDocumento == 'Todos' || $this->tipoDocumento == 'Nota') {
            $notes = Note::query()
                ->with(['client', 'company', 'sucursal'])
                ->when($this->search, function ($query) {
                    return $query->where(function ($q) {
                        $q->where('serie', 'like', '%' . $this->search . '%')
                            ->orWhere('correlativo', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->filtroFechaInicio && $this->filtroFechaFin, function ($query) {
                    return $query->whereBetween('created_at', [
                        Carbon::parse($this->filtroFechaInicio)->startOfDay(),
                        Carbon::parse($this->filtroFechaFin)->endOfDay()
                    ]);
                })
                ->latest()
                ->get();
        }

        if ($this->tipoDocumento == 'Todos' || $this->tipoDocumento == 'Ticket') {
            $tickets = Ticket::query()
                ->with(['client', 'company', 'encomienda'])
                ->when($this->search, function ($query) {
                    return $query->where(function ($q) {
                        $q->where('serie', 'like', '%' . $this->search . '%')
                            ->orWhere('correlativo', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->filtroFechaInicio && $this->filtroFechaFin, function ($query) {
                    return $query->whereBetween('created_at', [
                        Carbon::parse($this->filtroFechaInicio)->startOfDay(),
                        Carbon::parse($this->filtroFechaFin)->endOfDay()
                    ]);
                })
                ->latest()
                ->get();
        }

        $totalFacturas = $invoices->sum('mtoImpVenta');
        $totalNotas = $notes->sum('mtoImpVenta');
        $totalTickets = $tickets->sum('mtoImpVenta');
        $totalGeneral = $totalFacturas + $totalTickets - $totalNotas;

        return view('livewire.report.facturacion-report', compact('invoices', 'notes', 'tickets', 'totalFacturas', 'totalNotas', 'totalTickets', 'totalGeneral'));
    }

    public function exportExcel()
    {
        try {
            $invoices = collect();
            $notes = collect();
            $tickets = collect();

            if ($this->tipoDocumento == 'Todos' || $this->tipoDocumento == 'Factura' || $this->tipoDocumento == 'Boleta') {
                $invoices = Invoice::query()
                    ->with(['client', 'company', 'sucursal'])
                    ->when($this->search, function ($query) {
                        return $query->where(function ($q) {
                            $q->where('serie', 'like', '%' . $this->search . '%')
                                ->orWhere('correlativo', 'like', '%' . $this->search . '%')
                                ->orWhereHas('client', function ($query) {
                                    $query->where('code', 'like', '%' . $this->search . '%')
                                        ->orWhere('name', 'like', '%' . $this->search . '%');
                                });
                        });
                    })
                    ->when($this->filtroFechaInicio && $this->filtroFechaFin, function ($query) {
                        return $query->whereBetween('created_at', [
                            Carbon::parse($this->filtroFechaInicio)->startOfDay(),
                            Carbon::parse($this->filtroFechaFin)->endOfDay()
                        ]);
                    })
                    ->when($this->tipoDocumento == 'Factura', function ($query) {
                        return $query->where('tipoDoc', '01');
                    })
                    ->when($this->tipoDocumento == 'Boleta', function ($query) {
                        return $query->where('tipoDoc', '03');
                    })
                    ->latest()
                    ->get();
            }

            if ($this->tipoDocumento == 'Todos' || $this->tipoDocumento == 'Nota') {
                $notes = Note::query()
                    ->with(['client', 'company', 'sucursal'])
                    ->when($this->search, function ($query) {
                        return $query->where(function ($q) {
                            $q->where('serie', 'like', '%' . $this->search . '%')
                                ->orWhere('correlativo', 'like', '%' . $this->search . '%');
                        });
                    })
                    ->when($this->filtroFechaInicio && $this->filtroFechaFin, function ($query) {
                        return $query->whereBetween('created_at', [
                            Carbon::parse($this->filtroFechaInicio)->startOfDay(),
                            Carbon::parse($this->filtroFechaFin)->endOfDay()
                        ]);
                    })
                    ->latest()
                    ->get();
            }

            if ($this->tipoDocumento == 'Todos' || $this->tipoDocumento == 'Ticket') {
                $tickets = Ticket::query()
                    ->with(['client', 'company', 'encomienda'])
                    ->when($this->search, function ($query) {
                        return $query->where(function ($q) {
                            $q->where('serie', 'like', '%' . $this->search . '%')
                                ->orWhere('correlativo', 'like', '%' . $this->search . '%');
                        });
                    })
                    ->when($this->filtroFechaInicio && $this->filtroFechaFin, function ($query) {
                        return $query->whereBetween('created_at', [
                            Carbon::parse($this->filtroFechaInicio)->startOfDay(),
                            Carbon::parse($this->filtroFechaFin)->endOfDay()
                        ]);
                    })
                    ->latest()
                    ->get();
            }

            $totalFacturas = $invoices->sum('mtoImpVenta');
            $totalNotas = $notes->sum('mtoImpVenta');
            $totalTickets = $tickets->sum('mtoImpVenta');
            $totalGeneral = $totalFacturas + $totalTickets - $totalNotas;

            return Excel::download(
                new ReportFacturacionExport($invoices, $notes, $tickets, $totalFacturas, $totalNotas, $totalTickets, $totalGeneral),
                'reporte-facturacion-' . date('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            $this->error('Error al exportar: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filtroFechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->filtroFechaFin = Carbon::now()->endOfDay()->format('Y-m-d');
        $this->tipoDocumento = 'Todos';
    }
}

