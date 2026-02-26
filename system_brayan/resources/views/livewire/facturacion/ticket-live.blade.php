<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="text-sm text-gray-600">{{ $sub_title }}</p>
        </div>

        <!-- Filtros Mejorados -->
        <div class="px-6 pt-6 pb-2">
            <form wire:submit.prevent class="w-full">
                <div class="flex flex-col md:flex-row md:items-end md:space-x-4 gap-4">
                    <div class="flex-1">
                        <flux:input
                            type="search"
                            label="Buscar ticket"
                            wire:model.live="search"
                            placeholder="Documento, cliente, correlativo..."
                            icon="magnifying-glass"
                            size="sm"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <flux:input
                            type="datetime-local"
                            label="Desde"
                            wire:model.live="filtroFechaInicio"
                            size="sm"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <flux:input
                            type="datetime-local"
                            label="Hasta"
                            wire:model.live="filtroFechaFin"
                            size="sm"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <flux:select
                            wire:model.live="FiltroFormaPagoTipo"
                            label="Estado de pago"
                            size="sm"
                            class="w-full"
                        >
                            @foreach ($formaPagos as $forma)
                                <flux:select.option value="{{ $forma['id'] }}">{{ $forma['name'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </form>
            <div class="flex justify-end mt-3 space-x-2">
                <button type="button" wire:click="clearFilters"
                    class="inline-flex items-center px-3 py-1.5 rounded bg-gray-100 hover:bg-gray-200 text-sm text-gray-700 transition">
                    Limpiar filtros
                </button>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PDF</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $ticket->id }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-semibold text-purple-600">
                                    {{ $ticket->serie }}-{{ $ticket->correlativo }}</div>
                                <div class="text-xs text-gray-500">{{ $ticket->created_at->format('d-m-Y H:i') }}</div>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $ticket->formaPago_tipo == 'Contado' ? 'bg-cyan-100 text-cyan-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $ticket->formaPago_tipo }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="text-xs text-gray-500">{{ $ticket->client->code }}</div>
                                <div class="font-medium">{{ $ticket->client->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold">S/
                                {{ number_format($ticket->mtoImpVenta, 2) }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex gap-1">
                                    <flux:button wire:click="showPdf({{ $ticket->id }}, 'a4')"
                                        size="xs" variant="primary" icon="document-text" />
                                    <flux:button wire:click="showPdf({{ $ticket->id }}, '80mm')"
                                        size="xs" variant="primary" icon="printer" />
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <flux:dropdown>
                                    <flux:button slot="trigger" size="xs" variant="outline"
                                        icon="ellipsis-vertical" />
                                    <flux:menu>
                                        <flux:menu.item wire:click="showInfo({{ $ticket->id }})"
                                            icon="information-circle">Ver información</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No se encontraron tickets
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $tickets->links() }}
        </div>
    </div>

    <!-- Modal de información -->
    @if ($infoModal)
        <flux:modal wire:model="infoModal" name="infoModal">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Información del Comprobante</h3>
                @if ($cdr_code)
                    <div class="space-y-2 mb-4">
                        <p><strong>Código CDR:</strong> {{ $cdr_code }}</p>
                        <p><strong>Descripción:</strong> {{ $cdr_description }}</p>
                        @if ($cdr_note)
                            <p><strong>Nota:</strong> {{ $cdr_note }}</p>
                        @endif
                    </div>
                @endif
                @if ($errorCode)
                    <div class="bg-red-50 border border-red-200 rounded p-4">
                        <p><strong>Error:</strong> {{ $errorCode }}</p>
                        <p>{{ $errorMessage }}</p>
                    </div>
                @endif
                <div class="mt-4 flex justify-end">
                    <flux:button wire:click="closeInfo" variant="primary">Cerrar</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    <!-- Modal de PDF -->
    @if ($pdfModal)
        <flux:modal wire:model="pdfModal" name="pdfModal" class="max-w-7xl">
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ $pdfTitle }}</h3>
                    <div class="flex gap-2">
                        <flux:button href="{{ $pdfUrl }}" target="_blank" variant="outline" size="xs" icon="arrow-down-tray">
                            Descargar
                        </flux:button>
                        <flux:button wire:click="closePdf" variant="primary" size="xs">Cerrar</flux:button>
                    </div>
                </div>
                <div class="w-full" style="height: 80vh;">
                    <iframe src="{{ $pdfUrl }}" class="w-full h-full border-0 rounded-lg" frameborder="0"></iframe>
                </div>
            </div>
        </flux:modal>
    @endif
</div>

