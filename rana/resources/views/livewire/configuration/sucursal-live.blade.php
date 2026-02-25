<div class="p-8 w-full">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-md border border-zinc-100 overflow-hidden mb-8 p-4">
        <div class="px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <flux:icon name="building-office" class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-zinc-900">Gestión de Sucursales</h1>
                            <p class="text-zinc-500 text-sm">Administra las sucursales de tu empresa de forma simple y eficiente</p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <flux:button wire:click="openSucursalModal()" icon="plus" variant="primary" class="flex items-center gap-2">
                        Nueva Sucursal
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes -->
    @if (session()->has('message'))
        <div class="mb-6">
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3">
                <div class="flex-shrink-0">
                    <flux:icon name="check-circle" class="w-5 h-5 text-green-600" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="text-green-400 hover:text-green-600"
                        onclick="this.parentElement.parentElement.parentElement.remove()">
                        <flux:icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Lista de Sucursales -->
    <div class="space-y-8">
        <div class="bg-white rounded-xl shadow-md border border-zinc-100 overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-6 py-4 border-b bg-zinc-50">
                <div>
                    <h3 class="text-xl font-semibold text-zinc-900">
                        Sucursales Registradas
                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                            {{ $sucursales->total() }} total
                        </span>
                    </h3>
                    <p class="text-sm text-zinc-500">
                        Mostrando {{ $sucursales->count() }} de {{ $sucursales->total() }} sucursales
                    </p>
                </div>
                                    <div class="flex gap-2">
                        <flux:input type="search" wire:model.live="search" placeholder="Buscar sucursal..." class="w-64" />
                        <flux:select wire:model.live="filterStatus" class="w-48">
                            <option value="">Todos</option>
                            <option value="1">Solo activas</option>
                            <option value="0">Solo inactivas</option>
                        </flux:select>
                    </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 px-6 py-4 bg-blue-50 text-center">
                <div>
                    <div class="text-xs text-zinc-500">Total</div>
                    <div class="text-lg font-bold text-zinc-900">{{ $sucursales->total() }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Activas</div>
                    <div class="text-lg font-bold text-green-600">{{ $sucursales->where('isActive', true)->count() }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Inactivas</div>
                    <div class="text-lg font-bold text-red-600">{{ $sucursales->where('isActive', false)->count() }}</div>
                </div>
                <div>
                    <div class="text-xs text-zinc-500">Departamentos</div>
                    <div class="text-lg font-bold text-purple-600">{{ $sucursales->unique('departamento')->count() }}</div>
                </div>
            </div>

            <!-- Tabla de Sucursales -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200">
                    <thead class="bg-zinc-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Sucursal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Código SUNAT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Color</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Ubicación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Contacto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-zinc-100">
                        @forelse ($sucursales as $sucursal)
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <flux:icon name="building-office" class="w-5 h-5 text-blue-600" />
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-zinc-900">{{ $sucursal->name }}</div>
                                            <div class="text-sm text-zinc-500">Código: {{ $sucursal->code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $sucursal->codeSunat ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="inline-block w-5 h-5 rounded-full border border-zinc-200" style="background: {{ $sucursal->color ?? '#3B82F6' }}"></span>
                                        <span class="text-xs text-zinc-700">{{ $sucursal->color ?? '-' }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900">{{ $sucursal->address }}</div>
                                    @if($sucursal->departamento)
                                        <div class="text-sm text-zinc-500">{{ $sucursal->departamento }}, {{ $sucursal->provincia ?? '' }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($sucursal->phone || $sucursal->email)
                                        <div class="text-sm text-zinc-900">
                                            @if($sucursal->phone)
                                                <div class="flex items-center gap-1">
                                                    <flux:icon name="phone" class="w-3 h-3 text-zinc-400" />
                                                    {{ $sucursal->phone }}
                                                </div>
                                            @endif
                                            @if($sucursal->email)
                                                <div class="flex items-center gap-1">
                                                    <flux:icon name="envelope" class="w-3 h-3 text-zinc-400" />
                                                    {{ $sucursal->email }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-zinc-400">Sin contacto</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($sucursal->isActive)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <flux:icon name="check-circle" class="w-3 h-3 mr-1" />
                                            Activa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <flux:icon name="x-circle" class="w-3 h-3 mr-1" />
                                            Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            wire:click="openSucursalModal({{ $sucursal->id }})"
                                            icon="pencil"
                                            size="sm"
                                            variant="outline"
                                        >
                                            Editar
                                        </flux:button>
                                        <flux:button
                                            wire:click="confirmDelete({{ $sucursal->id }})"
                                            icon="trash"
                                            size="sm"
                                            variant="danger"
                                        >
                                            Eliminar
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-zinc-400">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-zinc-100 rounded-full flex items-center justify-center mb-4">
                                            <flux:icon name="building-office" class="w-8 h-8 text-zinc-400" />
                                        </div>
                                        <h3 class="text-lg font-medium text-zinc-900 mb-2">No hay sucursales</h3>
                                        <p class="text-zinc-500 mb-4">Comienza creando tu primera sucursal</p>
                                        <flux:button wire:click="openSucursalModal()" icon="plus" variant="primary">
                                            Crear Sucursal
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($sucursales->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100 bg-zinc-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-zinc-500">
                            Mostrando {{ $sucursales->firstItem() ?? 0 }} a {{ $sucursales->lastItem() ?? 0 }} de {{ $sucursales->total() }} resultados
                        </div>
<div>
                            {{ $sucursales->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Sucursal -->
    <flux:modal wire:model="showSucursalModal" variant="flyout" max-width="4xl">
        <div class="px-6 pt-6 pb-2 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-center gap-3 mb-2">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <flux:icon name="building-office" class="w-6 h-6 text-blue-600" />
                </div>
                <h2 class="text-xl font-bold text-zinc-900">
                    {{ $editingSucursal ? 'Editar Sucursal' : 'Nueva Sucursal' }}
                </h2>
            </div>
            <p class="text-sm text-zinc-600 text-center">
                {{ $editingSucursal ? 'Actualiza la información de la sucursal' : 'Completa la información para crear una nueva sucursal' }}
            </p>
        </div>

        <div class="p-8 max-h-[80vh] overflow-y-auto">
            <!-- Información Básica -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="building-office" class="w-5 h-5 text-blue-600" />
                    <h3 class="text-lg font-semibold text-zinc-900">Información Básica</h3>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model.defer="name" label="Nombre de la Sucursal" required size="sm" />
                        <flux:input wire:model.defer="code" label="Código Único" required size="sm" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <flux:input wire:model.defer="codeSunat" label="Código SUNAT" required size="sm" />
                        <flux:input wire:model.defer="igv" label="IGV (%)" type="number" step="0.01" min="0" max="100" required size="sm" />
                    </div>
                    <div class="mt-4">
                        <flux:input wire:model.defer="color" label="Color de Identificación" type="color" size="sm" />
                    </div>
                </div>
            </div>

            <!-- Configuración Fiscal -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="document-text" class="w-5 h-5 text-green-600" />
                    <h3 class="text-lg font-semibold text-zinc-900">Configuración Fiscal</h3>
                </div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model.defer="serieFactura" label="Serie Factura" required size="sm" />
                        <flux:input wire:model.defer="serieBoleta" label="Serie Boleta" required size="sm" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <flux:input wire:model.defer="serieGuiaRemision" label="Serie Guía de Remisión" required size="sm" />
                        <flux:input wire:model.defer="serieNotaCreditoFactura" label="Serie Nota Crédito Factura" required size="sm" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <flux:input wire:model.defer="serieNotaCreditoBoleta" label="Serie Nota Crédito Boleta" required size="sm" />
                        <flux:input wire:model.defer="serieNotaDebitoFactura" label="Serie Nota Débito Factura" required size="sm" />
                    </div>
                    <div class="mt-4">
                        <flux:input wire:model.defer="serieNotaDebitoBoleta" label="Serie Nota Débito Boleta" required size="sm" />
                    </div>
                </div>
            </div>

            <!-- Dirección -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="map-pin" class="w-5 h-5 text-purple-600" />
                    <h3 class="text-lg font-semibold text-zinc-900">Dirección</h3>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                    <flux:input wire:model.defer="address" label="Dirección Completa" required size="sm" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <flux:input wire:model.defer="departamento" label="Departamento" required size="sm" />
                        <flux:input wire:model.defer="provincia" label="Provincia" required size="sm" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <flux:input wire:model.defer="distrito" label="Distrito" required size="sm" />
                        <flux:input wire:model.defer="urbanizacion" label="Urbanización" required size="sm" />
                    </div>
                    <div class="mt-4">
                        <flux:input wire:model.defer="ubigeo" label="Código UBIGEO" required size="sm" />
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="phone" class="w-5 h-5 text-orange-600" />
                    <h3 class="text-lg font-semibold text-zinc-900">Información de Contacto</h3>
                </div>
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model.defer="phone" label="Teléfono" type="tel" size="sm" />
                        <flux:input wire:model.defer="email" label="Correo Electrónico" type="email" size="sm" />
                    </div>
                </div>
            </div>

            <!-- Estado -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="signal" class="w-5 h-5 text-gray-600" />
                    <h3 class="text-lg font-semibold text-zinc-900">Estado</h3>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <flux:checkbox wire:model.defer="isActive" label="Sucursal activa" />
                    <p class="text-xs text-gray-500 mt-2">
                        Las sucursales inactivas no aparecerán en las opciones de selección
                    </p>
                </div>
            </div>
        </div>

        <div class="px-6 pb-6 pt-2 border-t bg-gray-50 flex justify-between items-center">
            <div class="text-sm text-gray-500">
                <flux:icon name="information-circle" class="w-4 h-4 inline mr-1" />
                Los campos marcados con * son obligatorios
            </div>
            <div class="flex gap-3">
                <flux:button wire:click="closeSucursalModal" variant="outline" size="sm">
                    Cancelar
                </flux:button>
                <flux:button wire:click="saveSucursal" variant="primary" size="sm" icon="check">
                    {{ $editingSucursal ? 'Actualizar' : 'Crear' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal de Confirmación de Eliminación -->
    <flux:modal wire:model="showDeleteModal">
        <div class="px-6 pt-6 pb-2 border-b">
            <h2 class="text-xl font-bold text-center text-zinc-900">
                Confirmar Eliminación
            </h2>
        </div>
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <flux:icon name="exclamation-triangle" class="w-8 h-8 text-red-600" />
            </div>
            <h3 class="text-lg font-medium text-zinc-900 mb-2">¿Estás seguro?</h3>
            <p class="text-zinc-500 mb-6">
                Esta acción no se puede deshacer. La sucursal será eliminada permanentemente.
            </p>
        </div>
        <div class="px-6 pb-6 pt-2 border-t flex justify-end space-x-3">
            <flux:button wire:click="cancelDelete" variant="outline">
                Cancelar
            </flux:button>
            <flux:button wire:click="deleteSucursal" variant="danger">
                Eliminar Definitivamente
            </flux:button>
        </div>
    </flux:modal>
</div>
