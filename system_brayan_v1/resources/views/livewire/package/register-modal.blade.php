@if ($this->destinatario && $this->remitente && $this->cliFacturacion && $this->paquetes)
    <x-mary-modal wire:model="modalConfimation" class="backdrop-blur" box-class="max-w-6xl max-h-full bg-gray-200" separator
        progress-indicator>
        <div class="p-2 space-y-4 ">
            <!-- Información de clientes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 ">
                <!-- Remitente -->
                <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-green-500">
                    <div class="flex items-center mb-2">
                        <x-mary-icon name="s-user" class="text-green-500 mr-2" />
                        <h3 class="font-bold text-green-700">REMITENTE</h3>
                    </div>
                    <div class="space-y-1 text-sm">
                        <p class="font-medium">{{ $this->remitente->name ?? 'name' }}</p>
                        <p>{{ $this->remitente->type_code == 1 ? 'DNI:' : 'RUC:' }} {{ $this->remitente->code ?? 'code' }}</p>
                        @if ($this->remitente->phone)
                            <p class="flex items-center"><x-mary-icon name="s-phone" class="text-gray-500 mr-1 h-4 w-4" /> {{ $this->remitente->phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- Destinatario -->
                <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-blue-500">
                    <div class="flex items-center mb-2">
                        <x-mary-icon name="s-user" class="text-blue-500 mr-2" />
                        <h3 class="font-bold text-blue-700">DESTINATARIO</h3>
                    </div>
                    <div class="space-y-1 text-sm">
                        <p class="font-medium">{{ $this->destinatario->name ?? 'name' }}</p>
                        <p>{{ $this->destinatario->type_code == 1 ? 'DNI:' : 'RUC:' }} {{ $this->destinatario->code ?? 'code' }}</p>
                        @if ($this->destinatario->phone)
                            <p class="flex items-center"><x-mary-icon name="s-phone" class="text-gray-500 mr-1 h-4 w-4" /> {{ $this->destinatario->phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- Facturación -->
                <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-purple-500">
                    <div class="flex items-center mb-2">
                        <x-mary-icon name="s-document-text" class="text-purple-500 mr-2" />
                        <h3 class="font-bold text-purple-700">FACTURACIÓN</h3>
                    </div>
                    <div class="space-y-1 text-sm">
                        <p class="font-medium">{{ $this->cliFacturacion->name ?? 'name' }}</p>
                        <p>{{ $this->cliFacturacion->type_code == 1 ? 'DNI:' : 'RUC:' }} {{ $this->cliFacturacion->code ?? 'code' }}</p>
                        @if ($this->cliFacturacion->phone)
                            <p class="flex items-center"><x-mary-icon name="s-phone" class="text-gray-500 mr-1 h-4 w-4" /> {{ $this->cliFacturacion->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detalles de pago -->
            <div class="bg-white rounded-lg shadow-sm p-3">
                <div class="flex items-center mb-3">
                    <x-mary-icon name="s-credit-card" class="text-indigo-500 mr-2" />
                    <h3 class="font-bold text-indigo-700">DETALLE PAGO</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-gray-50 rounded p-2">
                        <x-mary-stat title="Estado pago" value="{{ $this->estado_pago }}" icon="s-banknotes"
                            tooltip="Pagado o Contra entrega" />
                    </div>
                    <div class="bg-gray-50 rounded p-2">
                        <x-mary-stat title="Tipo comprobante" value="{{ $this->tipo_comprobante }}" icon="s-document"
                            tooltip="Ticket, Boleta, Factura" />
                    </div>
                    <div class="bg-gray-50 rounded p-2">
                        <x-mary-stat title="Método de pago" value="{{ strtoupper($this->metodo_pago) }}" icon="s-currency-dollar"
                            tooltip="Método de pago (Contado, Yape, Transferencia, Depósito)" />
                    </div>
                    <div class="bg-gray-50 rounded p-2">
                        @if ($isHome)
                            @if ($isReturn)
                                <x-mary-stat title="Entrega" value="RETORNO" icon="s-arrow-path" tooltip="Retorno de encomienda" />
                            @else
                                <x-mary-stat title="Entrega" value="DOMICILIO" icon="s-home" tooltip="Reparto a domicilio" />
                            @endif
                        @else
                            <x-mary-stat title="Entrega" value="AGENCIA" icon="o-building-storefront" tooltip="Reparto a agencia" />
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detalle de paquetes -->
            <div class="bg-white rounded-lg shadow-sm p-3">
                <div class="flex items-center mb-3">
                    <x-mary-icon name="s-cube" class="text-amber-500 mr-2" />
                    <h3 class="font-bold text-amber-700">DETALLE PAQUETES</h3>
                </div>
                <div class="overflow-x-auto">
                    <x-mary-table :headers="$headers_paquetes" :rows="$paquetes" striped hover>
                    </x-mary-table>
                </div>
                <div class="text-right font-bold text-lg mt-2 text-indigo-600 border-t pt-2">
                    Total: S/{{ number_format($paquetes->sum('sub_total'), 2) }}
                </div>
            </div>
        </div>
        <x-slot:actions>
            <div class="flex space-x-2">
                <x-mary-button label="Cancelar" @click="$wire.modalConfimation = false" class="bg-gray-200 hover:bg-gray-300 text-gray-700" />
                <x-mary-button wire:click='confirmEncomienda' label="Confirmar" class="bg-green-500 hover:bg-green-700 text-white" spinner />
            </div>
        </x-slot:actions>
    </x-mary-modal>
@endif
