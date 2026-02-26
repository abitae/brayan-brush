<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
                    <p class="text-sm text-gray-600 dark:text-zinc-400">{{ $sub_title }}</p>
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
                        <flux:input type="search" label="Buscar encomienda" wire:model.live="search"
                            placeholder="Código, Remitente, Destinatario..." size="sm" icon="magnifying-glass"
                            class="w-full" />
                    </div>
                    <div>
                        <flux:input type="datetime-local" label="Desde" wire:model.live="filtroFechaInicio"
                            size="sm" class="w-full" />
                    </div>
                    <div>
                        <flux:input type="datetime-local" label="Hasta" wire:model.live="filtroFechaFin" size="sm"
                            class="w-full" />
                    </div>
                    <div>
                        <flux:select wire:model.live="filtroSucursal" label="Sucursal" size="sm" class="w-full">
                            <flux:select.option value="">Todas</flux:select.option>
                            @foreach($sucursales as $sucursal)
                                <flux:select.option value="{{ $sucursal->id }}">{{ $sucursal->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div>
                        <flux:select wire:model.live="FiltroEstadoEncomienda" label="Estado Encomienda" size="sm"
                            class="w-full">
                            <flux:select.option value="">Todos</flux:select.option>
                            @foreach(\App\Livewire\Report\EncomiendasReport::ESTADOS_ENCOMIENDA as $estado)
                                <flux:select.option value="{{ $estado['id'] }}">{{ $estado['name'] }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div>
                        <flux:select wire:model.live="FiltroEstadoPago" label="Tipo de Pago" size="sm"
                            class="w-full">
                            <flux:select.option value="">Todos</flux:select.option>
                            @foreach(\App\Livewire\Report\EncomiendasReport::ESTADOS_PAGO as $estado)
                                <flux:select.option value="{{ $estado['id'] }}">{{ $estado['name'] }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div>
                        <flux:select wire:model.live="filtroMetodoPago" label="Método de Pago" size="sm"
                            class="w-full">
                            <flux:select.option value="">Todos</flux:select.option>
                            @foreach(\App\Livewire\Report\EncomiendasReport::METODOS_PAGO as $metodo)
                                <flux:select.option value="{{ $metodo['id'] }}">{{ $metodo['name'] }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </form>
            <div class="flex justify-end mt-3 space-x-2">
                <button type="button" wire:click="resetFilters"
                    class="inline-flex items-center px-3 py-1.5 rounded bg-gray-100 hover:bg-gray-200 text-sm text-gray-700 transition">
                    Limpiar filtros
                </button>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto px-6 pt-6">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Código</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Remitente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Destinatario</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Monto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Documentos</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($encomiendas as $encomienda)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-zinc-100">{{ $encomienda->id }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $encomienda->code }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $encomienda->remitente->code }}</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $encomienda->remitente->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $encomienda->destinatario->code }}</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $encomienda->destinatario->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                    {{ $encomienda->estado_encomienda == 'ENTREGADO' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 
                                       ($encomienda->estado_encomienda == 'ENVIADO' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : 
                                       ($encomienda->estado_encomienda == 'RECIBIDO' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : 'bg-gray-100 dark:bg-zinc-800 text-gray-800 dark:text-zinc-300')) }}">
                                    {{ $encomienda->estado_encomienda }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">S/ {{ number_format($encomienda->monto, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-zinc-100">
                                @if($encomienda->fecha_creacion)
                                    {{ $encomienda->fecha_creacion instanceof \Carbon\Carbon ? $encomienda->fecha_creacion->format('d-m-Y H:i') : \Carbon\Carbon::parse($encomienda->fecha_creacion)->format('d-m-Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1 flex-wrap">
                                        @if($encomienda->doc_ticket)
                                            <a href="{{ route('encomienda.ticket.pdf', $encomienda->id) }}" target="_blank" 
                                               class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition"
                                               title="Ver Ticket">
                                                <flux:icon name="document" class="w-3 h-3 mr-1" />
                                                Ticket
                                            </a>
                                        @endif
                                        @if($encomienda->doc_factura)
                                            @php
                                                $invoice = $encomienda->invoice ?? \App\Models\Facturacion\Invoice::find($encomienda->doc_factura);
                                            @endphp
                                            @if($invoice)
                                                <a href="{{ route('pdf.invoice.80mm', $invoice->id) }}" target="_blank" 
                                                   class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-900/50 transition"
                                                   title="Ver Invoice/Factura">
                                                    <flux:icon name="document-text" class="w-3 h-3 mr-1" />
                                                    Invoice
                                                </a>
                                            @endif
                                        @endif
                                        @if($encomienda->doc_guia)
                                            <a href="{{ route('encomienda.guia.pdf', $encomienda->id) }}" target="_blank" 
                                               class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-900/50 transition"
                                               title="Ver Guía">
                                                <flux:icon name="document-duplicate" class="w-3 h-3 mr-1" />
                                                Guía
                                            </a>
                                        @endif
                                    </div>
                                    @if($encomienda->estado_credito && in_array($encomienda->estado_credito, ['Pendiente', 'Cancelado']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                            {{ $encomienda->estado_credito == 'Cancelado' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' }}">
                                            {{ $encomienda->estado_credito }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-400">
                                No se encontraron encomiendas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700">
            {{ $encomiendas->links() }}
        </div>
    </div>
</div>

