<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <x-package.encomienda-page-header
        title="Recepción de Encomiendas"
        description="Recibe encomiendas enviadas a esta sucursal"
        icon="inbox"
        iconColor="blue"
        actionLabel="Recibir"
        actionMethod="openReceiveModal()"
        actionIcon="check-circle"
        :actionDisabled="$this->selectedEncomiendasCount === 0"
        :actionCount="$this->selectedEncomiendasCount" />

    <x-package.encomienda-table-wrapper borderColor="blue">
        <x-slot name="filters">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 border-b border-gray-100 dark:border-zinc-700 bg-blue-50/30 dark:bg-blue-900/20">
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
                            class="w-full sm:w-64 max-w-full focus:border-blue-400 dark:focus:border-blue-500 transition" />
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                            <flux:icon name="calendar" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                            Fecha
                        </label>
                        <flux:input 
                            type="date" 
                            wire:model.live="fecha_creacion_filter" 
                            wire:key="fecha-filter-{{ $fecha_creacion_filter }}"
                            size="xs"
                            class="w-full sm:w-36 max-w-full focus:border-blue-400 dark:focus:border-blue-500 transition" />
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

                                            @if($encomienda->doc_factura)
                                                <flux:menu.item icon="document-text"
                                                    wire:click="verInvoicePDF({{ $encomienda->id }})">
                                                    Ver Factura/Boleta
                                                </flux:menu.item>
                                            @endif

                                            @if($encomienda->doc_guia)
                                                <flux:menu.item icon="document-duplicate"
                                                    wire:click="verGuiaPDF({{ $encomienda->id }})">
                                                    Ver Guía
                                                </flux:menu.item>
                                            @endif

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
                                    para recibir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
        </div>

        <x-slot name="pagination">
            {{ $encomiendas->links() }}
        </x-slot>
    </x-package.encomienda-table-wrapper>

    <!-- Modal de Recepción -->
    <flux:modal name="receive" wire:model="showReceiveModal">
        <form wire:submit="receiveEncomiendas">
            <div class="space-y-4">
                <div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Recibir Encomiendas</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Confirma la recepción de {{ $this->selectedEncomiendasCount }} encomienda(s)</p>
                </div>

                <flux:input type="datetime-local" wire:model="fecha_recepcion" label="Fecha de Recepción" required />

                <div class="flex justify-end gap-2">
                    <flux:button type="button" wire:click="closeReceiveModal()" variant="ghost" size="xs">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary" size="xs">Confirmar Recepción</flux:button>
                </div>
            </div>
        </form>
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
                <div id="loadingStateReceive" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando ticket...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerReceive" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenReceive()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameReceive"
                            src="{{ route('encomienda.ticket.pdf', ['id' => $encomienda_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingReceive()" onerror="showErrorReceive()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateReceive" class="hidden">
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
            function hideLoadingReceive() {
                document.getElementById('loadingStateReceive').classList.add('hidden');
                document.getElementById('pdfContainerReceive').classList.remove('hidden');
            }

            function showErrorReceive() {
                document.getElementById('loadingStateReceive').classList.add('hidden');
                document.getElementById('errorStateReceive').classList.remove('hidden');
            }

            function toggleFullscreenReceive() {
                const iframe = document.getElementById('pdfFrameReceive');
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
                if (!document.getElementById('pdfContainerReceive').classList.contains('hidden')) {
                    hideLoadingReceive();
                }
            }, 10000);
            // Livewire: refrescar ticket
            document.addEventListener('livewire:init', () => {
                Livewire.on('ticket-refreshed', () => {
                    document.getElementById('errorStateReceive').classList.add('hidden');
                    document.getElementById('loadingStateReceive').classList.remove('hidden');
                    document.getElementById('pdfContainerReceive').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameReceive');
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
                <div id="loadingStateInvoiceReceive" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando factura...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerInvoiceReceive" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenInvoiceReceive()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameInvoiceReceive"
                            src="{{ route('pdf.invoice.80mm', ['invoice' => $invoice_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingInvoiceReceive()" onerror="showErrorInvoiceReceive()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateInvoiceReceive" class="hidden">
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
            function hideLoadingInvoiceReceive() {
                document.getElementById('loadingStateInvoiceReceive').classList.add('hidden');
                document.getElementById('pdfContainerInvoiceReceive').classList.remove('hidden');
            }

            function showErrorInvoiceReceive() {
                document.getElementById('loadingStateInvoiceReceive').classList.add('hidden');
                document.getElementById('errorStateInvoiceReceive').classList.remove('hidden');
            }

            function toggleFullscreenInvoiceReceive() {
                const iframe = document.getElementById('pdfFrameInvoiceReceive');
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
                if (!document.getElementById('pdfContainerInvoiceReceive').classList.contains('hidden')) {
                    hideLoadingInvoiceReceive();
                }
            }, 10000);
            // Livewire: refrescar invoice
            document.addEventListener('livewire:init', () => {
                Livewire.on('invoice-refreshed', () => {
                    document.getElementById('errorStateInvoiceReceive').classList.add('hidden');
                    document.getElementById('loadingStateInvoiceReceive').classList.remove('hidden');
                    document.getElementById('pdfContainerInvoiceReceive').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameInvoiceReceive');
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
                <div id="loadingStateGuiaReceive" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando guía de remisión...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerGuiaReceive" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenGuiaReceive()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameGuiaReceive"
                            src="{{ route('encomienda.guia.pdf', ['id' => $guia_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingGuiaReceive()" onerror="showErrorGuiaReceive()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateGuiaReceive" class="hidden">
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
            function hideLoadingGuiaReceive() {
                document.getElementById('loadingStateGuiaReceive').classList.add('hidden');
                document.getElementById('pdfContainerGuiaReceive').classList.remove('hidden');
            }

            function showErrorGuiaReceive() {
                document.getElementById('loadingStateGuiaReceive').classList.add('hidden');
                document.getElementById('errorStateGuiaReceive').classList.remove('hidden');
            }

            function toggleFullscreenGuiaReceive() {
                const iframe = document.getElementById('pdfFrameGuiaReceive');
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
                if (!document.getElementById('pdfContainerGuiaReceive').classList.contains('hidden')) {
                    hideLoadingGuiaReceive();
                }
            }, 10000);
            // Livewire: refrescar guía
            document.addEventListener('livewire:init', () => {
                Livewire.on('guia-refreshed', () => {
                    document.getElementById('errorStateGuiaReceive').classList.add('hidden');
                    document.getElementById('loadingStateGuiaReceive').classList.remove('hidden');
                    document.getElementById('pdfContainerGuiaReceive').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameGuiaReceive');
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

