<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <x-package.encomienda-page-header title="Entrega a Domicilio" description="Entrega encomiendas recibidas a domicilio"
        icon="home" iconColor="cyan" />

    <x-package.encomienda-table-wrapper borderColor="cyan">
        <x-slot name="filters">
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 border-b border-gray-100 dark:border-zinc-700 bg-cyan-50/30 dark:bg-cyan-900/20">
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                            <flux:icon name="magnifying-glass" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                            Buscar
                        </label>
                        <flux:input type="search" wire:model.live="search"
                            placeholder="Código, remitente o destinatario..." size="xs"
                            class="w-full sm:w-64 max-w-full focus:border-cyan-400 dark:focus:border-cyan-500 transition" />
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                            <flux:icon name="calendar" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                            Fecha
                        </label>
                        <flux:input type="date" wire:model.live="fecha_creacion_filter"
                            wire:key="fecha-filter-{{ $fecha_creacion_filter }}" size="xs"
                            class="w-full sm:w-36 max-w-full focus:border-cyan-400 dark:focus:border-cyan-500 transition" />
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
                            <x-package.encomienda-code-cell :encomienda="$encomienda" color="cyan" :showPago="true"
                                :showComprobante="true" />
                            <x-package.encomienda-people-cell :encomienda="$encomienda" />
                            <x-package.encomienda-destination-cell :encomienda="$encomienda" :showFechaCreacion="true" />
                            <x-package.encomienda-amount-cell :amount="$encomienda->monto ?? 0" color="cyan" />
                            <td class="px-2 py-1 text-center">
                                <flux:dropdown>
                                    <flux:button icon:trailing="bars-3" size="xs" color="zinc">
                                    </flux:button>

                                    <flux:menu>
                                        <flux:menu.item icon="check-circle"
                                            wire:click="openDeliverModal({{ $encomienda->id }})">
                                            Entregar Encomienda
                                        </flux:menu.item>

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
                            <td colspan="7" class="px-2 py-4 text-center text-gray-400 dark:text-zinc-500">No hay
                                encomiendas
                                para entrega a domicilio.</td>
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

    <!-- Modal de Entrega -->
    @if ($encomiendaSeleccionada)
        <flux:modal name="deliver" wire:model="showDeliverModal">
            <form wire:submit="deliverEncomienda">
                <div class="space-y-4">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Entregar Encomienda a
                            Domicilio</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Código: {{ $encomiendaSeleccionada->code }}
                        </p>
                    </div>

                    <x-package.encomienda-info-card :encomienda="$encomiendaSeleccionada" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:input label="Descuento (S/)" wire:model="monto_descuento_entrega" type="number"
                                min="0" step="0.01" size="xs" />
                            @error('monto_descuento_entrega')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <flux:textarea label="Motivo del descuento" wire:model="motivo_descuento_entrega"
                                rows="2" size="xs" />
                            @error('motivo_descuento_entrega')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-cyan-50 dark:bg-cyan-900/20 p-3 rounded-lg">
                        <p class="text-xs text-cyan-700 dark:text-cyan-400">
                            <flux:icon name="information-circle" class="w-4 h-4 inline mr-1" />
                            Dirección de entrega: {{ $encomiendaSeleccionada->direccion_envio ?? 'N/A' }}
                        </p>
                    </div>

                    <flux:input type="datetime-local" wire:model="fecha_entrega" label="Fecha de Entrega" required />

                    @if (
                        $pinValido &&
                            ($encomiendaSeleccionada->estado_pago == 'CONTRA ENTREGA' ||
                                $encomiendaSeleccionada->estado_pago == 'ENVIO PAGADO') &&
                            !$encomiendaSeleccionada->doc_factura)
                        <div
                            class="w-full mt-4 bg-white dark:bg-zinc-900 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-zinc-700">
                            <h3
                                class="text-sm font-semibold text-zinc-900 dark:text-white mb-4 flex items-center gap-2">
                                <flux:icon name="document-text" class="w-5 h-5 text-blue-600" />
                                Datos de Facturación
                            </h3>

                            <!-- Tipo de comprobante y Tipo de pago -->
                            <div class="flex flex-col md:flex-row gap-4 mb-4">
                                <div class="w-full md:w-1/2">
                                    <flux:select label="Tipo de comprobante" wire:model.live="tipo_comprobante"
                                        size="xs" required>
                                        <flux:select.option value="TICKET">TICKET</flux:select.option>
                                        <flux:select.option value="BOLETA">BOLETA</flux:select.option>
                                        <flux:select.option value="FACTURA">FACTURA</flux:select.option>
                                    </flux:select>
                                </div>
                                <div class="w-full md:w-1/2">
                                    <flux:select label="Tipo de pago" wire:model.live="tipo_pago" size="xs"
                                        required>
                                        <flux:select.option value="CONTADO">CONTADO</flux:select.option>
                                        <flux:select.option value="CREDITO">CREDITO</flux:select.option>
                                    </flux:select>
                                </div>
                            </div>

                            @if ($tipo_pago == 'CONTADO')
                                <div class="mb-4">
                                    <flux:select label="Método de pago" wire:model.live="metodo_pago" size="xs"
                                        required>
                                        <flux:select.option value="EFECTIVO">EFECTIVO</flux:select.option>
                                        <flux:select.option value="YAPE">YAPE</flux:select.option>
                                        <flux:select.option value="TARJETA">TARJETA</flux:select.option>
                                        <flux:select.option value="CHEQUE">CHEQUE</flux:select.option>
                                        <flux:select.option value="TRANSFERENCIA">TRANSFERENCIA</flux:select.option>
                                        <flux:select.option value="OTRO">OTRO</flux:select.option>
                                    </flux:select>
                                </div>
                            @endif

                            @if ($tipo_comprobante != 'TICKET')
                                <!-- Campos de datos de facturación con búsqueda de documentos -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Columna 1: Documento y Dirección -->
                                    <div class="flex flex-col gap-4">
                                        <div class="flex gap-2">
                                            <flux:input.group>
                                                <flux:select class="min-w-[90px]" wire:model="type_code_facturacion"
                                                    size="xs">
                                                    <flux:select.option value="DNI">DNI</flux:select.option>
                                                    <flux:select.option value="RUC">RUC</flux:select.option>
                                                    <flux:select.option value="CE">CE</flux:select.option>
                                                    <flux:select.option value="PASAPORTE">PASAPORTE
                                                    </flux:select.option>
                                                </flux:select>
                                                <flux:input class="w-full" wire:model="code_facturacion"
                                                    placeholder="Documento" size="xs" />
                                                <flux:button wire:click="searchFacturacion" icon="magnifying-glass"
                                                    size="xs" type="button">
                                                    Buscar
                                                </flux:button>
                                            </flux:input.group>
                                        </div>
                                        @error('code_facturacion')
                                            <p class="text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        <flux:input placeholder="Dirección" wire:model="address_facturacion"
                                            size="xs" />
                                        <flux:select placeholder="Ubigeo" wire:model="ubigeo_facturacion"
                                            size="xs">
                                            <flux:select.option value=""></flux:select.option>
                                            @if (isset($ubigeos) && is_iterable($ubigeos))
                                                @foreach ($ubigeos as $ubigeo)
                                                    @if (isset($ubigeo->ubigeo2) && isset($ubigeo->texto_ubigeo))
                                                        <flux:select.option value="{{ $ubigeo->ubigeo2 }}">
                                                            {{ $ubigeo->texto_ubigeo }}</flux:select.option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </flux:select>
                                    </div>
                                    <!-- Columna 2: Razón Social, Teléfono y Email -->
                                    <div class="flex flex-col gap-4">
                                        <flux:input placeholder="Razón Social o Nombre" wire:model="name_facturacion"
                                            size="xs" required />
                                        <div class="grid grid-cols-2 gap-2">
                                            <flux:input placeholder="Teléfono" wire:model="phone_facturacion"
                                                size="xs" />
                                            <flux:input placeholder="Email" wire:model="email_facturacion"
                                                size="xs" type="email" />
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="flex justify-end gap-2">
                        <flux:button type="button" wire:click="closeDeliverModal()" variant="ghost" size="xs">
                            Cancelar</flux:button>
                        <flux:button type="submit" variant="primary" size="xs" :disabled="!$pinValido">
                            Confirmar Entrega
                        </flux:button>
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
                <div id="loadingStateTicketHome" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando ticket...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerTicketHome" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenTicketHome()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameTicketHome"
                            src="{{ route('encomienda.ticket.pdf', ['id' => $encomienda_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingTicketHome()" onerror="showErrorTicketHome()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateTicketHome" class="hidden">
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
            function hideLoadingTicketHome() {
                document.getElementById('loadingStateTicketHome').classList.add('hidden');
                document.getElementById('pdfContainerTicketHome').classList.remove('hidden');
            }

            function showErrorTicketHome() {
                document.getElementById('loadingStateTicketHome').classList.add('hidden');
                document.getElementById('errorStateTicketHome').classList.remove('hidden');
            }

            function toggleFullscreenTicketHome() {
                const iframe = document.getElementById('pdfFrameTicketHome');
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
                if (!document.getElementById('pdfContainerTicketHome').classList.contains('hidden')) {
                    hideLoadingTicketHome();
                }
            }, 10000);
            // Livewire: refrescar ticket
            document.addEventListener('livewire:init', () => {
                Livewire.on('ticket-refreshed', () => {
                    document.getElementById('errorStateTicketHome').classList.add('hidden');
                    document.getElementById('loadingStateTicketHome').classList.remove('hidden');
                    document.getElementById('pdfContainerTicketHome').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameTicketHome');
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
                <div id="loadingStateInvoiceHome" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando factura...</p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerInvoiceHome" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenInvoiceHome()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameInvoiceHome"
                            src="{{ route('pdf.invoice.80mm', ['invoice' => $invoice_id ?? 0]) }}"
                            class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                            onload="hideLoadingInvoiceHome()" onerror="showErrorInvoiceHome()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateInvoiceHome" class="hidden">
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
            function hideLoadingInvoiceHome() {
                document.getElementById('loadingStateInvoiceHome').classList.add('hidden');
                document.getElementById('pdfContainerInvoiceHome').classList.remove('hidden');
            }

            function showErrorInvoiceHome() {
                document.getElementById('loadingStateInvoiceHome').classList.add('hidden');
                document.getElementById('errorStateInvoiceHome').classList.remove('hidden');
            }

            function toggleFullscreenInvoiceHome() {
                const iframe = document.getElementById('pdfFrameInvoiceHome');
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
                if (!document.getElementById('pdfContainerInvoiceHome').classList.contains('hidden')) {
                    hideLoadingInvoiceHome();
                }
            }, 10000);
            // Livewire: refrescar invoice
            document.addEventListener('livewire:init', () => {
                Livewire.on('invoice-refreshed', () => {
                    document.getElementById('errorStateInvoiceHome').classList.add('hidden');
                    document.getElementById('loadingStateInvoiceHome').classList.remove('hidden');
                    document.getElementById('pdfContainerInvoiceHome').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameInvoiceHome');
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
                <div id="loadingStateGuiaHome" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando guía de remisión...
                        </p>
                    </div>
                </div>

                <!-- PDF -->
                <div id="pdfContainerGuiaHome" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreenGuiaHome()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        <iframe id="pdfFrameGuiaHome"
                            src="{{ route('encomienda.guia.pdf', ['id' => $guia_id ?? 0]) }}" class="w-full border-0"
                            style="min-height: 400px; max-height: 60vh;" onload="hideLoadingGuiaHome()"
                            onerror="showErrorGuiaHome()">
                        </iframe>
                    </div>
                </div>

                <!-- Error -->
                <div id="errorStateGuiaHome" class="hidden">
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
            function hideLoadingGuiaHome() {
                document.getElementById('loadingStateGuiaHome').classList.add('hidden');
                document.getElementById('pdfContainerGuiaHome').classList.remove('hidden');
            }

            function showErrorGuiaHome() {
                document.getElementById('loadingStateGuiaHome').classList.add('hidden');
                document.getElementById('errorStateGuiaHome').classList.remove('hidden');
            }

            function toggleFullscreenGuiaHome() {
                const iframe = document.getElementById('pdfFrameGuiaHome');
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
                if (!document.getElementById('pdfContainerGuiaHome').classList.contains('hidden')) {
                    hideLoadingGuiaHome();
                }
            }, 10000);
            // Livewire: refrescar guía
            document.addEventListener('livewire:init', () => {
                Livewire.on('guia-refreshed', () => {
                    document.getElementById('errorStateGuiaHome').classList.add('hidden');
                    document.getElementById('loadingStateGuiaHome').classList.remove('hidden');
                    document.getElementById('pdfContainerGuiaHome').classList.add('hidden');
                    const iframe = document.getElementById('pdfFrameGuiaHome');
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
                    <div class="w-6 h-6 bg-cyan-50 dark:bg-cyan-900/20 rounded flex items-center justify-center">
                        <flux:icon name="information-circle" class="w-4 h-4 text-cyan-600 dark:text-cyan-400" />
                    </div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Detalles de Encomienda</h2>
                    <span class="text-xs text-gray-500 dark:text-zinc-400">#{{ $encomiendaDetalle->code }}</span>
                </div>
            </div>
            <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <!-- Información General -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-2">
                        <div class="font-semibold text-cyan-700 dark:text-cyan-400 mb-2">Información General</div>
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
                        <div class="font-semibold text-cyan-700 dark:text-cyan-400 mb-2">Remitente</div>
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
                        <div class="font-semibold text-cyan-700 dark:text-cyan-400 mb-2">Destinatario</div>
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
                            <div class="font-semibold text-cyan-700 dark:text-cyan-400 mb-2">Ruta</div>
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
                        <div class="font-semibold text-cyan-700 dark:text-cyan-400 mb-2">Paquetes
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
                        <div class="font-semibold text-cyan-700 dark:text-cyan-400 mb-2">Información Adicional</div>
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
