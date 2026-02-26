<?php

namespace App\Livewire\Facturacion;

use App\Models\Facturacion\Ticket;
use App\Traits\ToastTrait;
use App\Traits\UtilsTrait;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class TicketLive extends Component
{
    use ToastTrait, WithPagination, WithoutUrlPagination, UtilsTrait;
    
    public string $title = 'TICKETS DE ENVÍO';
    public string $sub_title = 'Módulo reporte de ticket';
    public int $perPage = 20;
    public $infoModal = false;
    public $pdfModal = false;
    public $pdfUrl = '';
    public $pdfTitle = '';

    public $cdr_code;
    public $cdr_description;
    public $cdr_note;
    public $errorCode;
    public $errorMessage;

    public $filtroFechaInicio;
    public $filtroFechaFin;
    public $search;
    public $FiltroFormaPagoTipo = 'Todos';
    public $formaPagos = [
        ['id' => 'Todos', 'name' => 'Todos'],
        ['id' => 'Contado', 'name' => 'Contado'],
        ['id' => 'Credito', 'name' => 'Credito'],
    ];
    
    public function mount()
    {
        $this->filtroFechaInicio = Carbon::now()->startOfDay()->format('Y-m-d H:i');
        $this->filtroFechaFin = $this->dateNow('Y-m-d H:i:s');
    }
    
    public function render()
    {
        $tickets = Ticket::query()
            ->with(['client', 'company', 'encomienda'])
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
            ->when($this->FiltroFormaPagoTipo !== 'Todos', function ($query) {
                return $query->where('formaPago_tipo', $this->FiltroFormaPagoTipo);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.facturacion.ticket-live', compact('tickets'));
    }
    
    public function clearFilters()
    {
        $this->search = '';
        $this->filtroFechaInicio = Carbon::now()->startOfDay()->format('Y-m-d H:i');
        $this->filtroFechaFin = $this->dateNow('Y-m-d H:i:s');
        $this->FiltroFormaPagoTipo = 'Todos';
    }
    
    public function showInfo(Ticket $ticket)
    {
        $this->cdr_code = $ticket->cdr_code ?? null;
        $this->cdr_description = $ticket->cdr_description ?? null;
        $this->cdr_note = $ticket->cdr_note ?? null;
        $this->errorCode = $ticket->errorCode ?? null;
        $this->errorMessage = $ticket->errorMessage ?? null;
        $this->infoModal = true;
    }
    
    public function closeInfo()
    {
        $this->infoModal = false;
        $this->reset(['cdr_code', 'cdr_description', 'cdr_note', 'errorCode', 'errorMessage']);
    }
    
    public function showPdf(Ticket $ticket, $type = 'a4')
    {
        $this->pdfUrl = $type === 'a4' 
            ? route('pdf.ticket.a4', $ticket)
            : route('pdf.ticket.80mm', $ticket);
        $this->pdfTitle = $type === 'a4' 
            ? 'Ticket A4 - ' . $ticket->serie . '-' . $ticket->correlativo
            : 'Ticket 80mm - ' . $ticket->serie . '-' . $ticket->correlativo;
        $this->pdfModal = true;
    }
    
    public function closePdf()
    {
        $this->pdfModal = false;
        $this->pdfUrl = '';
        $this->pdfTitle = '';
    }
}

