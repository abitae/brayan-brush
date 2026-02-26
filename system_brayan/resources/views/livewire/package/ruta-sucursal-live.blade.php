<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <!-- Header compacto -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700">
        <!-- Header Mejorado -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-6 py-5 border-b border-gray-200 dark:border-zinc-700 bg-gradient-to-r from-blue-50 dark:from-blue-900/20 via-white dark:via-zinc-900 to-blue-50 dark:to-blue-900/20">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 shadow-sm">
                    <flux:icon name="map" class="w-7 h-7 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-blue-900 dark:text-blue-300 leading-tight flex items-center gap-2">
                        Rutas de Sucursal
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-xs text-blue-700 dark:text-blue-300 font-semibold border border-blue-200 dark:border-blue-800">
                            <flux:icon name="arrow-path" class="w-4 h-4 mr-1 text-blue-500 dark:text-blue-400" />
                            Gestión
                        </span>
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Administra y visualiza las rutas de salida de tu sucursal.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-2 md:mt-0">
                <flux:button wire:click="openModal" variant="primary" size="sm" icon:leading="plus">
                    Nueva ruta
                </flux:button>
            </div>
        </div>
        <!-- Content -->
        <div class="p-6">
            <!-- Estadísticas compactas -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                <div class="bg-white dark:bg-zinc-800 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 flex flex-col items-center shadow-sm">
                    <flux:icon name="list-bullet" class="w-6 h-6 text-zinc-400 dark:text-zinc-500 mb-1" />
                    <div class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $estadisticas['total'] }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Total</div>
                </div>
                <div class="bg-white dark:bg-zinc-800 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 flex flex-col items-center shadow-sm">
                    <flux:icon name="check-circle" class="w-6 h-6 text-green-500 dark:text-green-400 mb-1" />
                    <div class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $estadisticas['activas'] }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Activas</div>
                </div>
                <div class="bg-white dark:bg-zinc-800 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 flex flex-col items-center shadow-sm">
                    <flux:icon name="x-circle" class="w-6 h-6 text-red-400 dark:text-red-500 mb-1" />
                    <div class="text-lg font-semibold text-red-600 dark:text-red-400">{{ $estadisticas['inactivas'] }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Inactivas</div>
                </div>
                <div class="bg-white dark:bg-zinc-800 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 flex flex-col items-center shadow-sm">
                    <flux:icon name="pause-circle" class="w-6 h-6 text-yellow-500 dark:text-yellow-400 mb-1" />
                    <div class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ $estadisticas['suspendidas'] }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Suspendidas</div>
                </div>
                <div class="bg-white dark:bg-zinc-800 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 flex flex-col items-center shadow-sm">
                    <flux:icon name="check-badge" class="w-6 h-6 text-blue-500 dark:text-blue-400 mb-1" />
                    <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $estadisticas['completadas'] }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Completadas</div>
                </div>
            </div>
        </div>
    </div>


    <!-- Filtros compactos -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <flux:input type="search" wire:model.live="search" placeholder="Buscar..." size="xs" />
            <flux:select wire:model.live="filterEstado" size="xs">
                <option value="">Estado</option>
                @foreach ($estados as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="filterSucursalDestino" size="xs">
                <option value="">Destino</option>
                @foreach ($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}">{{ $sucursal->name }}</option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="perPage" size="xs">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </flux:select>
        </div>

        @if ($search || $filterEstado || $filterSucursalDestino)
            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-700">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Filtros:</span>
                @if ($search)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                        "{{ $search }}"
                        <button wire:click="$set('search', '')" class="ml-1">×</button>
                    </span>
                @endif
                @if ($filterEstado)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                        {{ $estados[$filterEstado] }}
                        <button wire:click="$set('filterEstado', '')" class="ml-1">×</button>
                    </span>
                @endif
                @if ($filterSucursalDestino)
                    @php $sucursalSeleccionada = $sucursales->where('id', $filterSucursalDestino)->first(); @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                        {{ $sucursalSeleccionada->name ?? 'N/A' }}
                        <button wire:click="$set('filterSucursalDestino', '')" class="ml-1">×</button>
                    </span>
                @endif
                <flux:button wire:click="limpiarFiltros" variant="ghost" size="xs">Limpiar
                </flux:button>
            </div>
        @endif
    </div>

    <!-- Tabla compacta -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-zinc-600 dark:text-zinc-300">Ruta</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-zinc-600 dark:text-zinc-300">Transportista</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-zinc-600 dark:text-zinc-300">Vehículo</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-zinc-600 dark:text-zinc-300">Horario</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-zinc-600 dark:text-zinc-300">Encomiendas</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-zinc-600 dark:text-zinc-300">Estado</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-zinc-600 dark:text-zinc-300">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-700">
                    @forelse ($rutas as $ruta)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800">
                            <td class="px-3 py-2">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $ruta->sucursalOrigen->name }} → {{ $ruta->sucursalDestino->name }}
                                </div>
                                <div class="text-xs text-zinc-400 dark:text-zinc-500">{{ $ruta->dia_semana }}</div>
                            </td>
                            <td class="px-3 py-2">
                                <div class="text-sm text-zinc-900 dark:text-white">
                                    {{ $ruta->transportista->name ?? 'N/A' }}</div>
                                <div class="text-xs text-zinc-400 dark:text-zinc-500">{{ $ruta->transportista->phone ?? '' }}
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                <div class="text-sm text-zinc-900 dark:text-white">{{ $ruta->vehiculo->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-zinc-400 dark:text-zinc-500">{{ $ruta->vehiculo->plate ?? '' }}
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                <div class="text-sm text-zinc-900 dark:text-white">
                                    {{ $ruta->hora_salida ? \Carbon\Carbon::parse($ruta->hora_salida)->format('H:i') : 'N/A' }}
                                </div>
                                <div class="text-xs text-zinc-400 dark:text-zinc-500">
                                    {{ $ruta->fecha_salida ? \Carbon\Carbon::parse($ruta->fecha_salida)->format('d/m/Y') : '' }}
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="inline-flex items-center justify-center px-2 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800">
                                    <flux:icon name="cube" class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-1" />
                                    <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">
                                        {{ $ruta->encomiendas_count ?? 0 }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if ($ruta->estado_ruta === 'ACTIVA') bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300
                                    @elseif($ruta->estado_ruta === 'INACTIVA') bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300
                                    @elseif($ruta->estado_ruta === 'SUSPENDIDA') bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300
                                    @elseif($ruta->estado_ruta === 'COMPLETADO') bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300
                                    @else bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 @endif">
                                    {{ $estados[$ruta->estado_ruta] ?? $ruta->estado_ruta }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button wire:click="editRuta({{ $ruta->id }})" variant="ghost"
                                        size="xs" icon="pencil" />
                                    <flux:button wire:click="toggleEstado({{ $ruta->id }})" variant="ghost"
                                        size="xs" :icon="$ruta->estado_ruta === 'ACTIVA' ? 'pause' : 'play'" />
                                    <flux:button wire:click="openDeleteModal({{ $ruta->id }})" variant="ghost"
                                        size="xs" icon="trash" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center">
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">No se encontraron rutas</div>
                                <flux:button wire:click="openModal" variant="ghost" size="xs" class="mt-2">
                                    Crear primera ruta
                                </flux:button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rutas instanceof \Illuminate\Pagination\LengthAwarePaginator && $rutas->hasPages())
            <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-700">
                {{ $rutas->links() }}
            </div>
        @endif
    </div>

    <!-- Modal compacto -->
    <flux:modal wire:model="showModal" variant="flyout" max-width="xl">
        <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">
                {{ $editingRuta ? 'Editar Ruta' : 'Nueva Ruta' }}
            </h2>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="p-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded text-sm text-zinc-600 dark:text-zinc-300">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Origen:</span><br>
                    {{ $sucursales->where('id', $sucursal_origen_id)->first()->name ?? 'N/A' }}
                </div>
                <flux:select wire:model.live="sucursal_destino_id" label="Destino" required size="xs">
                    <option value="">Seleccionar destino</option>
                    @foreach ($sucursales as $sucursal)
                        @if($sucursal->id != $sucursal_origen_id)
                            <option value="{{ $sucursal->id }}">{{ $sucursal->name }}</option>
                        @endif
                    @endforeach
                </flux:select>
                @error('sucursal_destino_id')
                    <div class="col-span-2 text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
                @enderror
                <flux:select wire:model.defer="transportista_id" label="Transportista" required size="xs">
                    <option value="">Seleccionar transportista</option>
                    @foreach ($transportistas as $transportista)
                        <option value="{{ $transportista->id }}">{{ $transportista->name }}</option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.defer="vehiculo_id" label="Vehículo" required size="xs">
                    <option value="">Seleccionar vehículo</option>
                    @foreach ($vehiculos as $vehiculo)
                        <option value="{{ $vehiculo->id }}">{{ $vehiculo->name }} -
                            {{ $vehiculo->placa }}</option>
                    @endforeach
                </flux:select>
                <flux:input wire:model.live="fecha_salida" label="Fecha" type="date" required size="xs" />
                <flux:input wire:model.defer="hora_salida" label="Hora" type="time" required size="xs" />
                <div class="p-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded text-sm text-zinc-600 dark:text-zinc-300">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Día:</span><br>
                    {{ $diasSemana[$dia_semana] ?? 'N/A' }}
                </div>
                <flux:select wire:model.defer="estado_ruta" label="Estado" required size="xs">
                    @foreach ($estados as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </flux:select>
            </div>
            <flux:textarea wire:model.defer="observaciones" label="Observaciones" size="xs" rows="2" />
            <div class="mt-3">
                <flux:checkbox wire:model.defer="isActive" label="Ruta activa" />
            </div>
        </div>
        <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-700 flex justify-end gap-2">
            <flux:button wire:click="closeModal" variant="ghost" size="xs">Cancelar</flux:button>
            <flux:button wire:click="saveRuta" variant="primary" size="xs">
                {{ $editingRuta ? 'Actualizar' : 'Crear' }}
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal de eliminación -->
    <flux:modal wire:model="showDeleteModal" variant="flyout" max-width="sm">
        <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Eliminar Ruta</h2>
        </div>
        <div class="p-4">
            @if ($rutaToDelete)
                <p class="text-sm text-zinc-600 dark:text-zinc-300 mb-3">
                    ¿Eliminar ruta de <strong>{{ $rutaToDelete->sucursalOrigen->name }}</strong> a
                    <strong>{{ $rutaToDelete->sucursalDestino->name }}</strong>?
                </p>
            @endif
        </div>
        <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-700 flex justify-end gap-2">
            <flux:button wire:click="closeDeleteModal" variant="ghost" size="xs">Cancelar
            </flux:button>
            <flux:button wire:click="deleteRuta" variant="danger" size="xs">Eliminar</flux:button>
        </div>
    </flux:modal>
</div>
