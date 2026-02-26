<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                <p class="text-sm text-gray-600">{{ $sub_title }}</p>
            </div>
            <flux:button href="{{ route('facturacion.despatche.create') }}" variant="primary" icon="plus">
                Nuevo
            </flux:button>
        </div>

        <!-- Filtros Mejorados -->
        <div class="px-6 pt-6 pb-2">
            <form wire:submit.prevent class="w-full">
                <div class="flex flex-col md:flex-row md:items-end md:space-x-4 gap-4 flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <flux:input type="search" label="Buscar guía" wire:model.live="search"
                            placeholder="N°, Remitente, Serie, Correlativo..." size="sm" icon="magnifying-glass"
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
                        <flux:select wire:model.live="FiltroSucursalEnvio" label="Sucursal envío" size="sm" class="w-full">
                            <flux:select.option value="">Todas</flux:select.option>
                            @foreach ($sucursales as $sucursal)
                                <flux:select.option value="{{ $sucursal->id }}">{{ $sucursal->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div>
                        <flux:select wire:model.live="FiltroSucursalDestino" label="Sucursal destino" size="sm" class="w-full">
                            <flux:select.option value="">Todas</flux:select.option>
                            @foreach ($sucursales as $sucursal)
                                <flux:select.option value="{{ $sucursal->id }}">{{ $sucursal->name }}</flux:select.option>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remitente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PDF</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">XML/CDR</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($despaches as $despache)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $despache->id }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-semibold text-purple-600">
                                    {{ $despache->serie }}-{{ $despache->correlativo }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="text-xs text-gray-500">{{ $despache->remitente->code }}</div>
                                <div class="font-medium">{{ $despache->remitente->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $despache->fechaEmision ? \Carbon\Carbon::parse($despache->fechaEmision)->format('d-m-Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex gap-1">
                                    <flux:button wire:click="showPdf({{ $despache->id }}, 'a4')"
                                        size="xs" variant="primary" icon="document-text" />
                                    <flux:button wire:click="showPdf({{ $despache->id }}, '80mm')"
                                        size="xs" variant="primary" icon="printer" />
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex gap-1">
                                    @if ($despache->xml_path && $despache->xml_hash)
                                        <flux:button wire:click="xmlDownload({{ $despache->id }})" size="xs"
                                            variant="primary" icon="arrow-down-tray" />
                                    @else
                                        <flux:button wire:click="xmlGenerate({{ $despache->id }})" size="xs"
                                            variant="primary" icon="arrow-path" />
                                    @endif
                                    @if ($despache->cdr_path)
                                        <flux:button wire:click="downloadCdrFile({{ $despache->id }})" size="xs"
                                            variant="primary" icon="arrow-down-tray" />
                                    @else
                                        <flux:button wire:click="sendXmlFile({{ $despache->id }})" size="xs"
                                            variant="primary" icon="paper-airplane" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <flux:dropdown>
                                    <flux:button slot="trigger" size="xs" variant="outline"
                                        icon="ellipsis-vertical" />
                                    <flux:menu>
                                        <flux:menu.item wire:click="showInfo({{ $despache->id }})"
                                            icon="information-circle">Ver información</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No se encontraron guías de remisión
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $despaches->links() }}
        </div>
    </div>

    <!-- Modal de información -->
    @if ($infoModal)
        <flux:modal wire:model="infoModal" name="infoModal">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Información de la Guía</h3>
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

    <!-- Modal de PDF (estilo FluxUI como encomienda-create-live) -->
    <flux:modal wire:model="pdfModal" name="pdfModal" class="w-full max-w-2xl mx-auto">
        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <flux:icon name="document-text" class="w-6 h-6 text-purple-500" />
                    <span class="font-semibold text-gray-800 dark:text-white text-base">{{ $pdfTitle }}</span>
                </div>
                @if ($pdfUrl)
                    <a href="{{ $pdfUrl }}" target="_blank"
                        class="flex items-center gap-1 px-2 py-1 text-xs text-purple-600 hover:underline"
                        title="Abrir PDF en nueva pestaña">
                        <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                        PDF
                    </a>
                @endif
            </div>

            <div>
                <div id="loadingStateDespatchePdf" class="flex items-center justify-center py-8">
                    <div>
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-400 mx-auto mb-2"></div>
                        <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando documento...</p>
                    </div>
                </div>

                <div id="pdfContainerDespatchePdf" class="hidden">
                    <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                        <div class="flex justify-end mb-1">
                            <button type="button" onclick="toggleFullscreen_pdfFrameDespatchePdf()"
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                title="Pantalla completa">
                                <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                            </button>
                        </div>
                        @if ($pdfUrl)
                            <iframe id="pdfFrameDespatchePdf" src="{{ $pdfUrl }}"
                                class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                                onload="hideLoading_loadingStateDespatchePdf()" onerror="showError_errorStateDespatchePdf()"></iframe>
                        @endif
                    </div>
                </div>

                <div id="errorStateDespatchePdf" class="hidden">
                    <div class="text-center py-8">
                        <flux:icon name="exclamation-triangle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                        <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">No se pudo cargar el documento.</div>
                        <div class="flex justify-center gap-2">
                            <flux:button wire:click="refreshPdf" icon="arrow-path" size="xs" class="bg-purple-500 text-white">
                                Reintentar
                            </flux:button>
                            @if ($pdfUrl)
                                <a href="{{ $pdfUrl }}" target="_blank"
                                    class="flex items-center gap-1 px-2 py-1 text-xs text-purple-600 hover:underline">
                                    <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                    PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                <flux:button wire:click="closePdf" size="xs" class="text-gray-500 dark:text-zinc-400 hover:text-gray-700">
                    Cerrar
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function initPdfModalDespatche(config) {
                const { loadingId, containerId, errorId, frameId, refreshEvent } = config;
                window['hideLoading_' + loadingId] = function() {
                    const loadingEl = document.getElementById(loadingId);
                    const containerEl = document.getElementById(containerId);
                    if (loadingEl) loadingEl.classList.add('hidden');
                    if (containerEl) containerEl.classList.remove('hidden');
                };
                window['showError_' + errorId] = function() {
                    const loadingEl = document.getElementById(loadingId);
                    const errorEl = document.getElementById(errorId);
                    if (loadingEl) loadingEl.classList.add('hidden');
                    if (errorEl) errorEl.classList.remove('hidden');
                };
                window['toggleFullscreen_' + frameId] = function() {
                    const iframe = document.getElementById(frameId);
                    if (iframe && iframe.requestFullscreen) iframe.requestFullscreen();
                };
                setTimeout(function() {
                    const containerEl = document.getElementById(containerId);
                    if (containerEl && containerEl.classList.contains('hidden')) window['hideLoading_' + loadingId]();
                }, 10000);
                document.addEventListener('livewire:init', () => {
                    Livewire.on(refreshEvent, () => {
                        const errorEl = document.getElementById(errorId);
                        const loadingEl = document.getElementById(loadingId);
                        const containerEl = document.getElementById(containerId);
                        const iframe = document.getElementById(frameId);
                        if (errorEl) errorEl.classList.add('hidden');
                        if (loadingEl) loadingEl.classList.remove('hidden');
                        if (containerEl) containerEl.classList.add('hidden');
                        if (iframe) iframe.src = iframe.src;
                    });
                    Livewire.on('despatche-pdf-opening', () => {
                        const loadingEl = document.getElementById(loadingId);
                        const containerEl = document.getElementById(containerId);
                        const errorEl = document.getElementById(errorId);
                        if (loadingEl) loadingEl.classList.remove('hidden');
                        if (containerEl) containerEl.classList.add('hidden');
                        if (errorEl) errorEl.classList.add('hidden');
                    });
                });
            }
            initPdfModalDespatche({
                loadingId: 'loadingStateDespatchePdf',
                containerId: 'pdfContainerDespatchePdf',
                errorId: 'errorStateDespatchePdf',
                frameId: 'pdfFrameDespatchePdf',
                refreshEvent: 'despatche-pdf-refreshed'
            });
        });
    </script>
</div>
