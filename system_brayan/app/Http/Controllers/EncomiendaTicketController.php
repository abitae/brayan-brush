<?php

namespace App\Http\Controllers;

use App\Models\Package\Encomienda;
use App\Models\Configuration\Company;
use App\Models\Configuration\Sucursal;
use App\Services\Shared\PdfService;
use Illuminate\Support\Facades\Auth;

class EncomiendaTicketController extends Controller
{
    public function verTicketPDF($id)
    {
        // Validar que el ID sea válido
        if (empty($id) || $id == 0) {
            abort(404, 'ID de encomienda no válido');
        }
        
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
            abort(403, 'No tienes permisos para ver este ticket');
        }

        // Cargar información de la empresa y sucursal
        $company = Company::first();
        $sucursal = Sucursal::find($encomienda->sucursal_id);

        // Agregar información adicional a la encomienda
        $encomienda->company_name = $company->razonSocial ?? 'CORPORACIÓN LOGÍSTICA BRAYAN BRUSH EIRL';
        $encomienda->company_ruc = $company->ruc ?? '20612345678';
        $encomienda->company_logo = $company->logo_path ?? null;

        // Renderizar la vista del ticket de 80mm
        $html = view('pdfs.tickets.80mm', compact('encomienda'))->render();

        // Generar el PDF usando el servicio
        $pdfService = app(PdfService::class);
        return $pdfService->generate80mmTicket($html, "ticket-encomienda-{$encomienda->code}.pdf");
    }
}
