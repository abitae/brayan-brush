<div class="p-4 md:p-6 lg:p-8 w-full max-w-7xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-700">
                <flux:heading size="xl">{{ $title }}</flux:heading>
                <flux:subheading class="mt-1">{{ $sub_title }}</flux:subheading>
            </div>

            <div class="p-6">
                <!-- Filtros de fecha -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <flux:input type="date" wire:model.live="date_ini" label="Fecha Inicio" size="sm" />
                    <flux:input type="date" wire:model.live="date_end" label="Fecha Fin" size="sm" />
                    <flux:select wire:model.live="selectedTipe" label="Tipo" size="sm">
                        <flux:select.option value="Y">Año</flux:select.option>
                        <flux:select.option value="M">Mes</flux:select.option>
                        <flux:select.option value="D">Día</flux:select.option>
                    </flux:select>
                </div>

                <!-- Tarjetas de estadísticas principales -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <flux:icon name="cube" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-1">Total Encomiendas</div>
                        <div class="text-2xl font-bold text-blue-900 dark:text-blue-300">{{ $totalEncomiendas }}</div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <flux:icon name="gift" class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="text-sm font-medium text-green-600 dark:text-green-400 mb-1">Entregadas</div>
                        <div class="text-2xl font-bold text-green-900 dark:text-green-300">{{ $encomiendasEntregadas }}</div>
                    </div>

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <flux:icon name="inbox" class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="text-sm font-medium text-yellow-600 dark:text-yellow-400 mb-1">Recibidas</div>
                        <div class="text-2xl font-bold text-yellow-900 dark:text-yellow-300">{{ $encomiendasRecibidas }}</div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <flux:icon name="paper-airplane" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="text-sm font-medium text-purple-600 dark:text-purple-400 mb-1">Enviadas</div>
                        <div class="text-2xl font-bold text-purple-900 dark:text-purple-300">{{ $encomiendasEnviadas }}</div>
                    </div>
                </div>

                <!-- Estadísticas financieras -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <flux:icon name="banknotes" class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="text-sm font-medium text-green-600 dark:text-green-400 mb-1">Total Ingresos</div>
                        <div class="text-2xl font-bold text-green-900 dark:text-green-300">S/ {{ number_format($totalIngresos, 2) }}</div>
                    </div>

                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <flux:icon name="clock" class="w-5 h-5 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="text-sm font-medium text-red-600 dark:text-red-400 mb-1">Pendientes de Cobro</div>
                        <div class="text-2xl font-bold text-red-900 dark:text-red-300">S/ {{ number_format($pendientesCobro, 2) }}</div>
                    </div>
                </div>

                <!-- Gráficos de estados -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
                        <flux:heading size="lg" class="mb-4">Encomiendas por Estado</flux:heading>
                        <div class="space-y-3">
                            @foreach($estadosData as $estado => $total)
                                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-700 last:border-0">
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $estado }}</span>
                                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $total }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
                        <flux:heading size="lg" class="mb-4">Métodos de Pago</flux:heading>
                        <div class="space-y-3">
                            @foreach($metodosPagoData as $metodo => $total)
                                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-700 last:border-0">
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $metodo }}</span>
                                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $total }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

