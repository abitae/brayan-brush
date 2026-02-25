<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
                    <p class="text-sm text-gray-600 dark:text-zinc-400">{{ $sub_title }}</p>
                </div>
            </div>
        </div>

        <!-- Búsqueda -->
        <div class="p-6">
            <flux:input type="search" wire:model.live="search" placeholder="Buscar manifiesto..." size="sm" />
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto p-6">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Sucursal Origen</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Sucursal Destino</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Cantidad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($manifiestos as $manifiesto)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $manifiesto->id }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $manifiesto->sucursal->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $manifiesto->sucursal->code ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $manifiesto->destino->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $manifiesto->destino->code ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $manifiesto->created_at->format('d-m-Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                @php
                                    $ids = json_decode($manifiesto->ids, true);
                                    $cantidad = is_array($ids) ? count($ids) : 0;
                                @endphp
                                {{ $cantidad }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <flux:button wire:click="excelGenerate({{ $manifiesto->id }})" size="xs" variant="primary" icon="arrow-down-tray">
                                    Excel
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-400">
                                No se encontraron manifiestos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700">
            {{ $manifiestos->links() }}
        </div>
    </div>
</div>

