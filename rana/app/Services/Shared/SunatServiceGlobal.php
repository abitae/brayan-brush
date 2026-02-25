<?php

namespace App\Services\Shared;

use App\Models\Configuration\Vehiculo;
use DateTime;
use Greenter\Api;
use Greenter\Model\Client\Client;
use Greenter\Model\Despatch\AdditionalDoc;
use Greenter\Model\Despatch\Transportist;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\FormaPagos\FormaPagoCredito;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\Storage;

class SunatServiceGlobal
{

    public function getSee($company)
    {
        $see = new See();
        $see->setCertificate(Storage::get($company->cert_path));
        $see->setService($company->production ? SunatEndpoints::FE_PRODUCCION : SunatEndpoints::FE_BETA);
        $see->setClaveSOL($company->ruc, $company->sol_user, $company->sol_pass);
        return $see;
    }

    public function getSeeApi($company)
    {
        $api = new Api(
            $company->production ? [
                'auth' => 'https://api-seguridad.sunat.gob.pe/v1',
                'cpe'  => 'https://api-cpe.sunat.gob.pe/v1',
            ] : [
                'auth' => 'https://gre-test.nubefact.com/v1',
                'cpe'  => 'https://gre-test.nubefact.com/v1',
            ]
        );
        $api->setBuilderOptions([
            'strict_variables' => true,
            'optimization'     => 0,
            'debug'            => true,
            'cache'            => false,
        ])->setApiCredentials(
            $company->production ? $company->client_id : "test-85e5b0ae-255c-4891-a595-0b98c65c9854",
            $company->production ? $company->client_secret : "test-Hty/M6QshYvPgItX2P0+Kw=="
        )->setClaveSOL(
            $company->ruc,
            $company->production ? $company->sol_user : 'MODDATOS',
            $company->production ? $company->sol_pass : 'MODDATOS'
        )->setCertificate(
            Storage::get($company->cert_path)
        );
        return $api;
    }

    public function getInvoce($data)
    {
        $invoice = new \Greenter\Model\Sale\Invoice();
        $invoice->setUblVersion($data->ublVersion ?? '2.1');
        $invoice->setFecVencimiento(new DateTime($data->fecVencimiento) ?? null);
        $invoice->setTipoOperacion($data->tipoOperacion ?? null); //Tipo operacion (Catálogo 51).
        $invoice->setTipoDoc($data->tipoDoc ?? null);
        $invoice->setSerie($data->serie ?? null);
        $invoice->setCorrelativo($data->correlativo ?? null);
        $invoice->setFechaEmision(new DateTime($data->fechaEmision) ?? null);
        $invoice->setFormaPago($data->formaPago_tipo == 'Contado' ? new FormaPagoContado() : new FormaPagoCredito($data->mtoImpVenta, 'PEN'));
        $invoice->setTipoMoneda($data->tipoMoneda ?? 'PEN');
        $invoice->setMtoOperGravadas($data->mtoOperGravadas);
        $invoice->setMtoOperExoneradas($data->mtoOperExoneradas);
        $invoice->setMtoOperInafectas($data->mtoOperInafecto);
        $invoice->setMtoOperExportacion($data->mtoOperExportacion);
        $invoice->setMtoOperGratuitas($data->mtoOperGratuitas);
        $invoice->setMtoIGV($data->mtoIGV);
        $invoice->setMtoIGVGratuitas($data->mtoIGVGratuitas);
        $invoice->setIcbper($data->icbper);
        $invoice->setTotalImpuestos($data->totalImpuestos);
        $invoice->setValorVenta($data->valorVenta);
        $invoice->setSubTotal($data->subTotal);
        $invoice->setMtoImpVenta($data->mtoImpVenta);
        $invoice->setRedondeo($data->redondeo);
        $invoice->setObservacion($data->observacion ?? null);
        $invoice->setCompany($this->getCompany($data));
        $invoice->setClient($this->getClient($data->client));
        if ($data->tipoOperacion == '1001' && $data->mtoOperGravadas >= 400) {
            $invoice->setDetraccion($this->getDetraccion($data) ?? null);
        }
        $invoice->setDetails($this->getDetails($data->details));
        $invoice->setLegends($this->getLegends($data->legends));
        return $invoice;
    }

