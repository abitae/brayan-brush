<?php

use App\Http\Controllers\EncomiendaTicketController;
use App\Http\Controllers\EncomiendaGuiaController;
use App\Http\Controllers\PdfController;
use App\Livewire\Caja\CajaLive;
use App\Livewire\Configuration\CompanyLive;
use App\Livewire\Configuration\SucursalLive;
use App\Livewire\Configuration\TipoCajaLive;
use App\Livewire\Configuration\TransportistaLive;
use App\Livewire\Configuration\VehiculoLive;
use App\Livewire\Package\CustomerLive;
use App\Livewire\Package\EncomiendaCreateLive;
use App\Livewire\Package\EncomiendaSendLive;
use App\Livewire\Package\EncomiendaReceiveLive;
use App\Livewire\Package\EncomiendaDeliverLive;
use App\Livewire\Package\EncomiendaReturnLive;
use App\Livewire\Package\EncomiendaHomeLive;
use App\Livewire\Package\RutaSucursalLive;
use App\Livewire\Facturacion\InvoiceLive;
use App\Livewire\Facturacion\InvoiceCreateLive;
use App\Livewire\Facturacion\NoteLive;
use App\Livewire\Facturacion\NoteCreateLive;
use App\Livewire\Facturacion\TicketLive;
use App\Livewire\Facturacion\DespatcheLive;
use App\Livewire\Facturacion\DespatcheCreateLive;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('dashboard', \App\Livewire\Home\DashboardLive::class)->name('dashboard');
    
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('caja', CajaLive::class)->name('caja');
    Route::get('configuration/tipo-caja', TipoCajaLive::class)->name('configuration.tipo-caja');
    Route::get('configuration/sucursal', SucursalLive::class)->name('configuration.sucursal');
    Route::get('configuration/company', CompanyLive::class)->name('configuration.company');
    Route::get('configuration/transportista', TransportistaLive::class)->name('configuration.transportista');
    Route::get('configuration/vehiculo', VehiculoLive::class)->name('configuration.vehiculo');
    Route::get('configuration/customer', CustomerLive::class)->name('configuration.customer');
    Route::get('package/encomienda', EncomiendaCreateLive::class)->name('package.encomienda');
    Route::get('package/encomienda/send', EncomiendaSendLive::class)->name('package.encomienda.send');
    Route::get('package/encomienda/receive', EncomiendaReceiveLive::class)->name('package.encomienda.receive');
    Route::get('package/encomienda/deliver', EncomiendaDeliverLive::class)->name('package.encomienda.deliver');
    Route::get('package/encomienda/home', EncomiendaHomeLive::class)->name('package.encomienda.home');
    Route::get('package/encomienda/return', EncomiendaReturnLive::class)->name('package.encomienda.return');
    Route::get('package/encomienda/ruta', RutaSucursalLive::class)->name('package.encomienda.ruta');
    
    // Rutas de Facturación
    Route::get('facturacion/invoice', InvoiceLive::class)->name('facturacion.invoice');
    Route::get('facturacion/invoice/create/{id?}', InvoiceCreateLive::class)->name('facturacion.invoice.create');
    Route::get('facturacion/note', NoteLive::class)->name('facturacion.note');
    Route::get('facturacion/note/create/{id?}', NoteCreateLive::class)->name('facturacion.note.create');
    Route::get('facturacion/ticket', TicketLive::class)->name('facturacion.ticket');
    Route::get('facturacion/despatche', DespatcheLive::class)->name('facturacion.despatche');
    Route::get('facturacion/despatche/create/{id?}', DespatcheCreateLive::class)->name('facturacion.despatche.create');
    
    // Rutas de Reportes
    Route::get('report/encomiendas', \App\Livewire\Report\EncomiendasReport::class)->name('report.encomiendas');
    Route::get('report/facturacion', \App\Livewire\Report\FacturacionReport::class)->name('report.facturacion');
    Route::get('report/customers', \App\Livewire\Report\CustomerReport::class)->name('report.customers');
    
    // Rutas de Cobro/Pago
    Route::get('cobrar/encomiendas', \App\Livewire\Cobrar\EncomiendaCobrar::class)->name('cobrar.encomiendas');
    Route::get('pagar/encomiendas', \App\Livewire\Pagar\EncomiendaPagar::class)->name('pagar.encomiendas');
    
    // Rutas de Manifiestos
    Route::get('package/manifiesto', \App\Livewire\Package\ManifiestoLive::class)->name('package.manifiesto');
});

// Rutas de PDFs
Route::middleware(['auth'])->group(function () {
    Route::get('encomienda/ticket/pdf/{id}', [EncomiendaTicketController::class, 'verTicketPDF'])->name('encomienda.ticket.pdf');
    Route::get('encomienda/guia/pdf/{id}', [EncomiendaGuiaController::class, 'verGuiaPDF'])->name('encomienda.guia.pdf');
    Route::get('ticket/80mm/{ticket}', [PdfController::class, 'ticket80mm'])->name('pdf.ticket.80mm');
    Route::get('ticket/a4/{ticket}', [PdfController::class, 'ticketA4'])->name('pdf.ticket.a4');
    Route::get('invoice/80mm/{invoice}', [PdfController::class, 'invoice80mm'])->name('pdf.invoice.80mm');
    Route::get('invoice/a4/{invoice}', [PdfController::class, 'invoiceA4'])->name('pdf.invoice.a4');
    Route::get('note/80mm/{note}', [PdfController::class, 'note80mm'])->name('pdf.note.80mm');
    Route::get('note/a4/{note}', [PdfController::class, 'noteA4'])->name('pdf.note.a4');
    Route::get('despache/80mm/{despache}', [PdfController::class, 'despache80mm'])->name('pdf.despache.80mm');
    Route::get('despache/a4/{despache}', [PdfController::class, 'despacheA4'])->name('pdf.despache.a4');
    Route::get('sticker/a5/{encomienda}', [PdfController::class, 'stickerA5'])->name('pdf.sticker.a5');
    Route::get('sticker/a6/{encomienda}', [PdfController::class, 'stickerA6'])->name('pdf.sticker.a6');
    Route::get('declaracion/{encomienda}', [PdfController::class, 'declaracion'])->name('pdf.declaracion');
    Route::get('caja/80mm/{caja}', [PdfController::class, 'caja'])->name('pdf.caja');
});

require __DIR__.'/auth.php';
