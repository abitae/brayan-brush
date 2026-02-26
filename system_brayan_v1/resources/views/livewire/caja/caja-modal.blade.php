<x-mary-modal wire:model="modalCaja" persistent class="backdrop-blur" box-class="max-h-full max-w-128">
    <x-mary-icon name="s-envelope" class="text-{{ !$openCaja ? 'green' : 'red' }}-500 text-md"
        label="{{ !$openCaja ? 'ABRIR' : 'CERRAR' }} CAJA" />
    <x-mary-form wire:submit.prevent="save">
        <div class="border border-{{ !$openCaja ? 'green' : 'red' }}-500 rounded-lg">
            <div class="grid grid-cols-4 p-2 space-x-2">
                <div class="grid col-span-4 pt-2">
                    <x-mary-input label="Monto {{ !$openCaja ? 'apertura' : 'cierre' }}"
                        wire:model.live="cajaForm.monto_{{ !$openCaja ? 'apertura' : 'cierre' }}" suffix="PEN" />
                </div>
            </div>
            <x-slot:actions>
                <x-mary-button label="Cancelar" @click="$wire.modalCaja = false" class="bg-red-500" />
                <x-mary-button type="submit" spinner="save" label="Guardar" class="bg-blue-500" />
            </x-slot:actions>
        </div>
    </x-mary-form>
</x-mary-modal>

<x-mary-modal wire:model="modalEntry" persistent class="backdrop-blur" box-class="max-h-full max-w-128">
    <x-mary-icon name="s-envelope" class="text-green-500 text-md" label="REGISTRO INGRESO" />
    <x-mary-form wire:submit="entryCaja">
        <div class="border border-green-500 rounded-lg">
            <div class="grid grid-cols-1 p-2 space-x-2">
                <div class="pt-2">
                    <x-mary-select label="Tipo" :options="$tipos" wire:model="entryForm.tipo_entry" />
                </div>
                <div class="pt-2">
                    <x-mary-input label="Monto" wire:model="entryForm.monto_entry" suffix="S/" locale="es-PE"
                        first-error-only />
                </div>
                <div class="pt-2">
                    <x-mary-input label="Descripción" wire:model="entryForm.description" first-error-only />
                </div>
                <div>
                    @php
                    $metodoPagos = [
                    ['id' => 'Efectivo', 'name' => 'Efectivo'],
                    ['id' => 'Yape', 'name' => 'Yape'],
                    ['id' => 'Transferencia', 'name' => 'Transferencia'],
                    ['id' => 'Deposito', 'name' => 'Deposito'],
                    ];
                    @endphp
                    <x-mary-select label="Metodo pago" icon="o-user" :options="$metodoPagos"
                    wire:model="entryForm.metodo_pago" class="rounded-r-lg" />
                </div>
            </div>
            <x-slot:actions>
                <x-mary-button label="Cancelar" @click="$wire.modalEntry = false" class="bg-red-500" />
                <x-mary-button type="submit" spinner="save3" label="Guardar" class="bg-blue-500" spinner="entryCaja" />
            </x-slot:actions>
        </div>
    </x-mary-form>
</x-mary-modal>

<x-mary-modal wire:model="modalExit" persistent class="backdrop-blur" box-class="max-h-full max-w-128">
    <x-mary-icon name="s-envelope" class="text-red-500 text-md" label="REGISTRO EGRESO" />
    <x-mary-form wire:submit="exitCaja">
        <div class="border border-red-500 rounded-lg">
            <div class="grid grid-cols-1 p-2 space-x-2">
                <div class="pt-2">
                    <x-mary-select label="Tipo" :options="$tipos2" wire:model="exitForm.tipo_exit" />
                </div>
                <div class="pt-2">
                    <x-mary-input label="Monto" wire:model="exitForm.monto_exit" suffix="S/" locale="es-PE" />
                </div>
                <div class="pt-2">
                    <x-mary-input label="Descripción" wire:model="exitForm.description" />
                </div>
                <div>
                    <x-mary-select label="Metodo pago" icon="o-user" :options="$metodoPagos"
                    wire:model="exitForm.metodo_pago" class="rounded-r-lg" />
                </div>
            </div>
            <x-slot:actions>
                <x-mary-button label="Cancelar" @click="$wire.modalExit = false" class="bg-red-500" />
                <x-mary-button type="submit" spinner="save3" label="Guardar" class="bg-blue-500" spinner="exitCaja" />
            </x-slot:actions>
        </div>
    </x-mary-form>
</x-mary-modal>
