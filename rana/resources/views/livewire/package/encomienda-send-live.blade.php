<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <x-package.encomienda-page-header
        title="Envío de Encomiendas"
        description="Gestiona encomiendas registradas para envío"
        icon="truck"
        iconColor="orange"
        actionLabel="Enviar"
        actionMethod="openSendModal()"
        actionIcon="paper-airplane"
        :actionDisabled="$this->selectedEncomiendasCount === 0"
        :actionCount="$this->selectedEncomiendasCount" />

    @if (empty($rutasDisponibles) || $rutasDisponibles->isEmpty())
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border border-gray-100 dark:border-zinc-700 p-2">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-orange-700 dark:text-orange-400">No hay rutas disponibles</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400">Configure una ruta para poder enviar encomiendas</p>
                <flux:button href="{{ route('package.encomienda.ruta') }}" icon:trailing="plus" class="shadow-xl"
                    size="xs">
                    Crear ruta
                </flux:button>
            </div>
        </div>
    @else
        <x-package.encomienda-table-wrapper borderColor="orange">
            <x-slot name="filters">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 border-b border-gray-100 dark:border-zinc-700 bg-orange-50/30 dark:bg-orange-900/20">
                    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                                <flux:icon name="magnifying-glass" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                                Buscar
                            </label>
                            <flux:input 
                                type="search" 
                                wire:model.live="search"
                                placeholder="Código, remitente o destinatario..." 
                                size="xs"
                                class="w-full sm:w-64 max-w-full focus:border-orange-400 dark:focus:border-orange-500 transition" />
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                                <flux:icon name="calendar" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                                Fecha
                            </label>
                            <flux:input 
                                type="date" 
                                wire:model.live="fecha_creacion_filter" 
                                size="xs"
                                class="w-full sm:w-36 max-w-full focus:border-orange-400 dark:focus:border-orange-500 transition" />
                        </div>
                        @if($rutasDisponibles && $rutasDisponibles->count() > 0)
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                                    <flux:icon name="map" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                                    Ruta
                                </label>
                                <flux:select wire:model.live="filterRuta" size="xs" class="w-80">
                                    @foreach ($rutasDisponibles as $ruta)
                                        <option value="{{ $ruta->id }}">
                                            Ruta #{{ $ruta->id }} - {{ $ruta->sucursalOrigen->name ?? 'N/A' }} → {{ $ruta->sucursalDestino->name ?? 'N/A' }} | {{ $ruta->transportista->name ?? 'N/A' }} | {{ $ruta->vehiculo->placa ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </flux:select>
                            </div>
                        @endif
                    </div>
                </div>
            </x-slot>

            <div class="overflow-x-auto">
            <table class="min-w-full text-xs border border-gray-200 dark:border-zinc-700 rounded-lg overflow-hidden shadow p-2">
                <thead>
                    <tr class="bg-gray-50 dark:bg-zinc-800">
                        <th class="px-2 py-2 text-center text-gray-500 dark:text-zinc-400 font-medium">
                            <flux:checkbox wire:model.live="selectAll" size="xs" />
                        </th>
                        <th class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 font-medium">#</th>
                        <th class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 font-medium">Código</th>
                        <th class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 font-medium">Remitente y Destinatario</th>
                        <th class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 font-medium">Destino y Fecha</th>
                        <th class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 font-medium">Monto S/</th>
                        <th class="px-2 py-2 text-center text-gray-500 dark:text-zinc-400 font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($encomiendas as $i => $encomienda)
                        <tr wire:key="encomienda-{{ $encomienda->id }}" class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                            <td class="px-2 py-1 text-center">
                                <flux:checkbox wire:model.live="selectedEncomiendas" value="{{ $encomienda->id }}"
                                    size="xs" />
                            </td>
                            <td class="px-2 py-1 text-gray-400 dark:text-zinc-500">{{ $loop->iteration }}</td>
                                <x-package.encomienda-code-cell :encomienda="$encomienda" color="blue" :showPago="true" :showComprobante="true" />
                                <x-package.encomienda-people-cell :encomienda="$encomienda" />
                                <x-package.encomienda-destination-cell 
                                    :encomienda="$encomienda" 
                                    :showFechaCreacion="true" />
                                <x-package.encomienda-amount-cell :amount="$encomienda->monto ?? 0" color="green" />
                                <td class="px-2 py-1 text-center">
                                    <flux:dropdown>
                                        <flux:button icon:trailing="bars-3" size="xs" color="zinc">
                                        </flux:button>

                                        <flux:menu>
                                            <flux:menu.item icon="document"
                                                wire:click="verTicketPDF({{ $encomienda->id }})">
                                                Ver Ticket
                                            </flux:menu.item>

                                            @if ($encomienda->doc_factura)
                                                <flux:menu.item icon="document-text"
                                                    wire:click="verInvoicePDF({{ $encomienda->id }})">
                                                    VerFactura/Boleta
                                                </flux:menu.item>
                                            @endif

                                            @if ($encomienda->doc_guia)
                                                <flux:menu.item icon="document"
                                                    wire:click="verGuiaPDF({{ $encomienda->id }})">
                                                    Ver Guía
                                                </flux:menu.item>
                                            @else
                                                <flux:menu.item icon="plus"
                                                    wire:click="crearGuiaPDF({{ $encomienda->id }})">
                                                    Crear Guía
                                                </flux:menu.item>
                                            @endif

                                            <flux:menu.item icon="document"
                                                wire:click="verStickerPDF({{ $encomienda->id }})">
                                                Ver Sticker
                                            </flux:menu.item>
                                            <flux:menu.item icon="document-text"
                                                wire:click="verDeclaracionPDF({{ $encomienda->id }})">
                                                Ver Declaración jurada
                                            </flux:menu.item>

                                            <flux:menu.separator />

                                            <flux:menu.item icon="eye"
                                                wire:click="showEncomiendaDetails({{ $encomienda->id }})">
                                                Ver detalles
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-2 py-4 text-center text-gray-400 dark:text-zinc-500">No hay encomiendas
                                    registradas para envío.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-2 flex justify-between items-center px-2 py-2">
                <div class="text-xs text-gray-500 dark:text-zinc-400">
                    @if ($encomiendas instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        @if ($encomiendas->total() > 0)
                            Mostrando {{ $encomiendas->firstItem() }}-{{ $encomiendas->lastItem() }} de
                            {{ $encomiendas->total() }}
                        @else
                            No hay encomiendas
                        @endif
                    @else
                        @if ($encomiendas->count() > 0)
                            Mostrando {{ $encomiendas->count() }} de {{ $encomiendas->count() }}
                        @else
                            No hay encomiendas
                        @endif
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 dark:text-zinc-400">Mostrar:</span>
                    <flux:select wire:model.live="perPage" size="xs" class="w-20">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="0">Todos</option>
                    </flux:select>
                </div>
            </div>

            <x-slot name="pagination">
                @if ($encomiendas instanceof \Illuminate\Pagination\LengthAwarePaginator && $encomiendas->hasPages())
                    {{ $encomiendas->links() }}
                @endif
            </x-slot>
        </x-package.encomienda-table-wrapper>
    @endif

    <!-- Modal de Envío -->
    <flux:modal wire:model="showSendModal" max-width="lg">
        <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-orange-50 dark:bg-orange-900/20 rounded flex items-center justify-center">
                    <flux:icon name="paper-airplane" class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                </div>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Enviar Encomiendas</h2>
            </div>
        </div>
        <div class="p-4">
            <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                <div class="flex items-center gap-2">
                    <flux:icon name="information-circle" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        Se enviarán <strong>{{ $this->selectedEncomiendasCount }}</strong> encomienda(s)
                    </p>
                </div>
            </div>
        </div>
        <div class="px-4 pb-4">
            <flux:input label="Fecha y hora de envío" type="datetime-local" wire:model.defer="fecha_envio"
                class="w-full max-w-xs focus:border-orange-400" size="xs"
                min="{{ now()->format('Y-m-d\TH:i') }}" />

        </div>
        <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-700 bg-zinc-50/30 dark:bg-zinc-800/30 flex justify-end gap-2">
            <flux:button wire:click="closeSendModal" variant="ghost" size="xs">
                Cancelar
            </flux:button>
            <flux:button wire:click="sendEncomiendas" variant="primary" size="xs" icon="paper-airplane">
                Enviar
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal de Manifiestos -->
    <flux:modal wire:model="showManifiestoModal" max-width="2xl">
        <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-green-50 dark:bg-green-900/20 rounded flex items-center justify-center">
                    <flux:icon name="document-duplicate" class="w-4 h-4 text-green-600 dark:text-green-400" />
                </div>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Manifiestos Creados</h2>
            </div>
        </div>
        <div class="p-4">
            <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-100 dark:border-green-800">
                <div class="flex items-center gap-2">
                    <flux:icon name="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400" />
                    <p class="text-sm text-green-800 dark:text-green-300">
                        Se crearon <strong>{{ count($manifiestosCreados) }}</strong> manifiesto(s) exitosamente.
                    </p>
                </div>
            </div>

            @if (count($manifiestosCreados) > 0)
                <div class="space-y-3">
                    @foreach ($manifiestosCreados as $manifiesto)
                        @php
                            $ids = json_decode($manifiesto->ids, true);
                            $cantidadEncomiendas = is_array($ids) ? count($ids) : 0;
                            $sucursalOrigen = $manifiesto->sucursal->name ?? 'N/A';
                            $sucursalDestino = $manifiesto->destino->name ?? 'N/A';
                        @endphp
                        <div class="p-4 bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-sm">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <flux:icon name="truck" class="w-5 h-5 text-blue-500" />
                                        <h3 class="font-semibold text-zinc-900 dark:text-white">
                                            {{ $sucursalOrigen }} → {{ $sucursalDestino }}
                                        </h3>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-zinc-400 space-y-1">
                                        <p>
                                            <span class="font-medium">Encomiendas:</span> {{ $cantidadEncomiendas }}
                                        </p>
                                        <p>
                                            <span class="font-medium">Fecha de creación:</span>
                                            {{ $manifiesto->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <flux:button 
                                        wire:click="downloadManifiesto({{ $manifiesto->id }})" 
                                        variant="primary" 
                                        size="xs" 
                                        icon="arrow-down-tray">
                                        Descargar
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-zinc-400">
                    <flux:icon name="exclamation-circle" class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                    <p>No hay manifiestos para mostrar.</p>
                </div>
            @endif
        </div>
        <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-700 bg-zinc-50/30 dark:bg-zinc-800/30 flex justify-end gap-2">
            <flux:button wire:click="closeManifiestoModal" variant="primary" size="xs">
                Cerrar
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal de Imprimir Ticket (Minimalista) -->
    <flux:modal wire:model="modalImprimirTicket" :dismissible="true"
        class="w-full max-w-2xl mx-auto">
        <div class="p-4 space-y-3">
            <!-- Encabezado minimalista -->
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <flux:icon name="document-text" class="w-6 h-6 text-blue-500" />
                    <span class="font-semibold text-gray-800 dark:text-white text-base">Encomienda</span>
                </div>
                <a href="{{ route('encomienda.ticket.pdf', ['id' => $encomienda_id ?? 0]) }}" target="_blank"
                    class="flex items-center gap-1 px-2 py-1 text-xs text-blue-600 hover:underline"
                    title="Abrir PDF en nueva pestaña">
                    <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                    PDF
                </a>
            </div>

            <!-- Contenido -->
            <div>
                <!-- Estado de carga -->
                <div id="loadingStateSend" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando ticket...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerSend" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenSend()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameSend"
                            src="{{ route('encomienda.ticket.pdf', ['id' => $encomienda_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingSend()" onerror="showErrorSend()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateSend" class="hidden">
                    <div class="text-center py-8">
                        <flux:icon name="exclamation-triangle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                        <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">No se pudo cargar el ticket.</div>
                        <div class="flex justify-center gap-2">
                            <flux:button wire:click="refreshTicket" icon="arrow-path" size="xs"
                                class="bg-blue-500 text-white">
                                Reintentar
                            </flux:button>
                            <a href="{{ route('encomienda.ticket.pdf', ['id' => $encomienda_id ?? 0]) }}"
                                target="_blank"
                                class="flex items-center gap-1 px-2 py-1 text-xs text-blue-600 hover:underline">
                                <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                <flux:button wire:click="$set('modalImprimirTicket', false)" size="xs"
                    class="text-gray-500 dark:text-zinc-400 hover:text-gray-700">
                    Cerrar
                </flux:button>
            </div>
        </div>

        <!-- Scripts minimalistas -->
        <script>
            function hideLoadingSend() {
                document.getElementById('loadingStateSend').classList.add('hidden');
                document.getElementById('pdfContainerSend').classList.remove('hidden');
            }

            function showErrorSend() {
                document.getElementById('loadingStateSend').classList.add('hidden');
                document.getElementById('errorStateSend').classList.remove('hidden');
            }

            function toggleFullscreenSend() {
                const iframe = document.getElementById('pdfFrameSend');
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else if (iframe.webkitRequestFullscreen) {
                    iframe.webkitRequestFullscreen();
                } else if (iframe.msRequestFullscreen) {
                    iframe.msRequestFullscreen();
                }
            }
            // Fallback para mostrar PDF si tarda mucho
            setTimeout(function() {
                if (!document.getElementById('pdfContainerSend').classList.contains('hidden')) {
                    hideLoadingSend();
                }
            }, 10000);
            // Livewire: refrescar ticket
            document.addEventListener('livewire:init', () => {
                Livewire.on('ticket-refreshed', () => {
                    document.getElementById('errorStateSend').classList.add('hidden');
                    document.getElementById('loadingStateSend').classList.remove('hidden');
                    document.getElementById('pdfContainerSend').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameSend');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                });
            });
        </script>
    </flux:modal>

    <!-- Modal de Ver Invoice/Factura (Minimalista) -->
    <flux:modal wire:model="modalVerInvoice" :dismissible="true"
        class="w-full max-w-2xl mx-auto">
        <div class="p-4 space-y-3">
            <!-- Encabezado minimalista -->
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <flux:icon name="document-text" class="w-6 h-6 text-green-500" />
                    <span class="font-semibold text-gray-800 dark:text-white text-base">Factura</span>
                </div>
                <a href="{{ route('pdf.invoice.80mm', ['invoice' => $invoice_id ?? 0]) }}" target="_blank"
                    class="flex items-center gap-1 px-2 py-1 text-xs text-green-600 hover:underline"
                    title="Abrir PDF en nueva pestaña">
                    <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                    PDF
                </a>
            </div>

            <!-- Contenido -->
            <div>
                <!-- Estado de carga -->
                <div id="loadingStateInvoice" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando factura...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerInvoice" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenInvoice()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameInvoice"
                            src="{{ route('pdf.invoice.80mm', ['invoice' => $invoice_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingInvoice()" onerror="showErrorInvoice()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateInvoice" class="hidden">
                    <div class="text-center py-8">
                        <flux:icon name="exclamation-triangle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                        <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">No se pudo cargar la factura.</div>
                        <div class="flex justify-center gap-2">
                            <flux:button wire:click="refreshInvoice" icon="arrow-path" size="xs"
                                class="bg-green-500 text-white">
                                Reintentar
                            </flux:button>
                            <a href="{{ route('pdf.invoice.80mm', ['invoice' => $invoice_id ?? 0]) }}"
                                target="_blank"
                                class="flex items-center gap-1 px-2 py-1 text-xs text-green-600 hover:underline">
                                <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                <flux:button wire:click="$set('modalVerInvoice', false)" size="xs"
                    class="text-gray-500 dark:text-zinc-400 hover:text-gray-700">
                    Cerrar
                </flux:button>
            </div>
        </div>

        <!-- Scripts minimalistas -->
        <script>
            function hideLoadingInvoice() {
                document.getElementById('loadingStateInvoice').classList.add('hidden');
                document.getElementById('pdfContainerInvoice').classList.remove('hidden');
            }

            function showErrorInvoice() {
                document.getElementById('loadingStateInvoice').classList.add('hidden');
                document.getElementById('errorStateInvoice').classList.remove('hidden');
            }

            function toggleFullscreenInvoice() {
                const iframe = document.getElementById('pdfFrameInvoice');
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else if (iframe.webkitRequestFullscreen) {
                    iframe.webkitRequestFullscreen();
                } else if (iframe.msRequestFullscreen) {
                    iframe.msRequestFullscreen();
                }
            }
            // Fallback para mostrar PDF si tarda mucho
            setTimeout(function() {
                if (!document.getElementById('pdfContainerInvoice').classList.contains('hidden')) {
                    hideLoadingInvoice();
                }
            }, 10000);
            // Livewire: refrescar invoice
            document.addEventListener('livewire:init', () => {
                Livewire.on('invoice-refreshed', () => {
                    document.getElementById('errorStateInvoice').classList.add('hidden');
                    document.getElementById('loadingStateInvoice').classList.remove('hidden');
                    document.getElementById('pdfContainerInvoice').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameInvoice');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                });
            });
        </script>
    </flux:modal>

    <!-- Modal de Ver Guía de Remisión (Minimalista) -->
    <flux:modal wire:model="modalVerGuia" :dismissible="true"
        class="w-full max-w-2xl mx-auto">
        <div class="p-4 space-y-3">
            <!-- Encabezado minimalista -->
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <flux:icon name="document-text" class="w-6 h-6 text-purple-500" />
                    <span class="font-semibold text-gray-800 dark:text-white text-base">Guía de Remisión</span>
                </div>
                <a href="{{ route('encomienda.guia.pdf', ['id' => $guia_id ?? 0]) }}" target="_blank"
                    class="flex items-center gap-1 px-2 py-1 text-xs text-purple-600 hover:underline"
                    title="Abrir PDF en nueva pestaña">
                    <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                    PDF
                </a>
            </div>

            <!-- Contenido -->
            <div>
                <!-- Estado de carga -->
                <div id="loadingStateGuiaSend" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando guía de remisión...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerGuiaSend" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenGuiaSend()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameGuiaSend"
                            src="{{ route('encomienda.guia.pdf', ['id' => $guia_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingGuiaSend()" onerror="showErrorGuiaSend()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateGuiaSend" class="hidden">
                    <div class="text-center py-8">
                        <flux:icon name="exclamation-triangle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                        <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">No se pudo cargar la guía de remisión.</div>
                        <div class="flex justify-center gap-2">
                            <flux:button wire:click="refreshGuia" icon="arrow-path" size="xs"
                                class="bg-purple-500 text-white">
                                Reintentar
                            </flux:button>
                            <a href="{{ route('encomienda.guia.pdf', ['id' => $guia_id ?? 0]) }}"
                                target="_blank"
                                class="flex items-center gap-1 px-2 py-1 text-xs text-purple-600 hover:underline">
                                <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                <flux:button wire:click="$set('modalVerGuia', false)" size="xs"
                    class="text-gray-500 dark:text-zinc-400 hover:text-gray-700">
                    Cerrar
                </flux:button>
            </div>
        </div>

        <!-- Scripts minimalistas -->
        <script>
            function hideLoadingGuiaSend() {
                document.getElementById('loadingStateGuiaSend').classList.add('hidden');
                document.getElementById('pdfContainerGuiaSend').classList.remove('hidden');
            }

            function showErrorGuiaSend() {
                document.getElementById('loadingStateGuiaSend').classList.add('hidden');
                document.getElementById('errorStateGuiaSend').classList.remove('hidden');
            }

            function toggleFullscreenGuiaSend() {
                const iframe = document.getElementById('pdfFrameGuiaSend');
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else if (iframe.webkitRequestFullscreen) {
                    iframe.webkitRequestFullscreen();
                } else if (iframe.msRequestFullscreen) {
                    iframe.msRequestFullscreen();
                }
            }
            // Fallback para mostrar PDF si tarda mucho
            setTimeout(function() {
                if (!document.getElementById('pdfContainerGuiaSend').classList.contains('hidden')) {
                    hideLoadingGuiaSend();
                }
            }, 10000);
            // Livewire: refrescar guía
            document.addEventListener('livewire:init', () => {
                Livewire.on('guia-refreshed', () => {
                    document.getElementById('errorStateGuiaSend').classList.add('hidden');
                    document.getElementById('loadingStateGuiaSend').classList.remove('hidden');
                    document.getElementById('pdfContainerGuiaSend').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameGuiaSend');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                });
            });
        </script>
    </flux:modal>

    <!-- Modal de Ver Sticker A6 (Minimalista) -->
    <flux:modal wire:model="modalVerSticker" :dismissible="true"
        class="w-full max-w-2xl mx-auto">
        <div class="p-4 space-y-3">
            <!-- Encabezado minimalista -->
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <flux:icon name="document" class="w-6 h-6 text-amber-500" />
                    <span class="font-semibold text-gray-800 dark:text-white text-base">Sticker A6</span>
                </div>
                @if ($encomienda_id)
                    <a href="{{ route('pdf.sticker.a6', ['encomienda' => $encomienda_id]) }}" target="_blank"
                        class="flex items-center gap-1 px-2 py-1 text-xs text-amber-600 hover:underline"
                        title="Abrir PDF en nueva pestaña">
                        <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                        PDF
                    </a>
                @endif
            </div>

            <!-- Contenido -->
            <div>
                <!-- Estado de carga -->
                <div id="loadingStateStickerSend" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando sticker A6...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerStickerSend" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenStickerSend()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameStickerSend"
                            src="{{ route('pdf.sticker.a6', ['encomienda' => $encomienda_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingStickerSend()" onerror="showErrorStickerSend()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateStickerSend" class="hidden">
                    <div class="text-center py-8">
                        <flux:icon name="exclamation-triangle" class="w-10 h-10 text-yellow-400 mx-auto mb-2" />
                        <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">No se pudo cargar el sticker A6.</div>
                        <div class="flex justify-center gap-2">
                            <flux:button wire:click="refreshSticker" icon="arrow-path" size="xs"
                                class="bg-amber-500 text-white">
                                Reintentar
                            </flux:button>
                            @if ($encomienda_id)
                                <a href="{{ route('pdf.sticker.a6', ['encomienda' => $encomienda_id]) }}"
                                    target="_blank"
                                    class="flex items-center gap-1 px-2 py-1 text-xs text-amber-600 hover:underline">
                                    <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                    PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                <flux:button wire:click="$set('modalVerSticker', false)" size="xs"
                    class="text-gray-500 dark:text-zinc-400 hover:text-gray-700">
                    Cerrar
                </flux:button>
            </div>
        </div>

        <!-- Scripts minimalistas -->
        <script>
            function hideLoadingStickerSend() {
                document.getElementById('loadingStateStickerSend').classList.add('hidden');
                document.getElementById('pdfContainerStickerSend').classList.remove('hidden');
            }

            function showErrorStickerSend() {
                document.getElementById('loadingStateStickerSend').classList.add('hidden');
                document.getElementById('errorStateStickerSend').classList.remove('hidden');
            }

            function toggleFullscreenStickerSend() {
                const iframe = document.getElementById('pdfFrameStickerSend');
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else if (iframe.webkitRequestFullscreen) {
                    iframe.webkitRequestFullscreen();
                } else if (iframe.msRequestFullscreen) {
                    iframe.msRequestFullscreen();
                }
            }
            // Fallback para mostrar PDF si tarda mucho
            setTimeout(function() {
                if (!document.getElementById('pdfContainerStickerSend').classList.contains('hidden')) {
                    hideLoadingStickerSend();
                }
            }, 10000);
            // Livewire: refrescar sticker
            document.addEventListener('livewire:init', () => {
                Livewire.on('sticker-refreshed', () => {
                    document.getElementById('errorStateStickerSend').classList.add('hidden');
                    document.getElementById('loadingStateStickerSend').classList.remove('hidden');
                    document.getElementById('pdfContainerStickerSend').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameStickerSend');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                });
            });
        </script>
    </flux:modal>

    <!-- Modal de Ver Declaración Jurada (Minimalista) -->
    <flux:modal wire:model="modalVerDeclaracion" :dismissible="true"
        class="w-full max-w-2xl mx-auto">
        <div class="p-4 space-y-3">
            <!-- Encabezado minimalista -->
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <flux:icon name="document-text" class="w-6 h-6 text-indigo-500" />
                    <span class="font-semibold text-gray-800 dark:text-white text-base">Declaración Jurada</span>
                </div>
                @if ($encomienda_id)
                    <a href="{{ route('pdf.declaracion', ['encomienda' => $encomienda_id]) }}" target="_blank"
                        class="flex items-center gap-1 px-2 py-1 text-xs text-indigo-600 hover:underline"
                        title="Abrir PDF en nueva pestaña">
                        <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                        PDF
                    </a>
                @endif
            </div>

            <!-- Contenido -->
            <div>
                <!-- Estado de carga -->
                <div id="loadingStateDeclaracionSend" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando declaración jurada...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerDeclaracionSend" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenDeclaracionSend()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameDeclaracionSend"
                            src="{{ route('pdf.declaracion', ['encomienda' => $encomienda_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingDeclaracionSend()" onerror="showErrorDeclaracionSend()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateDeclaracionSend" class="hidden">
                    <div class="text-center py-8">
                        <flux:icon name="exclamation-triangle" class="w-10 h-10 text-yellow-400 mx-auto mb-2" />
                        <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">No se pudo cargar la declaración jurada.</div>
                        <div class="flex justify-center gap-2">
                            <flux:button wire:click="refreshDeclaracion" icon="arrow-path" size="xs"
                                class="bg-indigo-500 text-white">
                                Reintentar
                            </flux:button>
                            @if ($encomienda_id)
                                <a href="{{ route('pdf.declaracion', ['encomienda' => $encomienda_id]) }}"
                                    target="_blank"
                                    class="flex items-center gap-1 px-2 py-1 text-xs text-indigo-600 hover:underline">
                                    <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                    PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                <flux:button wire:click="$set('modalVerDeclaracion', false)" size="xs"
                    class="text-gray-500 dark:text-zinc-400 hover:text-gray-700">
                    Cerrar
                </flux:button>
            </div>
        </div>

        <!-- Scripts minimalistas -->
        <script>
            function hideLoadingDeclaracionSend() {
                document.getElementById('loadingStateDeclaracionSend').classList.add('hidden');
                document.getElementById('pdfContainerDeclaracionSend').classList.remove('hidden');
            }

            function showErrorDeclaracionSend() {
                document.getElementById('loadingStateDeclaracionSend').classList.add('hidden');
                document.getElementById('errorStateDeclaracionSend').classList.remove('hidden');
            }

            function toggleFullscreenDeclaracionSend() {
                const iframe = document.getElementById('pdfFrameDeclaracionSend');
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else if (iframe.webkitRequestFullscreen) {
                    iframe.webkitRequestFullscreen();
                } else if (iframe.msRequestFullscreen) {
                    iframe.msRequestFullscreen();
                }
            }
            // Fallback para mostrar PDF si tarda mucho
            setTimeout(function() {
                if (!document.getElementById('pdfContainerDeclaracionSend').classList.contains('hidden')) {
                    hideLoadingDeclaracionSend();
                }
            }, 10000);
            // Livewire: refrescar declaración
            document.addEventListener('livewire:init', () => {
                Livewire.on('declaracion-refreshed', () => {
                    document.getElementById('errorStateDeclaracionSend').classList.add('hidden');
                    document.getElementById('loadingStateDeclaracionSend').classList.remove('hidden');
                    document.getElementById('pdfContainerDeclaracionSend').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameDeclaracionSend');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                });
            });
        </script>
    </flux:modal>

    <!-- Modal de Detalles de Encomienda -->
    @if($encomiendaDetalle)
        <flux:modal wire:model="showDetailsModal" :dismissible="true" max-width="4xl">
            <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-blue-50 dark:bg-blue-900/20 rounded flex items-center justify-center">
                        <flux:icon name="information-circle" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Detalles de Encomienda</h2>
                    <span class="text-xs text-gray-500 dark:text-zinc-400">#{{ $encomiendaDetalle->code }}</span>
                </div>
            </div>
            <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <!-- Información General -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                        <div class="font-semibold text-blue-700 dark:text-blue-400 mb-2">Información General</div>
                        <div><span class="font-semibold">Código:</span> {{ $encomiendaDetalle->code }}</div>
                        <div><span class="font-semibold">Estado:</span> 
                            <span class="px-2 py-0.5 rounded text-xs {{ $encomiendaDetalle->estado_encomienda == 'ENVIADO' ? 'bg-blue-100 text-blue-800' : ($encomiendaDetalle->estado_encomienda == 'RECIBIDO' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $encomiendaDetalle->estado_encomienda }}
                            </span>
                        </div>
                        <div><span class="font-semibold">Fecha Creación:</span> 
                            {{ $encomiendaDetalle->fecha_creacion ? \Carbon\Carbon::parse($encomiendaDetalle->fecha_creacion)->format('d/m/Y H:i') : 'N/A' }}
                        </div>
                        @if($encomiendaDetalle->fecha_envio)
                            <div><span class="font-semibold">Fecha Envío:</span> 
                                {{ \Carbon\Carbon::parse($encomiendaDetalle->fecha_envio)->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        @if($encomiendaDetalle->fecha_recepcion)
                            <div><span class="font-semibold">Fecha Recepción:</span> 
                                {{ \Carbon\Carbon::parse($encomiendaDetalle->fecha_recepcion)->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        <div><span class="font-semibold">Monto:</span> S/ {{ number_format($encomiendaDetalle->monto ?? 0, 2) }}</div>
                    </div>

                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                        <div class="font-semibold text-blue-700 dark:text-blue-400 mb-2">Remitente</div>
                        <div><span class="font-semibold">Nombre:</span> {{ $encomiendaDetalle->remitente->name ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Documento:</span> {{ ($encomiendaDetalle->remitente->type_code ?? '') . ': ' . ($encomiendaDetalle->remitente->code ?? 'N/A') }}</div>
                        <div><span class="font-semibold">Teléfono:</span> {{ $encomiendaDetalle->remitente->phone ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Email:</span> {{ $encomiendaDetalle->remitente->email ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Dirección:</span> {{ $encomiendaDetalle->remitente->address ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Sucursal:</span> {{ $encomiendaDetalle->sucursal_remitente->name ?? 'N/A' }}</div>
                    </div>
                </div>

                <!-- Destinatario y Ruta -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                        <div class="font-semibold text-blue-700 dark:text-blue-400 mb-2">Destinatario</div>
                        <div><span class="font-semibold">Nombre:</span> {{ $encomiendaDetalle->destinatario->name ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Documento:</span> {{ ($encomiendaDetalle->destinatario->type_code ?? '') . ': ' . ($encomiendaDetalle->destinatario->code ?? 'N/A') }}</div>
                        <div><span class="font-semibold">Teléfono:</span> {{ $encomiendaDetalle->destinatario->phone ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Email:</span> {{ $encomiendaDetalle->destinatario->email ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Dirección:</span> {{ $encomiendaDetalle->destinatario->address ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Sucursal Destino:</span> {{ $encomiendaDetalle->sucursal_destinatario->name ?? 'N/A' }}</div>
                    </div>

                    @if($encomiendaDetalle->ruta)
                        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                            <div class="font-semibold text-blue-700 dark:text-blue-400 mb-2">Ruta</div>
                            <div><span class="font-semibold">Origen:</span> {{ $encomiendaDetalle->ruta->sucursalOrigen->name ?? 'N/A' }}</div>
                            <div><span class="font-semibold">Destino:</span> {{ $encomiendaDetalle->ruta->sucursalDestino->name ?? 'N/A' }}</div>
                            @if($encomiendaDetalle->ruta->fecha_salida)
                                <div><span class="font-semibold">Fecha Salida:</span> {{ \Carbon\Carbon::parse($encomiendaDetalle->ruta->fecha_salida)->format('d/m/Y') }}</div>
                            @endif
                            @if($encomiendaDetalle->ruta->hora_salida)
                                <div><span class="font-semibold">Hora Salida:</span> {{ \Carbon\Carbon::parse($encomiendaDetalle->ruta->hora_salida)->format('H:i') }}</div>
                            @endif
                            <div><span class="font-semibold">Transportista:</span> {{ $encomiendaDetalle->ruta->transportista->name ?? 'N/A' }}</div>
                            <div><span class="font-semibold">Vehículo:</span> {{ $encomiendaDetalle->ruta->vehiculo->name ?? 'N/A' }} 
                                @if($encomiendaDetalle->ruta->vehiculo && $encomiendaDetalle->ruta->vehiculo->placa)
                                    ({{ $encomiendaDetalle->ruta->vehiculo->placa }})
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Paquetes -->
                @if($encomiendaDetalle->paquetes && count($encomiendaDetalle->paquetes) > 0)
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3">
                        <div class="font-semibold text-blue-700 dark:text-blue-400 mb-2">Paquetes</div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-zinc-800">
                                        <th class="px-2 py-1 text-left">Descripción</th>
                                        <th class="px-2 py-1 text-center">Cant.</th>
                                        <th class="px-2 py-1 text-right">Peso (kg)</th>
                                        <th class="px-2 py-1 text-right">Valor (S/)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($encomiendaDetalle->paquetes as $paquete)
                                        <tr class="border-b border-gray-100 dark:border-zinc-700">
                                            <td class="px-2 py-1">{{ $paquete->descripcion ?? 'N/A' }}</td>
                                            <td class="px-2 py-1 text-center">{{ $paquete->cantidad ?? 0 }}</td>
                                            <td class="px-2 py-1 text-right">{{ number_format($paquete->peso ?? 0, 2) }}</td>
                                            <td class="px-2 py-1 text-right">{{ number_format($paquete->valor ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Información Adicional -->
                <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                    <div class="font-semibold text-blue-700 dark:text-blue-400 mb-2">Información Adicional</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div><span class="font-semibold">Estado Pago:</span> {{ $encomiendaDetalle->estado_pago ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Tipo Pago:</span> {{ $encomiendaDetalle->tipo_pago ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Método Pago:</span> {{ $encomiendaDetalle->metodo_pago ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Tipo Comprobante:</span> {{ $encomiendaDetalle->tipo_comprobante ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Entrega a Domicilio:</span> {{ $encomiendaDetalle->isHome ? 'Sí' : 'No' }}</div>
                        <div><span class="font-semibold">Encomienda Retorno:</span> {{ $encomiendaDetalle->isReturn ? 'Sí' : 'No' }}</div>
                        @if($encomiendaDetalle->isHome && $encomiendaDetalle->direccion_envio)
                            <div class="col-span-2"><span class="font-semibold">Dirección Envío:</span> {{ $encomiendaDetalle->direccion_envio }}</div>
                        @endif
                        @if($encomiendaDetalle->observaciones)
                            <div class="col-span-2"><span class="font-semibold">Observaciones:</span> {{ $encomiendaDetalle->observaciones }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-700 bg-zinc-50/30 dark:bg-zinc-800/30 flex justify-end gap-2">
                <flux:button wire:click="closeDetailsModal" variant="primary" size="xs">
                    Cerrar
                </flux:button>
            </div>
        </flux:modal>
    @endif
</div>
