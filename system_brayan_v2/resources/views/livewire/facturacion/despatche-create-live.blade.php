<div class="p-4 w-full max-w-6xl mx-auto space-y-4">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                <p class="text-sm text-gray-600">{{ $sub_title }}</p>
            </div>
            <flux:button href="{{ route('facturacion.despatche') }}" variant="outline" icon="arrow-left">
                Volver a la lista
            </flux:button>
        </div>
        <div class="p-6">
            <p class="text-gray-600">
                Las guías de remisión se generan al emitir facturas desde el módulo de facturación (Invoice).
                Para crear una nueva guía, emita una factura desde la lista de facturas.
            </p>
        </div>
    </div>
</div>
