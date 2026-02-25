<div class="p-8 w-full">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-md border border-zinc-100 overflow-hidden mb-8">
        <div class="px-8 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1 flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <x-flux::icon name="layout-grid" class="w-6 h-6 text-blue-600" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900">Tipos de Caja</h1>
                    <p class="text-zinc-500 text-sm">Administra los tipos de entrada y salida de caja</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes -->
    @if ($message)
        <div class="mb-6">
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3">
                <div class="flex-shrink-0">
                    <x-flux::icon name="check-circle" class="w-5 h-5 text-green-600" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-800">{{ $message }}</p>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" class="text-green-400 hover:text-green-600"
                        onclick="this.parentElement.parentElement.parentElement.remove()">
                        <x-flux::icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Tipos de Entrada -->
        <div class="bg-white rounded-xl shadow-md border border-zinc-100 overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b bg-green-50">
                <h3 class="text-xl font-semibold text-zinc-900">Tipos de Entrada</h3>
                <flux:button wire:click="openEntryModal()" icon="plus" variant="primary" size="sm">
                    Nuevo
                </flux:button>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full text-sm text-zinc-700">
                    <thead>
                        <tr class="bg-zinc-100">
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Estado</th>
                            <th class="px-4 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tiposEntry as $entry)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $entry->name }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $entry->is_active ? 'bg-green-100 text-green-700' : 'bg-zinc-200 text-zinc-600' }}">
                                        {{ $entry->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right flex gap-2 justify-end">
                                    <flux:button wire:click="openEntryModal({{ $entry->id }})" icon="pencil" size="xs" variant="outline"></flux:button>
                                    <flux:button wire:click="toggleEntryActive({{ $entry->id }})" icon="check-circle" size="xs" variant="{{ $entry->is_active ? 'danger' : 'primary' }}">
                                    </flux:button>
                                    <flux:button wire:click="deleteEntry({{ $entry->id }})" icon="trash" size="xs" variant="danger"></flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-zinc-400 py-8">
                                    <x-flux::icon name="inbox" class="w-12 h-12 mx-auto mb-2 text-zinc-200" />
                                    <p>No hay tipos de entrada registrados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tipos de Salida -->
        <div class="bg-white rounded-xl shadow-md border border-zinc-100 overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b bg-red-50">
                <h3 class="text-xl font-semibold text-zinc-900">Tipos de Salida</h3>
                <flux:button wire:click="openExitModal()" icon="plus" variant="danger" size="sm">
                    Nuevo
                </flux:button>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full text-sm text-zinc-700">
                    <thead>
                        <tr class="bg-zinc-100">
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Estado</th>
                            <th class="px-4 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tiposExit as $exit)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $exit->name }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $exit->is_active ? 'bg-green-100 text-green-700' : 'bg-zinc-200 text-zinc-600' }}">
                                        {{ $exit->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right flex gap-2 justify-end">
                                    <flux:button wire:click="openExitModal({{ $exit->id }})" icon="pencil" size="xs" variant="outline"></flux:button>
                                    <flux:button wire:click="toggleExitActive({{ $exit->id }})" icon="check-circle" size="xs" variant="{{ $exit->is_active ? 'danger' : 'primary' }}">

                                    </flux:button>
                                    <flux:button wire:click="deleteExit({{ $exit->id }})" icon="trash" size="xs" variant="danger"></flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-zinc-400 py-8">
                                    <x-flux::icon name="inbox" class="w-12 h-12 mx-auto mb-2 text-zinc-200" />
                                    <p>No hay tipos de salida registrados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tipo Entrada -->
    <flux:modal wire:model="showEntryModal">
        <div class="px-6 pt-6 pb-2 border-b">
            <h2 class="text-xl font-bold text-center text-zinc-900">
                {{ $isEditingEntry ? 'Editar Tipo de Entrada' : 'Nuevo Tipo de Entrada' }}
            </h2>
        </div>
        <div class="p-8 space-y-6">
            <flux:input wire:model="entry_name" label="Nombre" type="text" required placeholder="Nombre del tipo de entrada" />
            <div class="flex items-center gap-2">
                <flux:switch wire:model="entry_is_active" id="entry_is_active" label="Activo" />
            </div>
        </div>
        <div class="px-6 pb-6 pt-2 border-t flex justify-end space-x-3">
            <flux:button wire:click="closeEntryModal" variant="outline">
                Cancelar
            </flux:button>
            <flux:button wire:click="saveEntry" variant="primary">
                {{ $isEditingEntry ? 'Actualizar' : 'Crear' }}
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal Tipo Salida -->
    <flux:modal wire:model="showExitModal">
        <div class="px-6 pt-6 pb-2 border-b">
            <h2 class="text-xl font-bold text-center text-zinc-900">
                {{ $isEditingExit ? 'Editar Tipo de Salida' : 'Nuevo Tipo de Salida' }}
            </h2>
        </div>
        <div class="p-8 space-y-6">
            <flux:input wire:model="exit_name" label="Nombre" type="text" required placeholder="Nombre del tipo de salida" />
            <div class="flex items-center gap-2">
                <flux:switch wire:model="exit_is_active" id="exit_is_active" label="Activo" />
            </div>
        </div>
        <div class="px-6 pb-6 pt-2 border-t flex justify-end space-x-3">
            <flux:button wire:click="closeExitModal" variant="outline">
                Cancelar
            </flux:button>
            <flux:button wire:click="saveExit" variant="danger">
                {{ $isEditingExit ? 'Actualizar' : 'Crear' }}
            </flux:button>
        </div>
    </flux:modal>
</div>
