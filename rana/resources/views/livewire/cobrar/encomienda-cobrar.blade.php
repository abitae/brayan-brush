<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="text-sm text-gray-600">{{ $sub_title }}</p>
        </div>

        <!-- Filtros -->
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <flux:input type="search" wire:model.live="search" placeholder="Buscar..." size="sm" />
                <flux:input type="datetime-local" wire:model.live="filtroFechaInicio" label="Desde" size="sm" />
                <flux:input type="datetime-local" wire:model.live="filtroFechaFin" label="Hasta" size="sm" />
                <flux:select wire:model.live="FiltroEstadoEncomienda" label="Estado" size="sm">
                    <flux:select.option value="">Todos</flux:select.option>
                    <flux:select.option value="REGISTRADO">REGISTRADO</flux:select.option>
                    <flux:select.option value="ENVIADO">ENVIADO</flux:select.option>
                    <flux:select.option value="RECIBIDO">RECIBIDO</flux:select.option>
                    <flux:select.option value="ENTREGADO">ENTREGADO</flux:select.option>
                </flux:select>
            </div>
            <div class="flex gap-2">
                <flux:button wire:click="resetFilters" variant="outline" size="sm">Limpiar Filtros</flux:button>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($encomiendas as $encomienda)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $encomienda->id }}</td>
                            <td class="px-4 py-3 text-sm font-semibold">{{ $encomienda->code }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="text-xs text-gray-500">{{ $encomienda->remitente->code }}</div>
                                <div class="font-medium">{{ $encomienda->remitente->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold">S/ {{ number_format($encomienda->monto, 2) }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $encomienda->estado_credito }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <flux:button wire:click="openCobrarModal({{ $encomienda->id }})" size="xs" variant="primary" icon="banknotes">
                                    Cobrar
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No se encontraron encomiendas pendientes de cobro
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $encomiendas->links() }}
        </div>
    </div>

    <!-- Modal de cobro -->
    @if($modalCobrar)
        <flux:modal wire:model="modalCobrar" name="modalCobrar">
            <div class="p-6 space-y-4">
                <h3 class="text-lg font-semibold">Cobrar Encomienda</h3>
                
                <div class="space-y-2">
                    <p><strong>Código:</strong> {{ $encomienda->code }}</p>
                    <p><strong>Cliente:</strong> {{ $cliFacturacion_name }}</p>
                    <p><strong>Monto:</strong> S/ {{ number_format($encomienda->monto, 2) }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="monto_descuento" label="Descuento" type="number" step="0.01" size="sm" />
                    <flux:input wire:model="motivo_descuento" label="Motivo Descuento" size="sm" />
                    <flux:select wire:model="tipo_pago" label="Tipo Pago" size="sm">
                        <flux:select.option value="Contado">Contado</flux:select.option>
                        <flux:select.option value="Credito">Crédito</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="tipo_comprobante" label="Tipo Comprobante" size="sm">
                        <flux:select.option value="TICKET">TICKET</flux:select.option>
                        <flux:select.option value="BOLETA">BOLETA</flux:select.option>
                        <flux:select.option value="FACTURA">FACTURA</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="metodo_pago" label="Método Pago" size="sm">
                        <flux:select.option value="Efectivo">Efectivo</flux:select.option>
                        <flux:select.option value="Yape">Yape</flux:select.option>
                        <flux:select.option value="Tarjeta">Tarjeta</flux:select.option>
                        <flux:select.option value="Transferencia">Transferencia</flux:select.option>
                    </flux:select>
                </div>

                <div class="flex gap-2 justify-end">
                    <flux:button wire:click="$set('modalCobrar', false)" variant="outline">Cancelar</flux:button>
                    <flux:button wire:click="cobrarEncomienda" variant="primary">Confirmar Cobro</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>

