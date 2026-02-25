<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                    <p class="text-sm text-gray-600">{{ $sub_title }}</p>
                </div>
                <flux:button wire:click="exportExcel" variant="primary" icon="arrow-down-tray" size="xs">
                    Exportar Excel
                </flux:button>
            </div>
        </div>

        <!-- Filtros Mejorados -->
        <div class="px-6 pt-6 pb-2">
            <form wire:submit.prevent class="w-full">
                <div class="flex flex-col md:flex-row md:items-end md:space-x-4 gap-4">
                    <div class="flex-1">
                        <flux:input type="search" label="Buscar cliente" wire:model.live="search"
                            placeholder="Código, Nombre, Teléfono..." size="sm" icon="magnifying-glass"
                            class="w-full" />
                    </div>
                    <div>
                        <flux:select wire:model.live="tipoDocumento" label="Tipo Documento" size="sm"
                            class="w-full">
                            @foreach($tiposDocumento as $tipo)
                                <flux:select.option value="{{ $tipo['id'] }}">{{ $tipo['name'] }}
                                </flux:select.option>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dirección</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Encomiendas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $customer->id }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-semibold">{{ $customer->code }}</div>
                                <div class="text-xs text-gray-500">{{ $customer->type_code }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $customer->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $customer->address }}</td>
                            <td class="px-4 py-3 text-sm">{{ $customer->phone }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="text-xs">
                                    <div>Remitente: {{ $customer->encomiendas_remitente_count }}</div>
                                    <div>Destinatario: {{ $customer->encomiendas_destinatario_count }}</div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No se encontraron clientes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $customers->links() }}
        </div>
    </div>
</div>