    public function getCompany($data): \Greenter\Model\Company\Company
    {

        $address = (new \Greenter\Model\Company\Address())
            ->setUbigueo($data->sucursal->ubigeo)
            ->setCodigoPais($data->sucursal->codigoPais ?? 'PE')
            ->setDepartamento($data->sucursal->departamento ?? 'LIMA')
            ->setProvincia($data->sucursal->provincia ?? 'LIMA')
            ->setDistrito($data->sucursal->distrito ?? 'LIMA')
            ->setDireccion($data->sucursal->address ?? 'AV. LOS ALISOS 123')
            ->setUrbanizacion($data->sucursal->urbanizacion ?? 'LIMA')
            ->setCodLocal($data->sucursal->codeSunat ?? '0000');
        return (new \Greenter\Model\Company\Company())
            ->setRuc($data->company->ruc)
            ->setRazonSocial($data->company->razonSocial)
            ->setNombreComercial($data->company->nombreComercial)
            ->setAddress($address);
    }

    public function getClient($client): \Greenter\Model\Client\Client
    {
        $address = (new \Greenter\Model\Company\Address())
            ->setUbigueo($client->ubigeo)
            ->setCodigoPais($client->codigoPais ?? 'PE')
            ->setDepartamento($client->departamento)
            ->setProvincia($client->provincia)
            ->setDistrito($client->distrito)
            ->setDireccion($client->address);
        return (new \Greenter\Model\Client\Client())
            ->setTipoDoc($client->type_code)
            ->setNumDoc($client->code)
            ->setRznSocial($client->name)
            ->setAddress($address);
    }

    public function getDetails($details): array
    {
        $items = [];
        foreach ($details as $detail) {
            $detail = (object) $detail;
            $item   = (new \Greenter\Model\Sale\SaleDetail())
                ->setTipAfeIgv($detail->tipAfeIgv)
                ->setCodProducto($detail->codProducto)
                ->setUnidad($detail->unidad)
                ->setDescripcion($detail->descripcion)
                ->setCantidad($detail->cantidad)
                ->setMtoValorUnitario($detail->mtoValorUnitario)
                ->setMtoValorVenta($detail->mtoValorVenta)
                ->setMtoBaseIgv($detail->mtoBaseIgv)
                ->setPorcentajeIgv($detail->porcentajeIgv)
                ->setIgv($detail->igv)
                ->setFactorIcbper($detail->factorIcbper)
                ->setIcbper($detail->icbper)
                ->setTotalImpuestos($detail->totalImpuestos)
                ->setMtoPrecioUnitario($detail->mtoPrecioUnitario);
            $items[] = $item;
        }
        return $items;
    }
    public function getLegends($legends): array
    {
        $legends = json_decode($legends);
        $items   = [];
        foreach ($legends as $legend) {
            $legend = $legend;
            $item   = (new \Greenter\Model\Sale\Legend())
                ->setCode($legend->code)
                ->setValue($legend->value);
            $items[] = $item;
        }
        return $items;
    }
    public function getDetraccion($data): \Greenter\Model\Sale\Detraction
    {

        return (new \Greenter\Model\Sale\Detraction())
            ->setCodBienDetraccion($data->codBienDetraccion)
            ->setCodMedioPago($data->codMedioPago)
            ->setCtaBanco($data->company->ctaBanco)
            ->setPercent($data->setPercent ?? 12)
            ->setMount($data->setMount ?? 47.20);
    }

