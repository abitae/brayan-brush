<div class="p-4 w-full max-w-6xl mx-auto space-y-4">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="text-sm text-gray-600">{{ $sub_title }}</p>
        </div>

        <div class="p-6 space-y-6">
            <!-- Selección de documento afectado -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-blue-500 rounded-lg">
                <flux:select wire:model.live="tipoDocAfectado" label="Tipo Doc. Afectado" size="sm">
                    @foreach($tipoDocs as $tipo)
                        <flux:select.option value="{{ $tipo['codigo'] }}">{{ $tipo['descripcion'] }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="numDocfectado" label="Documento Afectado" size="sm">
                    <flux:select.option value="">Seleccione un documento</flux:select.option>
                    @foreach($invoices as $invoice)
                        <flux:select.option value="{{ $invoice->id }}">{{ $invoice->serie }}-{{ $invoice->correlativo }} - {{ $invoice->client->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="motivo" label="Motivo" size="sm">
                    @foreach($motivos as $motivoItem)
                        <flux:select.option value="{{ $motivoItem->codigo }}">{{ $motivoItem->descripcion }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Datos del cliente (similar a invoice-create) -->
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

            <!-- Tabla de paquetes (similar a invoice-create) -->
            @if($paquetes->count() > 0)
                <div class="p-4 border border-green-500 rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left">Cant.</th>
                                    <th class="px-3 py-2 text-left">Unidad</th>
                                    <th class="px-3 py-2 text-left">Descripción</th>
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
                </div>
            @endif

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
                <flux:button wire:click="emitNote" variant="primary" size="lg" icon="check-circle">
                    Emitir Nota de Crédito
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Modal de impresión -->
    @if($modalPrintNote)
        <flux:modal wire:model="modalPrintNote" name="printNote">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Nota de Crédito Emitida</h3>
                <p class="mb-4">La nota de crédito se ha emitido correctamente.</p>
                <div class="flex gap-2 justify-end">
                    <flux:button href="{{ route('pdf.note.a4', $note) }}" target="_blank" variant="primary">Ver PDF A4</flux:button>
                    <flux:button href="{{ route('pdf.note.80mm', $note) }}" target="_blank" variant="primary">Ver PDF 80mm</flux:button>
                    <flux:button wire:click="closePrintNote" variant="outline">Cerrar</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>

