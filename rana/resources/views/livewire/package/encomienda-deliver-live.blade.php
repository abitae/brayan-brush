<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <x-package.encomienda-page-header title="Entrega de Encomiendas"
        description="Entrega encomiendas recibidas a los destinatarios" icon="gift" iconColor="green" />

    <x-package.encomienda-table-wrapper borderColor="green">
        <x-slot name="filters">
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 border-b border-gray-100 dark:border-zinc-700 bg-green-50/30 dark:bg-green-900/20">
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                            <flux:icon name="magnifying-glass" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                            Buscar
                        </label>
                        <flux:input type="search" wire:model.live="search"
                            placeholder="Código, remitente o destinatario..." size="xs"
                            class="w-full sm:w-64 max-w-full focus:border-green-400 dark:focus:border-green-500 transition" />
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                            <flux:icon name="calendar" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                            Fecha
                        </label>
                        <flux:input type="date" wire:model.live="fecha_creacion_filter"
                            wire:key="fecha-filter-{{ $fecha_creacion_filter }}" size="xs"
                            class="w-full sm:w-36 max-w-full focus:border-green-400 dark:focus:border-green-500 transition" />
                    </div>
                    @if ($rutasDisponibles && $rutasDisponibles->count() > 0)
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                                <flux:icon name="map" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                                Ruta
                            </label>
                            <flux:select wire:model.live="filterRuta" size="xs" class="w-80">
                                @foreach ($rutasDisponibles as $ruta)
                                    <option value="{{ $ruta->id }}">
                                        Ruta #{{ $ruta->id }} - {{ $ruta->sucursalOrigen->name ?? 'N/A' }} →
                                        {{ $ruta->sucursalDestino->name ?? 'N/A' }} |
                                        {{ $ruta->transportista->name ?? 'N/A' }} |
                                        {{ $ruta->vehiculo->placa ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endif
                </div>
            </div>
        </x-slot>

        <div class="overflow-x-auto">
            <table
                class="min-w-full text-xs border border-gray-200 dark:border-zinc-700 rounded-lg overflow-hidden shadow p-2">
                <thead>
                    <tr class="bg-gray-50 dark:bg-zinc-800">
                        <th class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 font-medium">#</th>
                        <th class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 font-medium">Código</th>
                        <th class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 font-medium">Remitente y
                            Destinatario</th>
                        <th class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 font-medium">Destino y Fecha
                        </th>
                        <th class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 font-medium">Monto S/</th>
                        <th class="px-2 py-2 text-center text-gray-500 dark:text-zinc-400 font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($encomiendas as $i => $encomienda)
                        <tr wire:key="encomienda-{{ $encomienda->id }}"
                            class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                            <td class="px-2 py-1 text-gray-400 dark:text-zinc-500">{{ $loop->iteration }}</td>
                            <x-package.encomienda-code-cell :encomienda="$encomienda" color="green" :showPago="true"
                                :showComprobante="true" />
                            <x-package.encomienda-people-cell :encomienda="$encomienda" />
                            <x-package.encomienda-destination-cell :encomienda="$encomienda" :showFechaCreacion="true" />
                            <x-package.encomienda-amount-cell :amount="$encomienda->monto ?? 0" color="green" />
                            <td class="px-2 py-1 text-center">
                                <flux:button size="xs" icon="check-circle"
                                    wire:click="openDeliverModal({{ $encomienda->id }})">
                                    Entregar
                                </flux:button>
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
                                                Ver Factura/Boleta
                                            </flux:menu.item>
                                        @endif

                                        @if ($encomienda->doc_guia)
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
                            <td colspan="6" class="px-2 py-4 text-center text-gray-400 dark:text-zinc-500">No hay
                                encomiendas
                                para entregar.</td>
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

    <!-- Modal de Validación PIN -->
    @if ($encomiendaParaValidar)
        <flux:modal name="pin" wire:model="showPinModal" max-width="md">
            <flux:card>
                <form wire:submit.prevent="validarPin" class="space-y-8">

                    <div class="max-w-64 mx-auto space-y-2">
                        <flux:heading size="lg" class="text-center">Validar PIN</flux:heading>
                        <flux:text class="text-center">Por favor ingresa el PIN de 3 dígitos que recibió el
                            destinatario.</flux:text>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 text-center">Código:
                            {{ $encomiendaParaValidar->code }}</p>
                    </div>

                    <x-package.encomienda-info-card :encomienda="$encomiendaParaValidar" />

                    <flux:otp wire:model="pin_verificacion" length="3" label="PIN" label:sr-only
                        :error:icon="false" error:class="text-center" class="mx-auto" autofocus private />

                    @error('pin_verificacion')
                        <p class="text-xs text-red-500 mt-1 text-center">{{ $message }}</p>
                    @enderror

                    <div class="space-y-4">
                        <flux:button variant="primary" type="submit" class="w-full" icon="check-circle">Validar PIN
                        </flux:button>
                        <flux:button wire:click="closePinModal()" type="button" class="w-full" variant="ghost">
                            Cancelar</flux:button>
                    </div>
                </form>
            </flux:card>
        </flux:modal>
    @endif

    <!-- Modal de Cobro -->
    @if ($showCobroModal)
        <flux:modal name="cobro" wire:model="showCobroModal" max-width="2xl">
            <form wire:submit="procesarCobro">
                <div class="space-y-4">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Cobrar Encomienda</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Código:
                            {{ $encomiendaSeleccionada->code }}</p>
                    </div>
                    <x-package.encomienda-info-card :encomienda="$encomiendaSeleccionada" :showPago="true" :showComprobante="true" />
                    <flux:button type="submit" variant="primary" size="xs">Confirmar Cobro</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
    <!-- Modal de Entrega -->
    @if ($encomiendaSeleccionada)
        <flux:modal name="deliver" wire:model="showDeliverModal" max-width="2xl">
            <form wire:submit="deliverEncomienda">
                <div class="space-y-4">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Entregar Encomienda</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Código:
                            {{ $encomiendaSeleccionada->code }}</p>
                    </div>

                    <x-package.encomienda-info-card :encomienda="$encomiendaSeleccionada" :showPago="true" :showComprobante="true" />
                    
                    <div class="flex justify-end gap-2">
                        <flux:button type="button" wire:click="closeDeliverModal()" variant="ghost" size="xs">
                            Cancelar</flux:button>
                        <flux:button type="submit" variant="primary" size="xs">Confirmar Entrega</flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>
    @endif

    <!-- Modal de Ver Ticket (Minimalista) -->
    <flux:modal wire:model="modalImprimirTicket" :dismissible="true" class="w-full max-w-2xl mx-auto">
        <div class="p-4 space-y-3">
            <!-- Encabezado minimalista -->
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <flux:icon name="document" class="w-6 h-6 text-blue-500" />
                    <span class="font-semibold text-gray-800 dark:text-white text-base">Ticket</span>
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
                <div id="loadingStateTicketDeliver" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando ticket...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerTicketDeliver" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenTicketDeliver()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameTicketDeliver"
                            src="{{ route('encomienda.ticket.pdf', ['id' => $encomienda_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingTicketDeliver()" onerror="showErrorTicketDeliver()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateTicketDeliver" class="hidden">
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
            function hideLoadingTicketDeliver() {
                document.getElementById('loadingStateTicketDeliver').classList.add('hidden');
                document.getElementById('pdfContainerTicketDeliver').classList.remove('hidden');
            }

            function showErrorTicketDeliver() {
                document.getElementById('loadingStateTicketDeliver').classList.add('hidden');
                document.getElementById('errorStateTicketDeliver').classList.remove('hidden');
            }

            function toggleFullscreenTicketDeliver() {
                const iframe = document.getElementById('pdfFrameTicketDeliver');
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
                if (!document.getElementById('pdfContainerTicketDeliver').classList.contains('hidden')) {
                    hideLoadingTicketDeliver();
                }
            }, 10000);
            // Livewire: refrescar ticket
            document.addEventListener('livewire:init', () => {
                Livewire.on('ticket-refreshed', () => {
                    document.getElementById('errorStateTicketDeliver').classList.add('hidden');
                    document.getElementById('loadingStateTicketDeliver').classList.remove('hidden');
                    document.getElementById('pdfContainerTicketDeliver').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameTicketDeliver');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                });
            });
        </script>
    </flux:modal>

    <!-- Modal de Ver Invoice/Factura (Minimalista) -->
    <flux:modal wire:model="modalVerInvoice" :dismissible="true" class="w-full max-w-2xl mx-auto">
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
                <div id="loadingStateInvoiceDeliver" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando factura...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerInvoiceDeliver" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenInvoiceDeliver()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameInvoiceDeliver"
                            src="{{ route('pdf.invoice.80mm', ['invoice' => $invoice_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingInvoiceDeliver()" onerror="showErrorInvoiceDeliver()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateInvoiceDeliver" class="hidden">
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
            function hideLoadingInvoiceDeliver() {
                document.getElementById('loadingStateInvoiceDeliver').classList.add('hidden');
                document.getElementById('pdfContainerInvoiceDeliver').classList.remove('hidden');
            }

            function showErrorInvoiceDeliver() {
                document.getElementById('loadingStateInvoiceDeliver').classList.add('hidden');
                document.getElementById('errorStateInvoiceDeliver').classList.remove('hidden');
            }

            function toggleFullscreenInvoiceDeliver() {
                const iframe = document.getElementById('pdfFrameInvoiceDeliver');
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
                if (!document.getElementById('pdfContainerInvoiceDeliver').classList.contains('hidden')) {
                    hideLoadingInvoiceDeliver();
                }
            }, 10000);
            // Livewire: refrescar invoice
            document.addEventListener('livewire:init', () => {
                Livewire.on('invoice-refreshed', () => {
                    document.getElementById('errorStateInvoiceDeliver').classList.add('hidden');
                    document.getElementById('loadingStateInvoiceDeliver').classList.remove('hidden');
                    document.getElementById('pdfContainerInvoiceDeliver').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameInvoiceDeliver');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                });
            });
        </script>
    </flux:modal>

    <!-- Modal de Ver Guía de Remisión (Minimalista) -->
    <flux:modal wire:model="modalVerGuia" :dismissible="true" class="w-full max-w-2xl mx-auto">
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
                <div id="loadingStateGuiaDeliver" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando guía de remisión...
                        </p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerGuiaDeliver" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenGuiaDeliver()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameGuiaDeliver"
                            src="{{ route('encomienda.guia.pdf', ['id' => $guia_id ?? 0]) }}" class="w-full border-0"
                            style="min-height: 400px; max-height: 60vh;" onload="hideLoadingGuiaDeliver()"
                            onerror="showErrorGuiaDeliver()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateGuiaDeliver" class="hidden">
                    <div class="text-center py-8">
                        <flux:icon name="exclamation-triangle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                        <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">No se pudo cargar la guía de
                            remisión.</div>
                        <div class="flex justify-center gap-2">
                            <flux:button wire:click="refreshGuia" icon="arrow-path" size="xs"
                                class="bg-purple-500 text-white">
                                Reintentar
                            </flux:button>
                            <a href="{{ route('encomienda.guia.pdf', ['id' => $guia_id ?? 0]) }}" target="_blank"
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
            function hideLoadingGuiaDeliver() {
                document.getElementById('loadingStateGuiaDeliver').classList.add('hidden');
                document.getElementById('pdfContainerGuiaDeliver').classList.remove('hidden');
            }

            function showErrorGuiaDeliver() {
                document.getElementById('loadingStateGuiaDeliver').classList.add('hidden');
                document.getElementById('errorStateGuiaDeliver').classList.remove('hidden');
            }

            function toggleFullscreenGuiaDeliver() {
                const iframe = document.getElementById('pdfFrameGuiaDeliver');
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
                if (!document.getElementById('pdfContainerGuiaDeliver').classList.contains('hidden')) {
                    hideLoadingGuiaDeliver();
                }
            }, 10000);
            // Livewire: refrescar guía
            document.addEventListener('livewire:init', () => {
                Livewire.on('guia-refreshed', () => {
                    document.getElementById('errorStateGuiaDeliver').classList.add('hidden');
                    document.getElementById('loadingStateGuiaDeliver').classList.remove('hidden');
                    document.getElementById('pdfContainerGuiaDeliver').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameGuiaDeliver');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                });
            });
        </script>
    </flux:modal>

    <!-- Modal de Detalles de Encomienda -->
    @if ($encomiendaDetalle)
        <flux:modal wire:model="showDetailsModal" :dismissible="true" max-width="4xl">
            <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-green-50 dark:bg-green-900/20 rounded flex items-center justify-center">
                        <flux:icon name="information-circle" class="w-4 h-4 text-green-600 dark:text-green-400" />
                    </div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Detalles de Encomienda</h2>
                    <span class="text-xs text-gray-500 dark:text-zinc-400">#{{ $encomiendaDetalle->code }}</span>
                </div>
            </div>
            <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <!-- Información General -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                        <div class="font-semibold text-green-700 dark:text-green-400 mb-2">Información General</div>
                        <div><span class="font-semibold">Código:</span> {{ $encomiendaDetalle->code }}</div>
                        <div><span class="font-semibold">Estado:</span>
                            <span
                                class="px-2 py-0.5 rounded text-xs {{ $encomiendaDetalle->estado_encomienda == 'ENVIADO' ? 'bg-blue-100 text-blue-800' : ($encomiendaDetalle->estado_encomienda == 'RECIBIDO' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $encomiendaDetalle->estado_encomienda }}
                            </span>
                        </div>
                        <div><span class="font-semibold">Fecha Creación:</span>
                            {{ $encomiendaDetalle->fecha_creacion ? \Carbon\Carbon::parse($encomiendaDetalle->fecha_creacion)->format('d/m/Y H:i') : 'N/A' }}
                        </div>
                        @if ($encomiendaDetalle->fecha_envio)
                            <div><span class="font-semibold">Fecha Envío:</span>
                                {{ \Carbon\Carbon::parse($encomiendaDetalle->fecha_envio)->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        @if ($encomiendaDetalle->fecha_recepcion)
                            <div><span class="font-semibold">Fecha Recepción:</span>
                                {{ \Carbon\Carbon::parse($encomiendaDetalle->fecha_recepcion)->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        @if ($encomiendaDetalle->fecha_entrega)
                            <div><span class="font-semibold">Fecha Entrega:</span>
                                {{ \Carbon\Carbon::parse($encomiendaDetalle->fecha_entrega)->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        <div><span class="font-semibold">Monto:</span> S/
                            {{ number_format($encomiendaDetalle->monto ?? 0, 2) }}</div>
                    </div>

                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                        <div class="font-semibold text-green-700 dark:text-green-400 mb-2">Remitente</div>
                        <div><span class="font-semibold">Nombre:</span>
                            {{ $encomiendaDetalle->remitente->name ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Documento:</span>
                            {{ ($encomiendaDetalle->remitente->type_code ?? '') . ': ' . ($encomiendaDetalle->remitente->code ?? 'N/A') }}
                        </div>
                        <div><span class="font-semibold">Teléfono:</span>
                            {{ $encomiendaDetalle->remitente->phone ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Email:</span>
                            {{ $encomiendaDetalle->remitente->email ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Dirección:</span>
                            {{ $encomiendaDetalle->remitente->address ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Sucursal:</span>
                            {{ $encomiendaDetalle->sucursal_remitente->name ?? 'N/A' }}</div>
                    </div>
                </div>

                <!-- Destinatario y Ruta -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                        <div class="font-semibold text-green-700 dark:text-green-400 mb-2">Destinatario</div>
                        <div><span class="font-semibold">Nombre:</span>
                            {{ $encomiendaDetalle->destinatario->name ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Documento:</span>
                            {{ ($encomiendaDetalle->destinatario->type_code ?? '') . ': ' . ($encomiendaDetalle->destinatario->code ?? 'N/A') }}
                        </div>
                        <div><span class="font-semibold">Teléfono:</span>
                            {{ $encomiendaDetalle->destinatario->phone ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Email:</span>
                            {{ $encomiendaDetalle->destinatario->email ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Dirección:</span>
                            {{ $encomiendaDetalle->destinatario->address ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Sucursal:</span>
                            {{ $encomiendaDetalle->sucursal_destinatario->name ?? 'N/A' }}</div>
                    </div>

                    @if ($encomiendaDetalle->ruta)
                        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                            <div class="font-semibold text-green-700 dark:text-green-400 mb-2">Ruta</div>
                            <div><span class="font-semibold">Origen:</span>
                                {{ $encomiendaDetalle->ruta->sucursalOrigen->name ?? 'N/A' }}</div>
                            <div><span class="font-semibold">Destino:</span>
                                {{ $encomiendaDetalle->ruta->sucursalDestino->name ?? 'N/A' }}</div>
                            <div><span class="font-semibold">Transportista:</span>
                                {{ $encomiendaDetalle->ruta->transportista->name ?? 'N/A' }}</div>
                            <div><span class="font-semibold">Vehículo:</span>
                                {{ $encomiendaDetalle->ruta->vehiculo->placa ?? 'N/A' }}</div>
                            <div><span class="font-semibold">Fecha Salida:</span>
                                {{ $encomiendaDetalle->ruta->fecha_salida ? \Carbon\Carbon::parse($encomiendaDetalle->ruta->fecha_salida)->format('d/m/Y') : 'N/A' }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Paquetes -->
                @if ($encomiendaDetalle->paquetes && $encomiendaDetalle->paquetes->count() > 0)
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs">
                        <div class="font-semibold text-green-700 dark:text-green-400 mb-2">Paquetes
                            ({{ $encomiendaDetalle->paquetes->count() }})</div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-zinc-700">
                                        <th class="text-left py-1">Descripción</th>
                                        <th class="text-right py-1">Cantidad</th>
                                        <th class="text-right py-1">Peso</th>
                                        <th class="text-right py-1">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($encomiendaDetalle->paquetes as $paquete)
                                        <tr class="border-b border-gray-100 dark:border-zinc-800">
                                            <td class="py-1">{{ $paquete->description ?? 'N/A' }}</td>
                                            <td class="text-right py-1">{{ $paquete->cantidad ?? 0 }}</td>
                                            <td class="text-right py-1">{{ number_format($paquete->peso ?? 0, 2) }} kg
                                            </td>
                                            <td class="text-right py-1">S/
                                                {{ number_format($paquete->amount ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Información Adicional -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                        <div class="font-semibold text-green-700 dark:text-green-400 mb-2">Información Adicional</div>
                        <div><span class="font-semibold">Estado Pago:</span>
                            {{ $encomiendaDetalle->estado_pago ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Tipo Pago:</span>
                            {{ $encomiendaDetalle->tipo_pago ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Método Pago:</span>
                            {{ $encomiendaDetalle->metodo_pago ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Comprobante:</span>
                            {{ $encomiendaDetalle->tipo_comprobante ?? 'N/A' }}</div>
                        <div><span class="font-semibold">Entrega a Domicilio:</span>
                            {{ $encomiendaDetalle->isHome ? 'Sí' : 'No' }}</div>
                        <div><span class="font-semibold">Retorno:</span>
                            {{ $encomiendaDetalle->isReturn ? 'Sí' : 'No' }}</div>
                        @if ($encomiendaDetalle->isHome && $encomiendaDetalle->direccion_envio)
                            <div class="col-span-2"><span class="font-semibold">Dirección Envío:</span>
                                {{ $encomiendaDetalle->direccion_envio }}</div>
                        @endif
                        @if ($encomiendaDetalle->observaciones)
                            <div class="col-span-2"><span class="font-semibold">Observaciones:</span>
                                {{ $encomiendaDetalle->observaciones }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-700 flex justify-end">
                <flux:button wire:click="closeDetailsModal()" variant="ghost" size="xs">Cerrar</flux:button>
            </div>
        </flux:modal>
    @endif

    <!-- Modal de Cobro (para CONTRA ENTREGA) -->
    @if ($encomiendaCobro)
        <flux:modal wire:model="showCobroModal" :dismissible="true" max-width="2xl">
            <form wire:submit="procesarCobro">
                <div class="space-y-4">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Cobrar Encomienda</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Código: {{ $encomiendaCobro->code }}</p>
                    </div>

                    <x-package.encomienda-info-card :encomienda="$encomiendaCobro" />

                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-lg">
                        <div class="text-xs space-y-2">
                            <div><span class="font-semibold">Monto a cobrar:</span> S/
                                {{ number_format($encomiendaCobro->monto ?? 0, 2) }}</div>
                            <div><span class="font-semibold">Cliente:</span>
                                {{ $encomiendaCobro->destinatario->name ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <flux:select wire:model="tipoComprobante" label="Tipo de Comprobante" required>
                        <option value="BOLETA">Boleta de Venta</option>
                        <option value="FACTURA">Factura</option>
                    </flux:select>

                    <flux:select wire:model="metodoPago" label="Método de Pago" required>
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="YAPE">Yape</option>
                        <option value="TARJETA">Tarjeta</option>
                        <option value="TRANSFERENCIA">Transferencia</option>
                        <option value="CHEQUE">Cheque</option>
                        <option value="OTRO">Otro</option>
                    </flux:select>

                    <div class="flex justify-end gap-2">
                        <flux:button type="button" wire:click="closeCobroModal()" variant="ghost" size="xs">
                            Cancelar</flux:button>
                        <flux:button type="submit" variant="primary" size="xs" color="emerald">Procesar Cobro
                        </flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
