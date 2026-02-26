<?php

namespace App\Http\Controllers;

use App\Models\Package\Encomienda;
use App\Models\Facturacion\Despatche;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Auth;

class EncomiendaGuiaController extends Controller
{
    public function verGuiaPDF($id)
    {
        $encomienda = Encomienda::with([
            'user',
            'transportista',
            'vehiculo',
            'remitente',
            'sucursal_remitente',
            'destinatario',
            'sucursal_destinatario',
            'facturacion',
            'paquetes'
        ])->findOrFail($id);

        // Verificar que el usuario tenga acceso a esta encomienda (origen o destino)
        $userSucursalId = Auth::user()->sucursal->id;
        if ($encomienda->sucursal_id !== $userSucursalId && $encomienda->sucursal_dest_id !== $userSucursalId) {
            abort(403, 'No tienes permisos para ver esta guía');
        }

        // Verificar que la encomienda tenga una guía asociada
        if (!$encomienda->doc_guia) {
            abort(404, 'Esta encomienda no tiene una guía de remisión asociada');
        }

        // Obtener la guía de remisión con todas las relaciones necesarias
        $despache = Despatche::with([
            'encomienda.remitente',
            'encomienda.destinatario',
            'encomienda.sucursal_remitente',
            'encomienda.sucursal_destinatario',
            'encomienda.paquetes',
            'company',
            'details',
            'remitente',
            'destinatario'
        ])->findOrFail($encomienda->doc_guia);

        // Usar el PdfController para generar el PDF A4
        $pdfController = new PdfController(app(\App\Services\Shared\PdfService::class));
        return $pdfController->despache80mm($despache);
    }
}

