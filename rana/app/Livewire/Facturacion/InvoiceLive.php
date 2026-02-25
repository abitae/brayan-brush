<?php

namespace App\Livewire\Facturacion;

use App\Models\Configuration\Sucursal;
use App\Models\Facturacion\Invoice;
use App\Services\Shared\SunatServiceGlobal;
use App\Traits\ToastTrait;
use App\Traits\UtilsTrait;
use Carbon\Carbon;
use Greenter\Report\XmlUtils;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class InvoiceLive extends Component
{
    use ToastTrait, WithPagination, WithoutUrlPagination, UtilsTrait;

    public string $title = 'BOLETAS Y FACTURAS';
    public string $sub_title = 'Módulo de facturación electrónica';
    public int $perPage = 20;
    public $infoModal = false;
    public $pdfModal = false;
    public $pdfUrl = '';
    public $pdfTitle = '';
    public $num_invoices = 0;
    public $cdr_code;
    public $cdr_description;
    public $cdr_note;
    public $errorCode;
    public $errorMessage;

    public $filtroFechaInicio;
    public $filtroFechaFin;
    public $search;
    public $FiltroFormaPagoTipo = 'Todos';
    public $FiltroSucursalEnvio = '';
    public $FiltroSucursalDestino = '';
    public $formaPagos = [
        ['id' => 'Todos', 'name' => 'Todos'],
        ['id' => 'Contado', 'name' => 'Contado'],
        ['id' => 'Credito', 'name' => 'Credito'],
    ];

    public function mount()
    {
        $this->filtroFechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00';
        $this->filtroFechaFin = Carbon::now()->endOfDay()->format('Y-m-d') . ' 23:59';
    }

    public function render()
    {
        $invoices = Invoice::query()
            ->with(['client', 'company', 'sucursal', 'encomienda'])
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('serie', 'like', '%' . $this->search . '%')
                        ->orWhere('correlativo', 'like', '%' . $this->search . '%')
                        ->orWhereHas('client', function ($subQuery) {
                            $subQuery->where('code', 'like', '%' . $this->search . '%')
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
            ->when($this->FiltroSucursalEnvio !== '' && $this->FiltroSucursalEnvio !== null, function ($query) {
                return $query->where(function ($q) {
                    $q->where('sucursal_id', $this->FiltroSucursalEnvio)
                        ->orWhereHas('encomienda', function ($subQuery) {
                            $subQuery->where('sucursal_id', $this->FiltroSucursalEnvio);
                        });
                });
            })
            ->when($this->FiltroSucursalDestino !== '' && $this->FiltroSucursalDestino !== null, function ($query) {
                return $query->whereHas('encomienda', function ($q) {
                    $q->where('sucursal_dest_id', $this->FiltroSucursalDestino);
                });
            })
            ->latest('id')
            ->paginate($this->perPage);

        $sucursales = Sucursal::where('isActive', true)->orderBy('name')->get();

        return view('livewire.facturacion.invoice-live', compact('invoices', 'sucursales'));
    }
    
    public function xmlGenerate(Invoice $invoice)
    {
        try {
            $company = $invoice->company;
            $sunat = new SunatServiceGlobal();
            $see = $sunat->getSee($company);
            $invoce = $sunat->getInvoce($invoice);
            $xml = $see->getXmlSigned($invoce);
            $hash = (new XmlUtils())->getHashSign($xml);
            $invoice->xml_hash = $hash;
            $invoice->xml_path = 'xml/' . $invoice->company->ruc . '-' . $invoice->tipoDoc . '-' . $invoice->serie . '-' . $invoice->correlativo . '.xml';
            $invoice->save();
            Storage::disk('public')->put($invoice->xml_path, $xml);
            $this->success('XML generado correctamente');
        } catch (\Exception $e) {
            $this->error('Error al generar XML: ' . $e->getMessage());
        }
    }
    
    public function xmlDownload(Invoice $invoice)
    {
        if (Storage::exists($invoice->xml_path)) {
            return response()->download(storage_path('app/public/' . $invoice->xml_path));
        }
        $this->error('El archivo XML no existe');
    }
    
    public function sendXmlFile(Invoice $invoice)
    {
        try {
            $company = $invoice->company;
            $sunat = new SunatServiceGlobal();
            $see = $sunat->getSee($company);
            
            if ($invoice->xml_path) {
                $xml = Storage::disk('public')->get($invoice->xml_path);
            } else {
                $invoce = $sunat->getInvoce($invoice);
                $xml = $see->getXmlSigned($invoce);
                $hash = (new XmlUtils())->getHashSign($xml);
                $invoice->xml_hash = $hash;
                $invoice->xml_path = 'xml/' . $invoice->company->ruc . '-' . $invoice->tipoDoc . '-' . $invoice->serie . '-' . $invoice->correlativo . '.xml';
                Storage::disk('public')->put($invoice->xml_path, $xml);
            }
            
            $result = $see->send($xml);
            $response = $sunat->sunatResponse($result);
            
            if ($response['success']) {
                $cdr = $response['cdrResponse'];
                $invoice->cdr_code = $cdr['code'];
                $invoice->cdr_description = $cdr['description'];
                $invoice->cdr_note = $cdr['notes'] ?? null;
                $invoice->cdr_path = 'cdr/' . $invoice->company->ruc . '-' . $invoice->tipoDoc . '-' . $invoice->serie . '-' . $invoice->correlativo . '.zip';
                $invoice->errorCode = null;
                $invoice->errorMessage = null;
                Storage::disk('public')->put($invoice->cdr_path, base64_decode($cdr['cdrZip']));
                $invoice->save();
                $this->success('Comprobante enviado correctamente a SUNAT');
            } else {
                $invoice->errorCode = $response['error']['code'];
                $invoice->errorMessage = $response['error']['message'];
                $invoice->save();
                $this->error('Error al enviar a SUNAT: ' . $response['error']['message']);
            }
        } catch (\Exception $e) {
            $this->error('Error al enviar XML: ' . $e->getMessage());
        }
    }
    
    public function showInfo(Invoice $invoice)
    {
        $this->cdr_code = $invoice->cdr_code;
        $this->cdr_description = $invoice->cdr_description;
        $this->cdr_note = $invoice->cdr_note;
        $this->errorCode = $invoice->errorCode;
        $this->errorMessage = $invoice->errorMessage;
        $this->infoModal = true;
    }
    
    public function closeInfo()
    {
        $this->infoModal = false;
        $this->reset(['cdr_code', 'cdr_description', 'cdr_note', 'errorCode', 'errorMessage']);
    }
    
    public function downloadCdrFile(Invoice $invoice)
    {
        if (Storage::exists($invoice->cdr_path)) {
            return response()->download(storage_path('app/public/' . $invoice->cdr_path));
        }
        $this->error('El archivo CDR no existe');
    }
    
    public function statusInvoice(Invoice $invoice)
    {
        $this->showInfo($invoice);
    }
    
    public function createNote(Invoice $invoice)
    {
        return redirect()->route('facturacion.note.create', $invoice->id);
    }
    
    public function clearFilters()
    {
        $this->search = '';
        $this->filtroFechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00';
        $this->filtroFechaFin = Carbon::now()->endOfDay()->format('Y-m-d') . ' 23:59';
        $this->FiltroFormaPagoTipo = 'Todos';
        $this->FiltroSucursalEnvio = '';
        $this->FiltroSucursalDestino = '';
    }
    
    public function showPdf(Invoice $invoice, $type = 'a4')
    {
        $this->pdfUrl = $type === 'a4'
            ? route('pdf.invoice.a4', $invoice)
            : route('pdf.invoice.80mm', $invoice);
        $this->pdfTitle = $type === 'a4'
            ? 'Factura A4 - ' . $invoice->serie . '-' . $invoice->correlativo
            : 'Factura 80mm - ' . $invoice->serie . '-' . $invoice->correlativo;
        $this->pdfModal = true;
        $this->dispatch('invoice-pdf-opening');
    }
    
    public function closePdf()
    {
        $this->pdfModal = false;
        $this->pdfUrl = '';
        $this->pdfTitle = '';
    }

    public function refreshPdf()
    {
        $this->dispatch('invoice-pdf-refreshed');
    }
}

