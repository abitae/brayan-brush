<div class="p-4 w-full">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-900 dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-100 dark:border-zinc-700 dark:border-zinc-700 overflow-hidden mb-4 p-3">
        <div class="px-4 py-3">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="p-1.5 bg-blue-100 dark:bg-blue-900/20 rounded-md">
                            <flux:icon name="users" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-zinc-900 dark:text-white dark:text-white">Gestión de Clientes</h1>
                            <p class="text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 text-xs">Administra los clientes de forma simple y eficiente</p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <flux:button wire:click="openCustomerModal()" icon="plus" variant="primary" size="xs" class="flex items-center gap-1">
                        Nuevo Cliente
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes -->
    @if (session()->has('message'))
        <div class="mb-4">
            <div class="rounded-md border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-3 py-2 flex items-start gap-2">
                <div class="flex-shrink-0">
                    <flux:icon name="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400" />
                </div>
                <div class="flex-1">
                    <p class="text-xs font-medium text-green-800 dark:text-green-300">{{ session('message') }}</p>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="text-green-400 dark:text-green-500 hover:text-green-600 dark:hover:text-green-400"
                        onclick="this.parentElement.parentElement.parentElement.remove()">
                        <flux:icon name="x-mark" class="w-3 h-3" />
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Lista de Clientes -->
    <div class="space-y-4">
        <div class="bg-white dark:bg-zinc-900 dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-100 dark:border-zinc-700 dark:border-zinc-700 overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 dark:bg-zinc-800">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white dark:text-white">
                        Clientes Registrados
                        <span class="ml-2 px-1.5 py-0.5 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                            {{ $customers->total() }} total
                        </span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500">
                        Mostrando {{ $customers->count() }} de {{ $customers->total() }} clientes
                    </p>
                </div>
                <div class="flex gap-2">
                    <flux:input type="search" wire:model.live="search" placeholder="Buscar cliente..." size="xs" class="w-48" />
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
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 dark:text-zinc-500">Total</div>
                    <div class="text-base font-bold text-zinc-900 dark:text-white">{{ $customers->total() }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 dark:text-zinc-500">Activos</div>
                    <div class="text-base font-bold text-green-600">{{ $customers->where('isActive', true)->count() }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 dark:text-zinc-500">Inactivos</div>
                    <div class="text-base font-bold text-red-600">{{ $customers->where('isActive', false)->count() }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 dark:text-zinc-500">Tipos</div>
                    <div class="text-base font-bold text-purple-600">{{ $customers->unique('type_code')->count() }}</div>
                </div>
            </div>

            <!-- Tabla de Clientes -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Contacto</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Dirección</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-100">
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 dark:bg-zinc-800 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <flux:icon name="users" class="w-4 h-4 text-blue-600" />
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white dark:text-white">{{ $customer->name }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500">Código: {{ $customer->code }}</div>
                                            @if($customer->type_code)
                                                <div class="text-xs text-zinc-400 dark:text-zinc-500">Tipo: {{ $customer->type_code }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($customer->phone || $customer->email)
                                        <div class="text-sm text-zinc-900 dark:text-white">
                                            @if($customer->phone)
                                                <div class="flex items-center gap-1">
                                                    <flux:icon name="phone" class="w-3 h-3 text-zinc-400 dark:text-zinc-500" />
                                                    {{ $customer->phone }}
                                                </div>
                                            @endif
                                            @if($customer->email)
                                                <div class="flex items-center gap-1">
                                                    <flux:icon name="envelope" class="w-3 h-3 text-zinc-400 dark:text-zinc-500" />
                                                    {{ $customer->email }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-zinc-400 dark:text-zinc-500">Sin contacto</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($customer->address)
                                        <div class="text-sm text-zinc-900 dark:text-white">{{ $customer->address }}</div>
                                        @if($customer->texto_ubigeo)
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400 dark:text-zinc-500">{{ $customer->texto_ubigeo }}</div>
                                        @elseif($customer->ubigeo)
                                            <div class="text-xs text-zinc-400 dark:text-zinc-500">UBIGEO: {{ $customer->ubigeo }}</div>
                                        @endif
                                    @else
                                        <span class="text-sm text-zinc-400 dark:text-zinc-500">Sin dirección</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($customer->isActive)
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
                                            wire:click="openCustomerModal({{ $customer->id }})"
                                            icon="pencil"
                                            size="xs"
                                            variant="outline"
                                        >
                                            Editar
                                        </flux:button>
                                        <flux:button
                                            wire:click="confirmDelete({{ $customer->id }})"
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
                                <td colspan="5" class="px-4 py-8 text-center text-zinc-400 dark:text-zinc-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mb-3">
                                            <flux:icon name="users" class="w-6 h-6 text-zinc-400 dark:text-zinc-500" />
                                        </div>
                                        <h3 class="text-base font-medium text-zinc-900 dark:text-white mb-1">No hay clientes</h3>
                                        <p class="text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 mb-3 text-sm">Comienza registrando tu primer cliente</p>
                                        <flux:button wire:click="openCustomerModal()" icon="plus" variant="primary" size="xs">
                                            Nuevo Cliente
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
                <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 dark:text-zinc-500">
                            Mostrando {{ $customers->firstItem() ?? 0 }} a {{ $customers->lastItem() ?? 0 }} de {{ $customers->total() }} resultados
                        </div>
                        <div>
                            {{ $customers->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Cliente -->
    <flux:modal wire:model="showCustomerModal" variant="flyout" max-width="3xl">
        <!-- Header del Modal -->
        <div class="px-6 pt-6 pb-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-center gap-3 mb-2">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <flux:icon name="users" class="w-6 h-6 text-blue-600" />
                </div>
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                    {{ $editingCustomer ? 'Editar Cliente' : 'Nuevo Cliente' }}
                </h2>
            </div>
            <p class="text-center text-sm text-zinc-600">
                {{ $editingCustomer ? 'Modifica la información del cliente seleccionado' : 'Completa la información para registrar un nuevo cliente' }}
            </p>
        </div>

        <!-- Contenido del Modal -->
        <div class="p-6 max-h-[75vh] overflow-y-auto">

            <!-- Sección: Información Básica -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="identification" class="w-5 h-5 text-blue-600" />
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Información Básica</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model.defer="name" label="Nombre Completo" required size="sm" />
                    <flux:input wire:model.defer="code" label="Código Único" required size="sm" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <flux:input wire:model.defer="type_code" label="Código de Tipo" size="sm" />
                    <flux:input wire:model.defer="phone" label="Teléfono" type="tel" size="sm" />
                </div>
            </div>

            <!-- Sección: Información de Contacto -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="envelope" class="w-5 h-5 text-green-600" />
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Información de Contacto</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model.defer="email" label="Correo Electrónico" type="email" size="sm" />
                    <div class="relative">
                        <flux:input
                            wire:model.live="ubigeoSearch"
                            label="Ubigeo"
                            placeholder="{{ $ubigeo || $texto_ubigeo ? 'Ubigeo seleccionado' : 'Buscar departamento, provincia o distrito...' }}"
                            size="sm"
                            class="{{ $ubigeo || $texto_ubigeo ? 'bg-green-50 border-green-300' : '' }}"
                        />
                        <!-- Campos ocultos para almacenar los valores del ubigeo -->
                        <input type="hidden" wire:model.defer="ubigeo" />
                        <input type="hidden" wire:model.defer="texto_ubigeo" />

                        @if($ubigeoSearch && strlen($ubigeoSearch) >= 2 && !$ubigeo && !$texto_ubigeo)
                            <div class="absolute right-3 top-8">
                                @if($isSearchingUbigeo)
                                    <div class="animate-spin">
                                        <flux:icon name="arrow-path" class="w-4 h-4 text-blue-500" />
                                    </div>
                                @else
                                    <flux:icon name="magnifying-glass" class="w-4 h-4 text-zinc-400 dark:text-zinc-500" />
                                @endif
                            </div>
                        @endif
                        @if($ubigeo || $texto_ubigeo)
                            <div class="absolute right-3 top-8">
                                <flux:button
                                    wire:click="clearUbigeo"
                                    icon="x-mark"
                                    variant="outline"
                                    size="xs"
                                    class="h-6 w-6 p-0"
                                    title="Limpiar ubigeo"
                                >
                                </flux:button>
                            </div>
                        @endif
                        @if($ubigeo && $texto_ubigeo)
                            <div class="absolute left-3 top-8">
                                <flux:icon name="check-circle" class="w-4 h-4 text-green-600" />
                            </div>
                        @endif
                        @if($showUbigeoDropdown && count($ubigeoResults) > 0)
                            <div class="absolute z-50 w-full mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                @foreach($ubigeoResults as $result)
                                    <div
                                        wire:click="selectUbigeo('{{ $result->ubigeo2 }}', '{{ $result->texto_ubigeo }}')"
                                        class="px-4 py-3 hover:bg-blue-50 cursor-pointer text-sm border-b border-zinc-100 dark:border-zinc-700 last:border-b-0 transition-colors"
                                    >
                                        <div class="font-medium text-zinc-900 dark:text-white">{{ $result->texto_ubigeo }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 mt-1">{{ $result->dpto }} - {{ $result->prov }} - {{ $result->distrito }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @if(!$ubigeo && !$texto_ubigeo)
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 mt-1">
                            <flux:icon name="information-circle" class="w-3 h-3 inline mr-1" />
                            Escribe para buscar departamento, provincia o distrito
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sección: Dirección -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="home" class="w-5 h-5 text-orange-600" />
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Dirección</h3>
                </div>
                <flux:input wire:model.defer="address" label="Dirección Completa" size="sm" placeholder="Ingrese la dirección completa del cliente" />
            </div>

            <!-- Sección: Estado -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="check-circle" class="w-5 h-5 text-emerald-600" />
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Estado del Cliente</h3>
                </div>
                <div class="flex items-center gap-3 p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                    <flux:checkbox wire:model.defer="isActive" label="Cliente activo" />
                    <div class="text-sm text-emerald-700">
                        <span class="font-medium">Activo:</span> El cliente podrá realizar operaciones en el sistema
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer del Modal -->
        <div class="px-6 pb-6 pt-4 border-t bg-zinc-50 dark:bg-zinc-800 flex justify-end gap-3">
            <flux:button icon="x-mark" wire:click="closeCustomerModal" variant="outline" size="sm" class="px-6">
                Cancelar
            </flux:button>
            <flux:button icon="check" wire:click="saveCustomer" variant="primary" size="sm" class="px-6">
                {{ $editingCustomer ? 'Actualizar Cliente' : 'Crear Cliente' }}
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal de Confirmación de Eliminación -->
    <flux:modal wire:model="showDeleteModal">
        <div class="px-4 pt-4 pb-2 border-b">
            <h2 class="text-lg font-bold text-center text-zinc-900 dark:text-white">
                Confirmar Eliminación
            </h2>
        </div>
        <div class="p-4 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <flux:icon name="exclamation-triangle" class="w-6 h-6 text-red-600" />
            </div>
            <h3 class="text-base font-medium text-zinc-900 dark:text-white mb-1">¿Estás seguro?</h3>
            <p class="text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 mb-4 text-sm">
                Esta acción no se puede deshacer. El cliente será eliminado permanentemente.
            </p>
        </div>
        <div class="px-4 pb-4 pt-2 border-t flex justify-end space-x-2">
            <flux:button wire:click="cancelDelete" variant="outline" size="xs">
                Cancelar
            </flux:button>
            <flux:button wire:click="deleteCustomer" variant="danger" size="xs">
                Eliminar Definitivamente
            </flux:button>
        </div>
    </flux:modal>
</div>

<script>
document.addEventListener('livewire:init', () => {
    // Cerrar dropdown de ubigeo cuando se hace clic fuera
    document.addEventListener('click', function(event) {
        const ubigeoDropdown = document.querySelector('[wire\\:model\\.live="ubigeoSearch"]').closest('.relative');
        if (!ubigeoDropdown.contains(event.target)) {
            @this.closeUbigeoDropdown();
        }
    });
});
</script>
