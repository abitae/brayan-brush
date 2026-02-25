<div class="p-4 w-full max-w-6xl mx-auto space-y-4">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $title }} - {{ $tipoDoc == '03' ? 'BOLETA' : 'FACTURA' }}</h1>
                    <p class="text-sm text-gray-600">{{ $sub_title }}</p>
                </div>
                <div class="text-green-500 text-4xl font-bold">{{ $tipoDoc == '03' ? 'BOLETA' : 'FACTURA' }}</div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Configuración del documento -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 border border-green-500 rounded-lg">
                <flux:select wire:model.live="tipoDoc" label="Tipo Doc." size="sm">
                    @foreach($tipoDocs as $tipo)
                        <flux:select.option value="{{ $tipo['codigo'] }}">{{ $tipo['descripcion'] }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="tipoOperacion" label="Tipo Oper." size="sm">
                    @foreach($tipoOperaciones as $operacion)
                        <flux:select.option value="{{ $operacion['codigo'] }}">{{ $operacion['descripcion'] }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="moneda" label="Moneda" size="sm">
                    @foreach($monedas as $monedaItem)
                        <flux:select.option value="{{ $monedaItem['codigo'] }}">{{ $monedaItem['descripcion'] }}</flux:select.option>
                    @endforeach
                </flux:select>
                @if($tipoOperacion == '1001')
                    <flux:select wire:model.live="tipoDetraccion" label="Tipo Detracción" size="sm">
                        @foreach($tipoDetracciones as $detraccion)
                            <flux:select.option value="{{ $detraccion->codigo }}">{{ $detraccion->descripcion }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @endif
                <flux:input wire:model.live="docAdjunto" label="Guía Adjunta" size="sm" />
            </div>

            <!-- Datos del cliente -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-green-500 rounded-lg">
                <div class="flex gap-2">
                    <flux:select wire:model.live="tipoDocumento" label="Tipo Doc.Ident." size="sm" class="w-32">
                        @foreach($tipoDocuments as $tipo)
                            <flux:select.option value="{{ $tipo['codigo'] }}">{{ $tipo['sigla'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:input.group class="flex-1">
                        <flux:input wire:model.live="numDocumento" label="Documento" size="sm" />
                        <flux:button wire:click="buscarDocumento" icon="magnifying-glass" size="sm" />
                    </flux:input.group>
                </div>
                <flux:input wire:model.live="razonSocial" label="Razón Social" size="sm" />
                <flux:input wire:model.live="direccion" label="Dirección" size="sm" />
                <flux:select wire:model.live="ubigeo" label="Ubigeo" size="sm">
                    <flux:select.option value=""></flux:select.option>
                    @foreach($ubigeos as $ubigeoItem)
                        <flux:select.option value="{{ $ubigeoItem->ubigeo2 }}">{{ $ubigeoItem->texto_ubigeo }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input wire:model.live="telefono" label="Teléfono" size="sm" />
            </div>

            <!-- Agregar paquetes -->
            <div class="p-4 border border-green-500 rounded-lg">
                <div class="grid grid-cols-8 gap-2 mb-4">
                    <flux:input wire:model="cantidad" label="CANT." size="xs" type="number" />
                    <flux:select wire:model="und_medida" label="MEDIDA" size="xs">
                        @foreach($unidadMedidas as $unidad)
                            <flux:select.option value="{{ $unidad->codigo }}">{{ $unidad->descripcion }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="description" label="DESCRIPCIÓN" size="xs" class="col-span-3" />
                    <flux:input wire:model="peso" label="PESO (KG)" size="xs" type="number" step="0.01" />
                    <flux:input wire:model="amount" label="MONTO" size="xs" type="number" step="0.01" />
                    <div class="flex gap-1 items-end">
                        <flux:button wire:click="addPaquete" icon="plus" size="xs" variant="primary" />
                        <flux:button wire:click="resetPaquete" icon="x-mark" size="xs" variant="danger" />
                    </div>
                </div>

                <!-- Tabla de paquetes -->
                @if($paquetes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left">Cant.</th>
                                    <th class="px-3 py-2 text-left">Unidad</th>
                                    <th class="px-3 py-2 text-left">Descripción</th>
                                    <th class="px-3 py-2 text-right">Peso</th>
                                    <th class="px-3 py-2 text-right">Monto</th>
                                    <th class="px-3 py-2 text-right">Subtotal</th>
                                    <th class="px-3 py-2 text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paquetes as $index => $paquete)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2">{{ $paquete['cantidad'] ?? 0 }}</td>
                                        <td class="px-3 py-2">{{ $paquete['und_medida'] ?? '' }}</td>
                                        <td class="px-3 py-2">{{ $paquete['description'] ?? '' }}</td>
                                        <td class="px-3 py-2 text-right">{{ number_format($paquete['peso'] ?? 0, 2) }}</td>
                                        <td class="px-3 py-2 text-right">S/ {{ number_format($paquete['amount'] ?? 0, 2) }}</td>
                                        <td class="px-3 py-2 text-right font-semibold">S/ {{ number_format($paquete['sub_total'] ?? 0, 2) }}</td>
                                        <td class="px-3 py-2 text-center">
                                            <flux:button wire:click="restPaquete({{ $index + 1 }})" icon="trash" size="xs" variant="danger" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Totales -->
            <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <label class="text-sm text-gray-600">Subtotal</label>
                    <div class="text-lg font-semibold">S/ {{ number_format($sub_total, 2) }}</div>
                </div>
                <div>
                    <label class="text-sm text-gray-600">IGV</label>
                    <div class="text-lg font-semibold">S/ {{ number_format($igv, 2) }}</div>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Total</label>
                    <div class="text-2xl font-bold text-green-600">S/ {{ number_format($total, 2) }}</div>
                </div>
            </div>

            <!-- Botón emitir -->
            <div class="flex justify-end">
                <flux:button wire:click="emitFactura" variant="primary" size="sm" icon="check-circle">
                    Emitir {{ $tipoDoc == '03' ? 'Boleta' : 'Factura' }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Modal de impresión -->
    @if($modalPrintInvoice)
        <flux:modal wire:model="modalPrintInvoice" name="printInvoice">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Factura Emitida</h3>
                <p class="mb-4">La factura se ha emitido correctamente.</p>
                <div class="flex gap-2 justify-end">
                    <flux:button href="{{ route('pdf.invoice.a4', $invoice) }}" target="_blank" variant="primary">Ver PDF A4</flux:button>
                    <flux:button href="{{ route('pdf.invoice.80mm', $invoice) }}" target="_blank" variant="primary">Ver PDF 80mm</flux:button>
                    <flux:button wire:click="closePrintInvoice" variant="outline">Cerrar</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>

