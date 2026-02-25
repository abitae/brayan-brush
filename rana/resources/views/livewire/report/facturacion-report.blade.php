<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                    <p class="text-sm text-gray-600">{{ $sub_title }}</p>
                </div>
                <flux:button wire:click="exportExcel" variant="primary" icon="arrow-down-tray" size="xs">
                    Exportar Excel
                </flux:button>
            </div>
        </div>

        <!-- Filtros Mejorados -->
        <div class="px-6 pt-6 pb-2">
            <form wire:submit.prevent class="w-full">
                <div class="flex flex-col md:flex-row md:items-end md:space-x-4 gap-4">
                    <div class="flex-1">
                        <flux:input type="search" label="Buscar comprobante" wire:model.live="search"
                            placeholder="N°, Cliente, Serie, Correlativo..." size="sm" icon="magnifying-glass"
                            class="w-full" />
                    </div>
                    <div>
                        <flux:input type="date" label="Desde" wire:model.live="filtroFechaInicio"
                            size="sm" class="w-full" />
                    </div>
                    <div>
                        <flux:input type="date" label="Hasta" wire:model.live="filtroFechaFin" size="sm"
                            class="w-full" />
                    </div>
                    <div>
                        <flux:select wire:model.live="tipoDocumento" label="Tipo Documento" size="sm"
                            class="w-full">
                            @foreach($tiposDocumento as $tipo)
                                <flux:select.option value="{{ $tipo['id'] }}">{{ $tipo['name'] }}
                                </flux:select.option>
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

        <!-- Resumen -->
        <div class="px-6 pt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm text-blue-600">Total Facturas</div>
                <div class="text-2xl font-bold text-blue-900">S/ {{ number_format($totalFacturas, 2) }}</div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-600">Total Tickets</div>
                <div class="text-2xl font-bold text-green-900">S/ {{ number_format($totalTickets, 2) }}</div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="text-sm text-red-600">Total Notas</div>
                <div class="text-2xl font-bold text-red-900">S/ {{ number_format($totalNotas, 2) }}</div>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="text-sm text-purple-600">Total General</div>
                <div class="text-2xl font-bold text-purple-900">S/ {{ number_format($totalGeneral, 2) }}</div>
            </div>
        </div>

        <!-- Tablas de documentos -->
        @if($tipoDocumento == 'Todos' || $tipoDocumento == 'Factura' || $tipoDocumento == 'Boleta')
            <div class="px-6 pt-6">
                <h3 class="text-lg font-semibold mb-4">Facturas y Boletas</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold">{{ $invoice->serie }}-{{ $invoice->correlativo }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $invoice->client->name }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold">S/ {{ number_format($invoice->mtoImpVenta, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $invoice->created_at->format('d-m-Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay facturas/boletas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($tipoDocumento == 'Todos' || $tipoDocumento == 'Nota')
            <div class="px-6 pt-6">
                <h3 class="text-lg font-semibold mb-4">Notas de Crédito</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($notes as $note)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold">{{ $note->serie }}-{{ $note->correlativo }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $note->client->name }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold">S/ {{ number_format($note->mtoImpVenta, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $note->created_at->format('d-m-Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay notas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($tipoDocumento == 'Todos' || $tipoDocumento == 'Ticket')
            <div class="px-6 pt-6 pb-6">
                <h3 class="text-lg font-semibold mb-4">Tickets</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-semibold">{{ $ticket->serie }}-{{ $ticket->correlativo }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $ticket->client->name }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold">S/ {{ number_format($ticket->mtoImpVenta, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $ticket->created_at->format('d-m-Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay tickets</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

