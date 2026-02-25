<?php

namespace App\Livewire\Facturacion;

use App\Models\Configuration\Sucursal;
use App\Models\Facturacion\Note;
use App\Services\Shared\SunatServiceGlobal;
use App\Traits\ToastTrait;
use App\Traits\UtilsTrait;
use Carbon\Carbon;
use Greenter\Report\XmlUtils;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class NoteLive extends Component
{
    use ToastTrait, WithPagination, WithoutUrlPagination, UtilsTrait;

    public string $title = 'NOTAS DE CRÉDITO';
    public string $sub_title = 'Módulo de notas de crédito';
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
    public $FiltroSucursalEnvio = '';

    public function mount()
    {
        $this->filtroFechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00';
        $this->filtroFechaFin = Carbon::now()->endOfDay()->format('Y-m-d') . ' 23:59';
    }

    public function render()
    {
        $notes = Note::query()
            ->with(['client', 'company', 'sucursal'])
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
            ->when($this->FiltroSucursalEnvio !== '' && $this->FiltroSucursalEnvio !== null, function ($query) {
                return $query->where('sucursal_id', $this->FiltroSucursalEnvio);
            })
            ->latest('id')
            ->paginate($this->perPage);

        $sucursales = Sucursal::where('isActive', true)->orderBy('name')->get();

        return view('livewire.facturacion.note-live', compact('notes', 'sucursales'));
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filtroFechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00';
        $this->filtroFechaFin = Carbon::now()->endOfDay()->format('Y-m-d') . ' 23:59';
        $this->FiltroSucursalEnvio = '';
    }
    
    public function xmlGenerate(Note $note)
    {
        try {
            $company = $note->company;
            $sunat = new SunatServiceGlobal();
            $see = $sunat->getSee($company);
            $invoce = $sunat->getNote($note);
            $xml = $see->getXmlSigned($invoce);
            $hash = (new XmlUtils())->getHashSign($xml);
            $note->xml_hash = $hash;
            $note->xml_path = 'xml/' . $note->company->ruc . '-' . $note->tipoDoc . '-' . $note->serie . '-' . $note->correlativo . '.xml';
            $note->save();
            Storage::disk('public')->put($note->xml_path, $xml);
            $this->success('XML generado correctamente');
        } catch (\Exception $e) {
            $this->error('Error al generar XML: ' . $e->getMessage());
        }
    }
    
    public function xmlDownload(Note $note)
    {
        if (Storage::exists($note->xml_path)) {
            return response()->download(storage_path('app/public/' . $note->xml_path));
        }
        $this->error('El archivo XML no existe');
    }
    
    public function sendXmlFile(Note $note)
    {
        try {
            $company = $note->company;
            $sunat = new SunatServiceGlobal();
            $see = $sunat->getSee($company);
            $xml = Storage::disk('public')->get($note->xml_path);
            $result = $see->sendXmlFile($xml);
            $response = $sunat->sunatResponse($result);
            
            if ($response['success']) {
                $cdr = $response['cdrResponse'];
                $note->cdr_description = $cdr['description'];
                $note->cdr_code = $cdr['code'];
                $note->cdr_note = $cdr['notes'] ?? null;
                $note->cdr_path = 'cdr/' . 'R-' . $note->company->ruc . '-' . $note->tipoDoc . '-' . $note->serie . '-' . $note->correlativo . '.zip';
                $note->errorCode = null;
                $note->errorMessage = null;
                $note->save();
                Storage::disk('public')->put($note->cdr_path, base64_decode($cdr['cdrZip']));
                $this->success('Comprobante enviado correctamente a SUNAT');
            } else {
                $note->errorCode = $response['error']['code'];
                $note->errorMessage = $response['error']['message'];
                $note->save();
                $this->error('Error al enviar a SUNAT: ' . $response['error']['message']);
            }
        } catch (\Exception $e) {
            $this->error('Error al enviar XML: ' . $e->getMessage());
        }
    }
    
    public function downloadCdrFile(Note $note)
    {
        if (Storage::exists($note->cdr_path)) {
            return response()->download(storage_path('app/public/' . $note->cdr_path));
        }
        $this->error('El archivo CDR no existe');
    }
    
    public function showInfo(Note $note)
    {
        $this->cdr_code = $note->cdr_code;
        $this->cdr_description = $note->cdr_description;
        $this->cdr_note = $note->cdr_note;
        $this->errorCode = $note->errorCode;
        $this->errorMessage = $note->errorMessage;
        $this->infoModal = true;
    }
    
    public function closeInfo()
    {
        $this->infoModal = false;
        $this->reset(['cdr_code', 'cdr_description', 'cdr_note', 'errorCode', 'errorMessage']);
    }
    
    public function showPdf(Note $note, $type = 'a4')
    {
        $this->pdfUrl = $type === 'a4'
            ? route('pdf.note.a4', $note)
            : route('pdf.note.80mm', $note);
        $this->pdfTitle = $type === 'a4'
            ? 'Nota de Crédito A4 - ' . $note->serie . '-' . $note->correlativo
            : 'Nota de Crédito 80mm - ' . $note->serie . '-' . $note->correlativo;
        $this->pdfModal = true;
        $this->dispatch('note-pdf-opening');
    }
    
    public function closePdf()
    {
        $this->pdfModal = false;
        $this->pdfUrl = '';
        $this->pdfTitle = '';
    }

    public function refreshPdf()
    {
        $this->dispatch('note-pdf-refreshed');
    }
}

