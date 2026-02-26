<div class="p-8 w-full">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-md border border-zinc-100 dark:border-zinc-700 overflow-hidden mb-8 p-4">
        <div class="px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                            <x-flux::icon name="banknotes" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Gestión de Caja</h1>
                            <p class="text-zinc-500 dark:text-zinc-400 text-sm">Administra tus movimientos de caja de forma simple y segura
                            </p>
                        </div>
                    </div>

                </div>
                <div class="flex gap-3">
                    <flux:button wire:click="showHistorialModal = true" icon="clock" variant="outline"
                        class="flex items-center gap-2">
                        Historial
                    </flux:button>
                    @if ($cajaActiva)
                        <flux:button wire:click="openCajaModal({{ $cajaActiva->id }})" icon="lock-closed"
                            variant="danger" class="flex items-center gap-2">
                            Cerrar Caja
                        </flux:button>
                    @else
                        <flux:button wire:click="openCajaModal()" icon="banknotes" variant="primary"
                            class="flex items-center gap-2">
                            Abrir Caja
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes -->
    @if (session()->has('message'))
        <div class="mb-6">
            <div class="rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 flex items-start gap-3">
                <div class="flex-shrink-0">
                    <x-flux::icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('message') }}</p>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="text-green-400 dark:text-green-500 hover:text-green-600 dark:hover:text-green-400"
                        onclick="this.parentElement.parentElement.parentElement.remove()">
                        <x-flux::icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Lista de Cajas -->
    <div class="space-y-8">
        @if ($cajaActiva)
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-md border border-zinc-100 dark:border-zinc-700 overflow-hidden">
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                    <div>
                        <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">
                            Caja #{{ $cajaActiva->id }}
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                Activa
                            </span>
                        </h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Usuario: <span class="font-medium">{{ $cajaActiva->user->name }}</span>
                            <span class="mx-2">|</span>
                            Apertura: {{ $cajaActiva->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 px-6 py-4 bg-blue-50 dark:bg-blue-900/20 text-center">
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Apertura</div>
                        <div class="text-lg font-bold text-zinc-900 dark:text-white">
                            S/{{ number_format($cajaActiva->monto_apertura, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Entradas</div>
                        <div class="text-lg font-bold text-green-600 dark:text-green-400">
                            S/{{ number_format($this->getTotalEntries($cajaActiva), 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Salidas</div>
                        <div class="text-lg font-bold text-red-600 dark:text-red-400">
                            S/{{ number_format($this->getTotalExits($cajaActiva), 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Balance</div>
                        <div
                            class="text-lg font-bold {{ $this->getBalance($cajaActiva) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            S/{{ number_format($this->getBalance($cajaActiva), 2) }}
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 p-4">
                    <!-- Entradas -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-base font-semibold text-zinc-900 dark:text-white">Entradas</h4>
                            <flux:button wire:click="openEntryModal()" icon="plus" variant="primary" size="sm"
                                class="flex items-center gap-1">
                                Nueva Entrada
                            </flux:button>
                        </div>
                        @if ($cajaActiva->entries->count() > 0)
                            <div class="divide-y divide-green-100 dark:divide-green-800 rounded-lg border border-green-100 dark:border-green-800 bg-green-50 dark:bg-green-900/20">
                                @foreach ($cajaActiva->entries as $entry)
                                    <div class="flex justify-between items-center px-2 py-1.5">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">{{ $entry->description }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $entry->tipoEntry?->name ?? '-' }} -
                                                {{ ucfirst($entry->metodo_pago) }}</div>
                                            <div class="text-xs text-zinc-400 dark:text-zinc-500">
                                                {{ $entry->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <div class="text-right ml-2 flex-shrink-0">
                                            <div class="text-sm font-bold text-green-600 dark:text-green-400">
                                                S/{{ number_format($entry->monto_entry, 2) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-zinc-400 dark:text-zinc-500">
                                <x-flux::icon name="inbox" class="w-10 h-10 mx-auto mb-2 text-zinc-200 dark:text-zinc-700" />
                                <p class="text-sm">No hay entradas registradas</p>
                            </div>
                        @endif
                    </div>
                    <!-- Salidas -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-base font-semibold text-zinc-900 dark:text-white">Salidas</h4>
                            <flux:button wire:click="openExitModal()" icon="plus" variant="danger" size="sm"
                                class="flex items-center gap-1">
                                Nueva Salida
                            </flux:button>
                        </div>
                        @if ($cajaActiva->exits->count() > 0)
                            <div class="divide-y divide-red-100 dark:divide-red-800 rounded-lg border border-red-100 dark:border-red-800 bg-red-50 dark:bg-red-900/20">
                                @foreach ($cajaActiva->exits as $exit)
                                    <div class="flex justify-between items-center px-2 py-1.5">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">{{ $exit->description }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $exit->tipoExit?->name ?? '-' }} -
                                                {{ ucfirst($exit->metodo_pago) }}</div>
                                            <div class="text-xs text-zinc-400 dark:text-zinc-500">
                                                {{ $exit->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <div class="text-right ml-2 flex-shrink-0">
                                            <div class="text-sm font-bold text-red-600 dark:text-red-400">
                                                S/{{ number_format($exit->monto_exit, 2) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-zinc-400 dark:text-zinc-500">
                                <x-flux::icon name="inbox" class="w-10 h-10 mx-auto mb-2 text-zinc-200 dark:text-zinc-700" />
                                <p class="text-sm">No hay salidas registradas</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-md border border-zinc-100 dark:border-zinc-700 overflow-hidden">
                <div class="px-8 py-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-6">
                        <x-flux::icon name="banknotes" class="w-12 h-12 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-2">No tienes una caja activa</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 mb-6 max-w-md mx-auto">
                        Para comenzar a registrar movimientos de caja, primero debes abrir una caja.
                        Esto te permitirá controlar entradas, salidas y mantener un balance actualizado.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de Caja -->
    <flux:modal wire:model="showCajaModal">
        <div class="px-6 pt-6 pb-2 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-xl font-bold text-center text-zinc-900 dark:text-white">
                {{ $editingCaja ? 'Cerrar Caja' : 'Abrir Caja' }}
            </h2>
        </div>
        <div class="p-8 space-y-6">
            @if (!$editingCaja)
                <flux:input wire:model="monto_apertura" label="Monto de Apertura" type="number" step="0.01"
                    min="0" placeholder="0.00" required />
            @else
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">
                            Balance Calculado
                            <span class="ml-1"
                                title="Este es el balance calculado automáticamente basado en apertura, entradas y salidas.">
                                <svg class="inline w-4 h-4 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="16" x2="12" y2="12" />
                                    <line x1="12" y1="8" x2="12.01" y2="8" />
                                </svg>
                            </span>
                        </label>
                        <div class="bg-zinc-100 dark:bg-zinc-800 rounded px-4 py-2 text-lg font-bold text-zinc-800 dark:text-white">
                            S/{{ number_format($editingCaja ? $editingCaja->monto_apertura + $editingCaja->entries->sum('monto_entry') - $editingCaja->exits->sum('monto_exit') : 0, 2) }}
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Este es el balance calculado automáticamente. Se usará
                            como monto de cierre.</p>
                    </div>
                    <!-- El input de monto_cierre ha sido eliminado -->
                </div>
            @endif
        </div>
        <div class="px-6 pb-6 pt-2 border-t flex justify-end gap-3">
            <flux:button wire:click="closeCajaModal" variant="outline">
                Cancelar
            </flux:button>
            <flux:button wire:click="saveCaja" variant="primary">
                {{ $editingCaja ? 'Cerrar Caja' : 'Abrir Caja' }}
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal de Entrada -->
    <flux:modal wire:model="showEntryModal">
        <div class="px-6 pt-6 pb-2 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-xl font-bold text-center text-zinc-900 dark:text-white">
                {{ $editingEntry ? 'Editar Entrada' : 'Nueva Entrada' }}
            </h2>
        </div>
        <div class="p-8 space-y-6">
            <flux:input wire:model="monto_entry" label="Monto" type="number" step="0.01" min="0"
                placeholder="0.00" required />
            <flux:textarea wire:model="description_entry" label="Descripción" rows="3"
                placeholder="Descripción de la entrada" />
            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="tipo_entry" label="Tipo de Entrada" required>
                    @foreach ($entry_types as $id => $name)
                        <option value="{{ $id }}">{{ ucfirst($name) }}</option>
                    @endforeach
                </flux:select>
                <flux:select wire:model="metodo_pago_entry" label="Método de Pago" required>
                    <flux:select.option value="EFECTIVO">EFECTIVO</flux:select.option>
                    <flux:select.option value="YAPE">YAPE</flux:select.option>
                    <flux:select.option value="TARJETA">TARJETA</flux:select.option>
                    <flux:select.option value="CHEQUE">CHEQUE</flux:select.option>
                    <flux:select.option value="TRANSFERENCIA">TRANSFERENCIA</flux:select.option>
                    <flux:select.option value="OTRO">OTRO</flux:select.option>
                </flux:select>
            </div>
        </div>
        <div class="px-6 pb-6 pt-2 border-t flex justify-end space-x-3">
            <flux:button wire:click="closeEntryModal" variant="outline">
                Cancelar
            </flux:button>
            <flux:button wire:click="saveEntry" variant="primary">
                {{ $editingEntry ? 'Actualizar' : 'Crear' }}
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal de Salida -->
    <flux:modal wire:model="showExitModal">
        <div class="px-6 pt-6 pb-2 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-xl font-bold text-center text-zinc-900 dark:text-white">
                {{ $editingExit ? 'Editar Salida' : 'Nueva Salida' }}
            </h2>
        </div>
        <div class="p-8 space-y-6">
            <flux:input wire:model="monto_exit" label="Monto" type="number" step="0.01" min="0"
                placeholder="0.00" required />
            <flux:textarea wire:model="description_exit" label="Descripción" rows="3"
                placeholder="Descripción de la salida" />
            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="tipo_exit" label="Tipo de Salida" required>
                    @foreach ($exit_types as $id => $name)
                        <option value="{{ $id }}">{{ ucfirst($name) }}</option>
                    @endforeach
                </flux:select>
                <flux:select wire:model="metodo_pago_exit" label="Método de Pago" required>
                    <flux:select.option value="EFECTIVO">EFECTIVO</flux:select.option>
                    <flux:select.option value="YAPE">YAPE</flux:select.option>
                    <flux:select.option value="TARJETA">TARJETA</flux:select.option>
                    <flux:select.option value="CHEQUE">CHEQUE</flux:select.option>
                    <flux:select.option value="TRANSFERENCIA">TRANSFERENCIA</flux:select.option>
                    <flux:select.option value="OTRO">OTRO</flux:select.option>
                </flux:select>
            </div>
        </div>
        <div class="px-6 pb-6 pt-2 border-t flex justify-end space-x-3">
            <flux:button wire:click="closeExitModal" variant="outline">
                Cancelar
            </flux:button>
            <flux:button wire:click="saveExit" variant="danger">
                {{ $editingExit ? 'Actualizar' : 'Crear' }}
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal de Previsualización PDF -->
    <flux:modal wire:model="showPdfModal" max-width="3xl">
        <div class="px-6 pt-4 pb-3 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-flux::icon name="document-text" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        Ticket de Cierre de Caja
                    </h2>
                </div>
                @if ($pdfUrl)
                    <div class="flex items-center gap-2">
                        <button onclick="printPdf('{{ $pdfUrl }}')" type="button"
                            class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                            title="Imprimir">
                            <x-flux::icon name="printer" class="w-4 h-4" />
                        </button>
                        <button onclick="window.open('{{ $pdfUrl }}', '_blank')" type="button"
                            class="p-1.5 text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                            title="Abrir en nueva pestaña">
                            <x-flux::icon name="arrow-top-right-on-square" class="w-4 h-4" />
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <div class="p-4 bg-zinc-50 dark:bg-zinc-800">
            @if ($pdfUrl)
                <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div class="w-full" style="height: 500px; max-height: 60vh;">
                        <iframe id="pdf-iframe" src="{{ $pdfUrl }}#toolbar=0&navpanes=0" 
                            class="w-full h-full" 
                            frameborder="0"
                            style="display: block;">
                        </iframe>
                    </div>
                </div>
            @endif
        </div>
        <div class="px-6 py-3 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 flex justify-end gap-2">
            <flux:button wire:click="closePdfModal" variant="outline" size="sm">
                Cerrar
            </flux:button>
            @if ($pdfUrl)
                <flux:button onclick="printPdf('{{ $pdfUrl }}')" icon="printer" variant="outline" size="sm">
                    Imprimir
                </flux:button>
                <flux:button onclick="window.open('{{ $pdfUrl }}', '_blank')" icon="arrow-down-tray"
                    variant="primary" size="sm">
                    Descargar
                </flux:button>
            @endif
        </div>
    </flux:modal>

    <script>
        function printPdf(url) {
            const iframe = document.getElementById('pdf-iframe');
            if (iframe) {
                try {
                    // Intentar imprimir el iframe directamente
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch (e) {
                    // Si falla por restricciones de seguridad, usar método alternativo
                    const printFrame = document.createElement('iframe');
                    printFrame.style.position = 'fixed';
                    printFrame.style.right = '0';
                    printFrame.style.bottom = '0';
                    printFrame.style.width = '0';
                    printFrame.style.height = '0';
                    printFrame.style.border = '0';
                    printFrame.src = url;
                    document.body.appendChild(printFrame);
                    
                    printFrame.onload = function() {
                        setTimeout(function() {
                            printFrame.contentWindow.focus();
                            printFrame.contentWindow.print();
                            setTimeout(function() {
                                document.body.removeChild(printFrame);
                            }, 1000);
                        }, 500);
                    };
                }
            }
        }
    </script>

    <!-- Modal de Historial de Cajas -->
    <flux:modal variant="flyout" wire:model="showHistorialModal">
        <div class="px-6 pt-6 pb-2 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-xl font-bold text-center text-zinc-900 dark:text-white">
                Historial de Cajas
            </h2>
        </div>
        <div class="p-8 overflow-x-auto">
            <table class="min-w-full text-sm text-zinc-700 dark:text-zinc-300">
                <thead>
                    <tr class="bg-zinc-100 dark:bg-zinc-800">
                        <th class="px-4 py-2 text-left text-zinc-700 dark:text-zinc-300">#</th>
                        <th class="px-4 py-2 text-left text-zinc-700 dark:text-zinc-300">Apertura</th>
                        <th class="px-4 py-2 text-left text-zinc-700 dark:text-zinc-300">Cierre</th>
                        <th class="px-4 py-2 text-left text-zinc-700 dark:text-zinc-300">Monto Apertura</th>
                        <th class="px-4 py-2 text-left text-zinc-700 dark:text-zinc-300">Monto Cierre</th>
                        <th class="px-4 py-2 text-left text-zinc-700 dark:text-zinc-300">Estado</th>
                        <th class="px-4 py-2 text-left text-zinc-700 dark:text-zinc-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historialCajas as $caja)
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <td class="px-4 py-2">{{ $caja->id }}</td>
                            <td class="px-4 py-2">{{ $caja->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">
                                {{ $caja->monto_cierre ? $caja->updated_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-4 py-2">S/{{ number_format($caja->monto_apertura, 2) }}</td>
                            <td class="px-4 py-2">S/{{ number_format($caja->monto_cierre, 2) }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="px-2 py-1 rounded-full text-xs {{ $caja->isActive ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300' }}">
                                    {{ $caja->isActive ? 'Activa' : 'Cerrada' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                @if (!$caja->isActive)
                                    <flux:button wire:click="previewPdf({{ $caja->id }})" icon="document-text"
                                        variant="outline" size="sm" class="flex items-center gap-1">
                                        Ver Ticket
                                    </flux:button>
                                @else
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($historialCajas->isEmpty())
                <div class="text-center text-zinc-400 dark:text-zinc-500 py-8">
                    <x-flux::icon name="inbox" class="w-12 h-12 mx-auto mb-2 text-zinc-200 dark:text-zinc-700" />
                    <p>No hay historial de cajas.</p>
                </div>
            @endif
        </div>
        <div class="px-6 pb-6 pt-2 border-t flex justify-end space-x-3">
            <flux:button wire:click="showHistorialModal = false" variant="outline">
                Cerrar
            </flux:button>
        </div>
    </flux:modal>
</div>
