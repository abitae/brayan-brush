<?php

namespace App\Traits;

use App\Models\Configuration\Company;
use App\Models\Facturacion\Despatche;
use App\Models\Facturacion\DespatcheDetail;
use App\Models\Facturacion\Invoice;
use App\Models\Facturacion\Ticket;
use App\Models\Package\Encomienda;
use Carbon\Carbon;
use Luecano\NumeroALetras\NumeroALetras;

trait createDocumentoTrait
{
    // crear ticket
    public function createTicket(Encomienda $encomienda): Ticket
    {
        $company = Company::first();
        $code = $encomienda->sucursal_remitente->code;
        $correlativo = Ticket::where('serie', $code)->count() + 1;
        $ticket = Ticket::create([
            'encomienda_id' => $encomienda->id,
            'tipoDoc' => '00',
            'tipoOperacion' => '00',
            'serie' => $code,
            'correlativo' => $correlativo,
            'fechaEmision' => Carbon::now(),
            'formaPago_moneda' => 'PEN',
            'formaPago_tipo' => '01',
            'tipoMoneda' => 'PEN',
            'company_id' => $company->id,
            'client_id' => $encomienda->customer_id,
            'mtoOperGravadas' => $encomienda->monto,
            'mtoIGV' => $encomienda->monto * 0.18,
            'totalImpuestos' => $encomienda->monto * 0.18,
            'valorVenta' => $encomienda->monto,
            'subTotal' => $encomienda->monto,
            'mtoImpVenta' => $encomienda->monto,
            'monto_descuento' => 0,
            'motivo_descuento' => '',
        ]);
        return $ticket;
    }
    public function createInvoice(Encomienda $encomienda): Invoice
    {
        $montoTotalIncIGV = $encomienda->paquetes->sum('sub_total');
        $mtoOperGravadas = round($montoTotalIncIGV / 1.18, 2);
        $igv = $montoTotalIncIGV - $mtoOperGravadas;
        $formatter = new NumeroALetras();
        $monto_letras = $formatter->toInvoice($montoTotalIncIGV, 2, 'SOLES');
        $company = Company::first();
        $legends[] = [
            'code' => '1000',
            'value' => $monto_letras,
        ];
        if ($encomienda->tipo_comprobante === 'BOLETA') {
            $code = $encomienda->sucursal_remitente->serieBoleta;
            $tipoDoc = '03';
            $tipoOperacion = '0101';
            $correlativo = Invoice::where('tipoDoc', $tipoDoc)->where('serie', $code)->count() + 1;
        } else {
            $code = $encomienda->sucursal_remitente->serieFactura;
            $tipoDoc = '01';
            $correlativo = Invoice::where('tipoDoc', $tipoDoc)->where('serie', $code)->count() + 1;
            if ($montoTotalIncIGV >= 400) {
                $tipoOperacion = '1001';
                $codBienDetraccion = '027';
                $codMedioPago = '001';
                $ctaBanco = $company->ctaBanco;
                $setPercent = 4;
                $setMount = $montoTotalIncIGV * 0.04;
                $legends[] = [
                    'code' => '2006',
                    'value' => 'Leyenda "Operación sujeta a detracción"',
                ];
            } else {
                $tipoOperacion = '0101';
            }
        }
        $legends = json_encode($legends);

        $invoice = Invoice::create([
            'encomienda_id' => $encomienda->id,
            'sucursal_id' => $encomienda->sucursal_id,
            'company_id' => $company->id,
            'client_id' => $encomienda->customer_fact_id,
            'tipoDoc' => $tipoDoc,
            'tipoOperacion' => $tipoOperacion,
            'codBienDetraccion' => $codBienDetraccion ?? null,
            'codMedioPago' => $codMedioPago ?? null,
            'ctaBanco' => $ctaBanco ?? null,
            'setPercent' => $setPercent ?? null,
            'setMount' => $setMount ?? null,
            'serie' => $code,
            'correlativo' => $correlativo,
            'fechaEmision' => Carbon::now(),
            'formaPago_moneda' => 'PEN',
            'formaPago_tipo' => '01',
            'tipoMoneda' => 'PEN',
            'mtoOperGravadas' => $mtoOperGravadas,
            'mtoIGV' => $igv,
            'totalImpuestos' => $igv,
            'valorVenta' => $mtoOperGravadas,
            'subTotal' => $montoTotalIncIGV,
            'mtoImpVenta' => $montoTotalIncIGV,
            'monto_letras' => $monto_letras,
            'observacion' => $encomienda->observation,

            'docAdjuntos' => $encomienda->docsTraslado,

            'legends' => $legends,
        ]);
        foreach ($encomienda->paquetes as $paquete) {
            $mtoValorUnitario = round($paquete->amount / 1.18, 2);
            $invoice->details()->create([
                'invoice_id' => $invoice->id,
                'tipAfeIgv' => '10',
                'codProducto' => $paquete->id,
                'unidad' => $paquete->und_medida,
                'descripcion' => strtoupper('SERVICIO TRASLADO ' . $paquete->description),
                'cantidad' => $paquete->cantidad,
                'mtoValorUnitario' => $mtoValorUnitario,
                'mtoValorVenta' => $mtoValorUnitario * $paquete->cantidad,
                'mtoBaseIgv' => $mtoValorUnitario * $paquete->cantidad,
                'porcentajeIgv' => 18,
                'igv' => ($paquete->amount - $mtoValorUnitario) * $paquete->cantidad,
                'totalImpuestos' => ($paquete->amount - $mtoValorUnitario) * $paquete->cantidad,
                'mtoPrecioUnitario' => $paquete->amount,
            ]);
        }
        return $invoice;
    }
    public function createGuiTrans(Encomienda $encomienda): Despatche
    {
        $company = Company::first();
        $correlativo = Despatche::count() + 1;
        $montoTotalIncIGV = $encomienda->paquetes->sum('sub_total');
        $mtoOperGravadas = round($montoTotalIncIGV / 1.18, 2);
        $igv = $montoTotalIncIGV - $mtoOperGravadas;
        $formatter = new NumeroALetras();
        $monto_letras = $formatter->toInvoice($montoTotalIncIGV, 2, 'SOLES');
        $despatch = Despatche::create([
            'encomienda_id' => $encomienda->id,
            'tipoDoc' => '31',
            'serie' => 'V001',
            'correlativo' => $correlativo,
            'fechaEmision' => Carbon::now(),
            'company_id' => $company->id,
            'flete_id' => $encomienda->remitente->id,
            'remitente_id' => $encomienda->remitente->id,
            'destinatario_id' => $encomienda->destinatario->id,
            'codTraslado' => '01',
            'modTraslado' => '02',

            'docsTraslado' => $encomienda->docsTraslado,

            'fecTraslado' => Carbon::now(),
            'pesoTotal' => $encomienda->paquetes->sum('peso'),
            'undPesoTotal' => 'KGM',
            'llegada_ubigueo' => $encomienda->sucursal_destinatario->ubigeo ?? '150203',
            'llegada_direccion' => $encomienda->sucursal_destinatario->address ?? 'Av. Villa Nueva 221',
            'partida_ubigueo' => $encomienda->sucursal_remitente->ubigeo ?? '150101',
            'partida_direccion' => $encomienda->sucursal_remitente->address ?? 'Av. Villa Nueva 221',
            'chofer_tipoDoc' => $encomienda->transportista->type_code ?? '03',
            'chofer_nroDoc' => $encomienda->transportista->dni ?? '4364990',
            'chofer_licencia' => $encomienda->transportista->licencia ?? '1234567890',
            'chofer_nombres' => $encomienda->transportista->name ?? 'Juan',
            'chofer_apellidos' => $encomienda->transportista->name,
            'vehiculo_placa' => $encomienda->vehiculo->name,
            'monto_letras' => $monto_letras,
            'setPercent' => 4,
            'setMount' => $montoTotalIncIGV * 0.04,
            'mtoIGV' => $igv,
            'valorVenta' => $mtoOperGravadas,
            'mtoImpVenta' => $montoTotalIncIGV,

        ]);
        $encomienda->doc_guia = $despatch->id;
        $encomienda->save();
        foreach ($encomienda->paquetes as $paquete) {
            $this->createDespatcheDetail($despatch->id, $paquete);
        }
        return $despatch;
    }
    private function createDespatcheDetail($despatcheId, $paquete)
    {
        $mtoValorUnitario = round($paquete->amount / 1.18, 2);
        DespatcheDetail::create([
            'despatche_id' => $despatcheId,
            'tipAfeIgv' => '10',
            'codProducto' => $paquete->id,
            'unidad' => $paquete->und_medida,
            'descripcion' => strtoupper('SERVICIO TRASLADO ' . $paquete->description),
            'cantidad' => $paquete->cantidad,
            'mtoValorUnitario' => $mtoValorUnitario,
            'mtoValorVenta' => $mtoValorUnitario * $paquete->cantidad,
            'mtoBaseIgv' => $mtoValorUnitario * $paquete->cantidad,
            'porcentajeIgv' => 18,
            'igv' => ($paquete->amount - $mtoValorUnitario) * $paquete->cantidad,
            'totalImpuestos' => ($paquete->amount - $mtoValorUnitario) * $paquete->cantidad,
            'mtoPrecioUnitario' => $paquete->amount,
        ]);
    }
}
