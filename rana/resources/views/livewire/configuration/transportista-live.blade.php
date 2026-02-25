<div class="p-4 w-full">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-zinc-100 overflow-hidden mb-4 p-3">
        <div class="px-4 py-3">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="p-1.5 bg-blue-100 rounded-md">
                            <flux:icon name="user-group" class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-zinc-900">Gestión de Transportistas</h1>
                            <p class="text-zinc-500 text-xs">Administra los transportistas de forma simple y eficiente</p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <flux:button wire:click="openTransportistaModal()" icon="plus" variant="primary" size="xs" class="flex items-center gap-1">
                        Nuevo Transportista
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes -->
    @if (session()->has('message'))
        <div class="mb-4">
            <div class="rounded-md border border-green-200 bg-green-50 px-3 py-2 flex items-start gap-2">
                <div class="flex-shrink-0">
                    <flux:icon name="check-circle" class="w-4 h-4 text-green-600" />
                </div>
                <div class="flex-1">
                    <p class="text-xs font-medium text-green-800">{{ session('message') }}</p>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="text-green-400 hover:text-green-600"
                        onclick="this.parentElement.parentElement.parentElement.remove()">
                        <flux:icon name="x-mark" class="w-3 h-3" />
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Lista de Transportistas -->
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-zinc-100 overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-4 py-3 border-b bg-zinc-50">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900">
                        Transportistas Registrados
                        <span class="ml-2 px-1.5 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">
                            {{ $transportistas->total() }} total
                        </span>
                    </h3>
                    <p class="text-xs text-zinc-500">
                        Mostrando {{ $transportistas->count() }} de {{ $transportistas->total() }} transportistas
                    </p>
                </div>
                <div class="flex gap-2">
                    <flux:input type="search" wire:model.live="search" placeholder="Buscar transportista..." size="xs" class="w-48" />
                    <flux:select wire:model.live="filterStatus" size="xs" class="w-36">
                        <option value="">Todos</option>
                        <option value="1">Solo activos</option>
                        <option value="0">Solo inactivos</option>
                    </flux:select>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 px-4 py-3 bg-blue-50 text-center">
                <div>
                    <div class="text-xs text-zinc-500">Total</div>
                    <div class="text-base font-bold text-zinc-900">{{ $transportistas->total() }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Activos</div>
                    <div class="text-base font-bold text-green-600">{{ $transportistas->where('isActive', true)->count() }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Inactivos</div>
                    <div class="text-base font-bold text-red-600">{{ $transportistas->where('isActive', false)->count() }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Tipos</div>
                    <div class="text-base font-bold text-purple-600">{{ $transportistas->unique('tipo')->count() }}</div>
                </div>
            </div>

            <!-- Tabla de Transportistas -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200">
                    <thead class="bg-zinc-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Transportista</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Documentación</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-zinc-100">
                        @forelse ($transportistas as $transportista)
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <flux:icon name="user-group" class="w-4 h-4 text-blue-600" />
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-zinc-900">{{ $transportista->name }}</div>
                                            <div class="text-xs text-zinc-500">Código: {{ $transportista->type_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1">
                                            <flux:icon name="identification" class="w-3 h-3 text-indigo-500" />
                                            <span class="text-xs font-medium text-zinc-900">DNI:</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ $transportista->dni }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <flux:icon name="document-text" class="w-3 h-3 text-green-500" />
                                            <span class="text-xs font-medium text-zinc-900">Licencia:</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                {{ $transportista->licencia }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $transportista->tipo }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($transportista->isActive)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <flux:icon name="check-circle" class="w-3 h-3 mr-1" />
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <flux:icon name="x-circle" class="w-3 h-3 mr-1" />
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:button
                                            wire:click="openTransportistaModal({{ $transportista->id }})"
                                            icon="pencil"
                                            size="xs"
                                            variant="outline"
                                        >
                                            Editar
                                        </flux:button>
                                        <flux:button
                                            wire:click="confirmDelete({{ $transportista->id }})"
                                            icon="trash"
                                            size="xs"
                                            variant="danger"
                                        >
                                            Eliminar
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-zinc-400">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mb-3">
                                            <flux:icon name="user-group" class="w-6 h-6 text-zinc-400" />
                                        </div>
                                        <h3 class="text-base font-medium text-zinc-900 mb-1">No hay transportistas</h3>
                                        <flux:button wire:click="openTransportistaModal()" icon="plus" variant="primary" size="xs">
                                            Nuevo Transportista
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transportistas->hasPages())
                <div class="px-4 py-3 border-t border-zinc-100 bg-zinc-50">
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-zinc-500">
                            Mostrando {{ $transportistas->firstItem() ?? 0 }} a {{ $transportistas->lastItem() ?? 0 }} de {{ $transportistas->total() }} resultados
                        </div>
                        <div>
                            {{ $transportistas->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Transportista -->
    <flux:modal wire:model="showTransportistaModal" variant="flyout" max-width="2xl">
        <div class="px-4 pt-4 pb-2 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-center gap-2 mb-1">
                <div class="p-1.5 bg-blue-100 rounded-md">
                    <flux:icon name="user-group" class="w-5 h-5 text-blue-600" />
                </div>
                <h2 class="text-lg font-bold text-zinc-900">
                    {{ $editingTransportista ? 'Editar Transportista' : 'Nuevo Transportista' }}
                </h2>
            </div>
        </div>
        <div class="p-4 max-h-[70vh] overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <flux:input wire:model.defer="name" label="Nombre Completo" required size="xs" />
                <flux:input wire:model.defer="type_code" label="Código de Tipo" size="xs" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <flux:input wire:model.defer="dni" label="DNI" required size="xs" />
                <flux:input wire:model.defer="licencia" label="Número de Licencia" required size="xs" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <flux:select wire:model.defer="tipo" label="Tipo de Transportista" size="xs">
                    <option value="">Seleccionar tipo</option>
                    <option value="INTERNO">Interno</option>
                    <option value="EXTERNO">Externo</option>
                </flux:select>
                <div class="flex items-center gap-2 mt-2">
                    <flux:checkbox wire:model.defer="isActive" label="Activo" />
                </div>
            </div>
        </div>
        <div class="px-4 pb-4 pt-2 border-t bg-gray-50 flex justify-end gap-2">
            <flux:button wire:click="closeTransportistaModal" variant="outline" size="xs">
                Cancelar
            </flux:button>
            <flux:button wire:click="saveTransportista" variant="primary" size="xs" icon="check">
                {{ $editingTransportista ? 'Actualizar' : 'Crear' }}
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal de Confirmación de Eliminación -->
    <flux:modal wire:model="showDeleteModal">
        <div class="px-4 pt-4 pb-2 border-b">
            <h2 class="text-lg font-bold text-center text-zinc-900">
                Confirmar Eliminación
            </h2>
        </div>
        <div class="p-4 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <flux:icon name="exclamation-triangle" class="w-6 h-6 text-red-600" />
            </div>
            <h3 class="text-base font-medium text-zinc-900 mb-1">¿Estás seguro?</h3>
            <p class="text-zinc-500 mb-4 text-sm">
                Esta acción no se puede deshacer. El transportista será eliminado permanentemente.
            </p>
        </div>
        <div class="px-4 pb-4 pt-2 border-t flex justify-end space-x-2">
            <flux:button wire:click="cancelDelete" variant="outline" size="xs">
                Cancelar
            </flux:button>
            <flux:button wire:click="deleteTransportista" variant="danger" size="xs">
                Eliminar Definitivamente
            </flux:button>
        </div>
    </flux:modal>
</div>
