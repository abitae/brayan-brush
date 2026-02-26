<?php

namespace App\Http\Controllers;

use App\Models\Caja\Caja;
use App\Models\Configuration\Company;
use App\Models\Facturacion\Despatche;
use App\Models\Facturacion\Invoice;
use App\Models\Facturacion\Note;
use App\Models\Facturacion\Ticket;
use App\Models\Package\Encomienda;
use App\Services\Shared\PdfService;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    protected $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function ticket80mm(Ticket $ticket)
    {
        $height = 250 + $ticket->details->count() * 8;
        $html = view('pdfs.ticket.80mm', ['ticket' => $ticket])->render();
        
        return $this->pdfService->generateCustom($html, [
            'format' => [80, $height],
            'margin_left' => 1,
            'margin_right' => 10,
            'margin_top' => 5,
            'margin_bottom' => 5,
        ], "ticket-{$ticket->serie}-{$ticket->correlativo}.pdf");
    }

    public function ticketA4(Ticket $ticket)
    {
        $html = view('pdfs.ticket.a4', ['ticket' => $ticket])->render();
        return $this->pdfService->generateA4($html, "ticket-{$ticket->serie}-{$ticket->correlativo}.pdf");
    }

    public function invoice80mm(Invoice $invoice)
    {
        $height = 220 + $invoice->details->count() * 8;
        $html = view('pdfs.invoice.80mm', ['invoice' => $invoice])->render();
        
        return $this->pdfService->generateCustom($html, [
            'format' => [80, $height],
            'margin_left' => 1,
            'margin_right' => 10,
            'margin_top' => 5,
            'margin_bottom' => 5,
        ], "invoice-{$invoice->serie}-{$invoice->correlativo}.pdf");
    }

    public function invoiceA4(Invoice $invoice)
    {
        $html = view('pdfs.invoice.a4', ['invoice' => $invoice])->render();
        return $this->pdfService->generateA4($html, "invoice-{$invoice->serie}-{$invoice->correlativo}.pdf");
    }

    public function note80mm(Note $note)
    {
        $height = 220 + $note->details->count() * 8;
        $html = view('pdfs.note.80mm', ['note' => $note])->render();
        
        return $this->pdfService->generateCustom($html, [
            'format' => [80, $height],
            'margin_left' => 1,
            'margin_right' => 10,
            'margin_top' => 5,
            'margin_bottom' => 5,
        ], "note-{$note->serie}-{$note->correlativo}.pdf");
    }

    public function noteA4(Note $note)
    {
        $html = view('pdfs.note.a4', ['note' => $note])->render();
        return $this->pdfService->generateA4($html, "note-{$note->serie}-{$note->correlativo}.pdf");
    }

    public function despache80mm(Despatche $despache)
    {
        $height = 250 + $despache->details->count() * 8;
        $html = view('pdfs.despache.80mm', ['despache' => $despache])->render();
        
        return $this->pdfService->generateCustom($html, [
            'format' => [80, $height],
            'margin_left' => 1,
            'margin_right' => 10,
            'margin_top' => 5,
            'margin_bottom' => 5,
        ], "despache-{$despache->serie}-{$despache->correlativo}.pdf");
    }

    public function despacheA4(Despatche $despache)
    {
        $html = view('pdfs.despache.a4', ['despache' => $despache])->render();
        return $this->pdfService->generateA4($html, "despache-{$despache->serie}-{$despache->correlativo}.pdf");
    }

    public function stickerA5(Encomienda $encomienda)
    {
        // Verificar que el usuario tenga acceso a esta encomienda (origen o destino)
        $userSucursalId = Auth::user()->sucursal->id;
        if ($encomienda->sucursal_id !== $userSucursalId && $encomienda->sucursal_dest_id !== $userSucursalId) {
            abort(403, 'No tienes permisos para ver este sticker');
        }
        
        $html = view('pdfs.sticker.a6', ['encomienda' => $encomienda])->render();
        return $this->pdfService->generateA5($html, "sticker-{$encomienda->code}.pdf");
    }

    public function stickerA6(Encomienda $encomienda)
    {
        // Verificar que el usuario tenga acceso a esta encomienda (origen o destino)
        $userSucursalId = Auth::user()->sucursal->id;
        if ($encomienda->sucursal_id !== $userSucursalId && $encomienda->sucursal_dest_id !== $userSucursalId) {
            abort(403, 'No tienes permisos para ver este sticker');
        }
        
        $html = view('pdfs.sticker.a6', ['encomienda' => $encomienda])->render();
        return $this->pdfService->generateA6($html, "sticker-{$encomienda->code}.pdf");
    }

    public function declaracion(Encomienda $encomienda)
    {
        // Verificar que el usuario tenga acceso a esta encomienda (origen o destino)
        $userSucursalId = Auth::user()->sucursal->id;
        if ($encomienda->sucursal_id !== $userSucursalId && $encomienda->sucursal_dest_id !== $userSucursalId) {
            abort(403, 'No tienes permisos para ver esta declaración');
        }
        $company = Company::first();
        $html = view('pdfs.declaracion.declaracion', ['encomienda' => $encomienda, 'company' => $company])->render();
        return $this->pdfService->generateA4($html, "declaracion-{$encomienda->code}.pdf");
    }

    public function caja(Caja $caja)
    {
        // Verificar que el usuario tenga acceso a esta caja
        if ($caja->user_id !== Auth::user()->id) {
            abort(403, 'No tienes permisos para ver esta caja');
        }
        
        // Cargar relaciones necesarias
        $caja->load(['user', 'entries.tipoEntry', 'exits.tipoExit']);
        
        // Calcular altura dinámica basada en el contenido
        $entriesCount = $caja->entries->count();
        $exitsCount = $caja->exits->count();
        $height = 300 + ($entriesCount * 8) + ($exitsCount * 8);
        
        $html = view('pdfs.caja.80mm', ['caja' => $caja])->render();
        
        return $this->pdfService->generateCustom($html, [
            'format' => [80, $height],
            'margin_left' => 1,
            'margin_right' => 10,
            'margin_top' => 5,
            'margin_bottom' => 5,
        ], "cierre-caja-{$caja->id}.pdf");
    }
}