    public function getNote($note): \Greenter\Model\Sale\Note
    {
        return (new \Greenter\Model\Sale\Note())
            ->setUblVersion($note->ublVersion ?? '2.1')
            ->setTipoDoc($note->tipoDoc ?? '07')
            ->setSerie($note->serie ?? 'FF01')
            ->setCorrelativo($note->correlativo ?? '123')
            ->setFechaEmision(new DateTime($note->fechaEmision) ?? null)
            ->setTipDocAfectado($note->tipoDocAfectado ?? '01')
            ->setNumDocfectado($note->numDocfectado ?? 'F001-1')
            ->setCodMotivo($note->codMotivo ?? '01')
            ->setDesMotivo($note->desMotivo ?? 'Anulacion de la operacion')
            ->setTipoMoneda($note->tipoMoneda ?? 'PEN')
            ->setCompany($this->getCompany($note))
            ->setClient($this->getClient($note->client))
            ->setMtoOperGravadas($note->mtoOperGravadas)
            ->setTotalImpuestos($note->mtoIGV)
            ->setMtoIGV($note->mtoIGV)
            ->setMtoImpVenta($note->mtoImpVenta)
            ->setDetails($this->getDetails($note->details))
            ->setLegends($this->getLegends($note->legends));
    }

    public function getDespatch($guiaT): \Greenter\Model\Despatch\Despatch
    {

        $pagaflete = new Client();
        $pagaflete->setTipoDoc($guiaT->flete->type_code ?? null)
            ->setNumDoc($guiaT->flete->code ?? null)
            ->setRznSocial(str_replace('&', '&amp;', $guiaT->flete->name ?? null));
        if ($guiaT->docTraslado) {
            $item = new AdditionalDoc();
            $item->setTipo("01")
                ->setTipoDesc("Factura")
                ->setNro($guiaT->docTraslado)
                ->setEmisor($guiaT->company->ruc);
            $relDoc[]     = $item;
        }
        $destinatario = new Client();
        $destinatario->setTipoDoc($guiaT->destinatario->type_code ?? null)
            ->setNumDoc($guiaT->destinatario->code ?? null)
            ->setRznSocial(str_replace('&', '&amp;', $guiaT->destinatario->name ?? null));
        $despatch = (new \Greenter\Model\Despatch\Despatch());
        $despatch->setVersion('2022')
            ->setTipoDoc($guiaT->tipoDoc)
            ->setSerie($guiaT->serie)
            ->setCorrelativo($guiaT->correlativo)
            ->setFechaEmision(new DateTime($guiaT->fechaEmision));
        $despatch->setPagaFlete($pagaflete)
            ->setCompany($this->getGRECompany($guiaT->company))
            ->setDestinatario($destinatario)
            ->setEnvio($this->getEnvio($guiaT))
            ->setObservacion('glosa');
        if ($guiaT->docTraslado) {
            $despatch->setAddDocs($relDoc);
        }
        $despatch->setDetails($this->getDespatchDetail($guiaT->details));
        return $despatch;
    }

    public function getGRECompany($company): \Greenter\Model\Company\Company
    {
        return (new \Greenter\Model\Company\Company())
            ->setRuc($company->ruc)
            ->setRazonSocial(str_replace('&', '&amp;', $company->razonSocial));
    }

    public function getEnvio($guiaT)
    {
        //dd($guiaT);
        $indicadores[] = "SUNAT_Envio_IndicadorPagadorFlete_Remitente";
        $transp        = new Transportist();
        $transp->setTipoDoc('6')
            ->setNumDoc($guiaT->company->ruc)
            ->setRznSocial($guiaT->company->razonSocial)
            ->setNroMtc($guiaT->company->nroMtc ?? "1553682CNG");
        $remitente = new Client();
        $remitente->setTipoDoc($guiaT->remitente->type_code)
            ->setNumDoc($guiaT->remitente->code)
            ->setRznSocial($guiaT->remitente->name);
        return (new \Greenter\Model\Despatch\Shipment())
            ->setCodTraslado($guiaT->codTraslado) //catalogo 20 sunat
            ->setModTraslado($guiaT->modTraslado) //catalogo 18 sunat
            ->setFecTraslado(new \DateTime($guiaT->created_at))
            ->setPesoTotal(100)
            ->setUndPesoTotal($guiaT->undPesoTotal)
            ->setTransportista($transp)
            ->setVehiculo($this->getVehiculos($guiaT))
            ->setChoferes($this->getChoferes($guiaT))
            ->setIndicador($indicadores)
            ->setLlegada(new \Greenter\Model\Despatch\Direction($guiaT->llegada_ubigueo, $guiaT->llegada_direccion))
            ->setPartida(new \Greenter\Model\Despatch\Direction($guiaT->partida_ubigueo, $guiaT->partida_direccion))
            ->setRemitente($remitente);
    }

