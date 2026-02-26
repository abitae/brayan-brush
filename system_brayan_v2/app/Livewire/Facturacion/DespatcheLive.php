<?php

namespace App\Livewire\Facturacion;

use App\Models\Configuration\Sucursal;
use App\Models\Facturacion\Despatche;
use App\Services\Shared\SunatServiceGlobal;
use App\Traits\LogCustom;
use App\Traits\ToastTrait;
use Carbon\Carbon;
use Greenter\Report\XmlUtils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class DespatcheLive extends Component
{
    use LogCustom, ToastTrait, WithPagination, WithoutUrlPagination;
    
    public string $title = 'GUÍA DE REMISIÓN TRANSPORTISTA';
    public string $sub_title = 'Módulo de facturación';
    public int $perPage = 10;
    public $cdr_code;
    public $cdr_description;
    public $cdr_note;
    public $errorCode;
    public $errorMessage;
    public bool $infoModal = false;
    public bool $pdfModal = false;
    public $pdfUrl = '';
    public $pdfTitle = '';
    public Despatche $despatche;
    public $filtroFechaInicio;
    public $filtroFechaFin;
    public $search;
    public $FiltroSucursalEnvio = '';
    public $FiltroSucursalDestino = '';
    public $despatches;
    public $num_despaches = 0;

    public function mount()
    {
        $this->filtroFechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00';
        $this->filtroFechaFin = Carbon::now()->endOfDay()->format('Y-m-d') . ' 23:59';
    }

    public function render()
    {
        $despaches = Despatche::query()
            ->with(['remitente', 'destinatario', 'encomienda'])
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('serie', 'like', '%' . $this->search . '%')
                        ->orWhere('correlativo', 'like', '%' . $this->search . '%')
                        ->orWhereHas('remitente', function ($subQuery) {
                            $subQuery->where('code', 'like', '%' . $this->search . '%')
                                ->orWhere('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->FiltroSucursalEnvio !== '' && $this->FiltroSucursalEnvio !== null, function ($query) {
                return $query->whereHas('encomienda', function ($subQuery) {
                    $subQuery->where('sucursal_id', $this->FiltroSucursalEnvio);
                });
            })
            ->when($this->FiltroSucursalDestino !== '' && $this->FiltroSucursalDestino !== null, function ($query) {
                return $query->whereHas('encomienda', function ($subQuery) {
                    $subQuery->where('sucursal_dest_id', $this->FiltroSucursalDestino);
                });
            })
            ->when($this->filtroFechaInicio && $this->filtroFechaFin, function ($query) {
                return $query->whereBetween('fechaEmision', [
                    Carbon::parse($this->filtroFechaInicio)->startOfDay(),
                    Carbon::parse($this->filtroFechaFin)->endOfDay()
                ]);
            })
            ->latest('id');
        $this->despatches = $despaches->get();
        $despaches = $despaches->paginate($this->perPage);
        $sucursales = Sucursal::where('isActive', true)->orderBy('name')->get();
        return view('livewire.facturacion.despatche-live', compact('despaches', 'sucursales'));
    }

    public function xmlGenerate(Despatche $despatche)
    {
        try {
            $company = $despatche->company;
            $sunat = new SunatServiceGlobal();
            $api = $sunat->getSeeApi($company);
            $despatch = $sunat->getDespatch($despatche);
            $xml = $api->getXmlSigned($despatch);
            $hash = (new XmlUtils())->getHashSign($xml);
            $despatche->xml_hash = $hash;
            $despatche->xml_path = 'xml/' . $despatche->company->ruc . '-' . $despatche->tipoDoc . '-' . $despatche->serie . '-' . $despatche->correlativo . '.xml';
            $despatche->save();
            Storage::disk('public')->put($despatche->xml_path, $xml);
            $this->success('XML generado correctamente');
        } catch (\Exception $e) {
            $this->error('Error al generar XML: ' . $e->getMessage());
        }
    }
    
    public function xmlDownload(Despatche $despatche)
    {
        if (Storage::exists($despatche->xml_path)) {
            return response()->download(storage_path('app/public/' . $despatche->xml_path));
        }
        $this->error('El archivo XML no existe');
    }
    
    public function sendXmlFile(Despatche $despatche)
    {
        try {
            $company = $despatche->company;
            $sunat = new SunatServiceGlobal();
            $api = $sunat->getSeeApi($company);
            $xml = Storage::disk('public')->get($despatche->xml_path);
            $result = $api->send($xml);
            $response = $sunat->sunatResponse($result);
            
            if ($response['success']) {
                $cdr = $response['cdrResponse'];
                $despatche->cdr_description = $cdr['description'];
                $despatche->cdr_code = $cdr['code'];
                $despatche->cdr_note = $cdr['notes'] ?? null;
                $despatche->cdr_path = 'cdr/' . $despatche->company->ruc . '-' . $despatche->tipoDoc . '-' . $despatche->serie . '-' . $despatche->correlativo . '.zip';
                $despatche->errorCode = null;
                $despatche->errorMessage = null;
                $despatche->save();
                Storage::disk('public')->put($despatche->cdr_path, base64_decode($cdr['cdrZip']));
                $this->success('Guía enviada correctamente a SUNAT');
            } else {
                $despatche->errorCode = $response['error']['code'];
                $despatche->errorMessage = $response['error']['message'];
                $despatche->save();
                $this->error('Error al enviar a SUNAT: ' . $response['error']['message']);
            }
        } catch (\Exception $e) {
            $this->error('Error al enviar XML: ' . $e->getMessage());
        }
    }
    
    public function downloadCdrFile(Despatche $despatche)
    {
        if (Storage::exists($despatche->cdr_path)) {
            return response()->download(storage_path('app/public/' . $despatche->cdr_path));
        }
        $this->error('El archivo CDR no existe');
    }
    
    public function showInfo(Despatche $despatche)
    {
        $this->despatche = $despatche;
        $this->cdr_code = $despatche->cdr_code;
        $this->cdr_description = $despatche->cdr_description;
        $this->cdr_note = $despatche->cdr_note;
        $this->errorCode = $despatche->errorCode;
        $this->errorMessage = $despatche->errorMessage;
        $this->infoModal = true;
    }
    
    public function closeInfo()
    {
        $this->infoModal = false;
        $this->reset(['cdr_code', 'cdr_description', 'cdr_note', 'errorCode', 'errorMessage']);
    }
    
    public function clearFilters()
    {
        $this->search = '';
        $this->filtroFechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00';
        $this->filtroFechaFin = Carbon::now()->endOfDay()->format('Y-m-d') . ' 23:59';
        $this->FiltroSucursalEnvio = '';
        $this->FiltroSucursalDestino = '';
    }
    
    public function showPdf(Despatche $despatche, $type = 'a4')
    {
        $this->pdfUrl = $type === 'a4'
            ? route('pdf.despache.a4', $despatche)
            : route('pdf.despache.80mm', $despatche);
        $this->pdfTitle = $type === 'a4'
            ? 'Guía de Remisión A4 - ' . $despatche->serie . '-' . $despatche->correlativo
            : 'Guía de Remisión 80mm - ' . $despatche->serie . '-' . $despatche->correlativo;
        $this->pdfModal = true;
        $this->dispatch('despatche-pdf-opening');
    }
    
    public function closePdf()
    {
        $this->pdfModal = false;
        $this->pdfUrl = '';
        $this->pdfTitle = '';
    }

    public function refreshPdf()
    {
        $this->dispatch('despatche-pdf-refreshed');
    }
}

