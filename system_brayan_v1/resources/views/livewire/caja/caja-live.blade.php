<div>
    <x-mary-card title="{{ $title }}" subtitle="{{ $sub_title }}" shadow separator progress-indicator>
        <x-slot:menu>
            <x-mary-button wire:click="openModal" icon="s-eye{{ !$openCaja ? '' : '-slash' }}"
                label="{{ !$openCaja ? 'Abrir' : 'Cerrar' }} Caja"
                class="text-white bg-{{ !$openCaja ? 'green' : 'red' }}-500" responsive />
                
            <x-mary-button @click="$wire.showHistory = true" icon="s-eye" label="Historial"
                class="text-white bg-purple-500" responsive />
        </x-slot:menu>
        @if ($openCaja)
            <div class="flex justify-end mb-4">
                <x-mary-toggle wire:model.live="showCajaStats" label="Mostrar estadísticas" wire:change="$set('showCajaStats', !$showCajaStats)" />
            </div>

            @if($showCajaStats)
            <div class="grid grid-cols-1 border border-gray-100 sm:grid-cols-2 md:grid-cols-4">
                <div>
                    <x-mary-stat title="Monto apertura" description="Apertura"
                        value="{{ number_format($caja->monto_apertura, 2) }}" icon="o-arrow-trending-up"
                        tooltip="Ops!" />
                </div>
                <div>
                    <x-mary-stat title="Ingresos totales" description="Boletas, Facturas y ticket"
                        value="{{ number_format($caja->entries->sum('monto_entry'), 2) }}" icon="o-arrow-trending-up"
                        class="text-green-500" color="text-green-500" tooltip="Total entradas de dinero" />
                </div>
                <div>
                    <x-mary-stat title="Egresos totales" description="Pagos y salidas"
                        value="{{ number_format($caja->exits->sum('monto_exit'), 2) }}" icon="o-arrow-trending-down"
                        class="text-red-500" color="text-red-500" tooltip="Total salidas de dinero" />
                </div>
                <div>
                    <x-mary-stat title="Monto cierre Efectivo" description="Cierre"
                        value="{{ number_format($caja->monto_apertura + $caja->entries->whereIn('metodo_pago', ['Efectivo'])->sum('monto_entry') - $caja->exits->whereIn('metodo_pago', ['Efectivo'])->sum('monto_exit'), 2) }}"
                        icon="o-arrow-trending-down" tooltip="Ops!" />
                </div>
                <div>
                    <x-mary-stat title="Ingresos Efectivo" description="Boletas, Facturas y ticket"
                        value="{{ number_format($caja->entries->whereIn('metodo_pago', ['Efectivo'])->sum('monto_entry'), 2) }}"
                        icon="s-currency-dollar" class="text-green-500" color="text-green-500"
                        tooltip="Total entradas de dinero" />
                </div>
                <div>
                    <x-mary-stat title="Ingresos Yape" description="Boletas, Facturas y ticket"
                        value="{{ number_format($caja->entries->whereNotIn('metodo_pago', ['Efectivo'])->sum('monto_entry'), 2) }}"
                        icon="s-currency-dollar" class="text-green-500" color="text-green-500"
                        tooltip="Total entradas de dinero" />
                </div>
                <div>
                    <x-mary-stat title="Egreso efectivo" description="Pagos y salidas"
                        value="{{ number_format($caja->exits->whereIn('metodo_pago', ['Efectivo'])->sum('monto_exit'), 2) }}"
                        icon="s-currency-dollar" class="text-red-500" color="text-red-500"
                        tooltip="Total salidas de dinero" />
                </div>
                <div>
                    <x-mary-stat title="Egresos Yape, Transferencia y Deposito" description="Pagos y salidas"
                        value="{{ number_format($caja->exits->whereNotIn('metodo_pago', ['Efectivo'])->sum('monto_exit'), 2) }}"
                        icon="s-currency-dollar" class="text-red-500" color="text-red-500"
                        tooltip="Total salidas de dinero" />
                </div>
            </div>
            @endif
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2">
                <div>
                    <x-mary-card title="Ingresos" subtitle="Registro de ingresos a caja" shadow separator>
                        <x-slot:menu>
                            <x-mary-button @click="$wire.modalEntry = true" responsive icon="o-plus" label="Ingreso"
                                class="text-white bg-green-500" />
                        </x-slot:menu>
                        <x-mary-table :headers="$headersIngreso" :rows="$caja->entries" striped>
                        </x-mary-table>
                    </x-mary-card>
                </div>
                <div>
                    <x-mary-card title="Egresos" subtitle="Registro de egresos de caja" shadow separator>
                        <x-slot:menu>
                            <x-mary-button @click="$wire.modalExit = true" responsive icon="c-minus" label="Egreso"
                                class="text-white bg-red-500" />
                        </x-slot:menu>
                        <x-mary-table :headers="$headersEgreso" :rows="$caja->exits" striped>
                        </x-mary-table>
                    </x-mary-card>
                </div>
            </div>
        @endif
    </x-mary-card>

    @include('livewire.caja.caja-modal')

    @include('livewire.caja.caja-drawer')
</div>