    public function getTransportista(): \Greenter\Model\Despatch\Transportist
    {
        return (new \Greenter\Model\Despatch\Transportist())
            ->setTipoDoc('6')
            ->setNumDoc('20123456789')
            ->setRznSocial('EMPRESA SAC')
            ->setNroMtc('0001');
    }

    public function getDespatchDetail($details): array
    {
        $items = [];
        foreach ($details as $detail) {
            $detail = (object) $detail;
            $item = (new \Greenter\Model\Despatch\DespatchDetail())
                ->setCantidad($detail->cantidad)
                ->setUnidad($detail->unidad)
                ->setDescripcion($detail->descripcion)
                ->setCodigo($detail->codProducto);
            $items[] = $item;
        }

        return $items;
    }

    public function getVehiculos($guiaT): \Greenter\Model\Despatch\Vehicle
    {
        $vehiculos = collect([
            ['placa' => $guiaT->vehiculo_placa],
        ]);

        $secundarios = $vehiculos->slice(1)->map(function ($item) {
            return (new \Greenter\Model\Despatch\Vehicle())->setPlaca($item['placa']);
        })->toArray();

        return (new \Greenter\Model\Despatch\Vehicle())
            ->setPlaca($vehiculos->first()['placa'])
            ->setSecundarios($secundarios);
    }

    public function getChoferes($guiaT): array
    {
        $choferes = collect([
            ['tipoDoc' => $guiaT->chofer_tipoDoc, 'numDoc' => $guiaT->chofer_nroDoc, 'licencia' => $guiaT->chofer_licencia, 'nombre' => $guiaT->chofer_nombres, 'apellidos' => $guiaT->chofer_apellidos],
        ]);

        $drivers = $choferes->map(function ($item, $key) {
            return (new \Greenter\Model\Despatch\Driver())
                ->setTipo($key === 0 ? 'Principal' : 'Secundario')
                ->setTipoDoc($item['tipoDoc'])
                ->setNroDoc($item['numDoc'])
                ->setLicencia($item['licencia'])
                ->setNombres($item['nombre'])
                ->setApellidos($item['apellidos']);
        })->toArray();

        return $drivers;
    }

    public function sunatResponse($result)
    {
        $response['success'] = $result->isSuccess();
        if (! $response['success']) {
            $response['error'] = [
                'code'    => $result->getError()->getCode(),
                'message' => $result->getError()->getMessage(),
            ];
            return $response;
        }

        $cdr = $result->getCdrResponse();

        $response['cdrResponse'] = [
            'code'        => (int) $cdr->getCode(),
            'description' => $cdr->getDescription(),
            'notes'       => $cdr->getNotes(),
            'cdrZip'      => base64_encode($result->getCdrZip()),
        ];

        return $response;
    }
    public function setResumenDiario($invoices)
    {
        $details = [];
        foreach ($invoices as $invoice) {
            $detail = new SummaryDetail();
            $detail->setTipoDoc('03')
                ->setSerieNro($invoice->serie . '-' . $invoice->correlativo)
                ->setEstado('3')
                ->setClienteTipo($invoice->client->type_code)
                ->setClienteNro($invoice->client->code)
                ->setTotal($invoice->mtoImpVenta)
                ->setMtoOperGravadas($invoice->mtoOperGravadas)
                ->setMtoIGV($invoice->mtoIGV);
            $details[] = $detail;
        }
        $sum = new Summary();
        $sum->setFecGeneracion(new DateTime($invoices->first()->fechaEmision))
            ->setFecResumen(new DateTime('-1days'))
            ->setCorrelativo('001')
            ->setCompany($this->getCompany($invoices->first()->company))
            ->setDetails($details);
        return $sum;
    }
}
