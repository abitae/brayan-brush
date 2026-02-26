<div>
    <x-mary-card title="{{ $title ?? 'title' }}" subtitle="{{ $sub_title ?? 'title' }}" shadow separator
        progress-indicator>
        <x-mary-steps wire:model="step" steps-color="step-warning"
            class="p-2 my-5 border rounded-lg shadow-xl border-sky-500">
            <x-mary-step step="1" text="Remitente">
                <div class="grid grid-cols-4 gap-3 p-4 bg-white rounded-lg shadow-sm">
                    <div class="col-span-4 md:col-span-2">
                        <x-mary-input label="Número de documento" wire:model='remitente_code' wire:keydown.enter="searchRemitente" wire:keydown.ctrl.enter="next"
                            placeholder="Ingrese documento">
                            <x-slot:prepend>
                                <x-mary-select wire:model='remitente_type_code' icon="o-user" :options="$tipoDocuments"
                                    option-value="codigo" option-label="sigla" class="rounded-e-none" />
                            </x-slot:prepend>
                            <x-slot:append>
                                <x-mary-button wire:click='searchRemitente' icon="o-magnifying-glass"
                                    class="btn-primary rounded-s-none hover:bg-blue-600" tooltip="Buscar remitente" />
                            </x-slot:append>
                        </x-mary-input>
                    </div>
                    <div class="col-span-4 md:col-span-2">
                        <x-mary-input label="Nombre/Razón Social" wire:model='remitente_name'
                            placeholder="Nombre completo" />
                    </div>
                    <div class="col-span-4 md:col-span-3">
                        <x-mary-input label="Dirección" wire:model='remitente_address' placeholder="Dirección completa"
                            icon="o-home" />
                    </div>
                    <div class="col-span-4 md:col-span-1">
                        <x-mary-input label="Celular" wire:model='remitente_phone' placeholder="999999999"
                            icon="o-device-phone-mobile" />
                    </div>
                </div>
            </x-mary-step>
            <x-mary-step step="2" text="Destinatario">
                <div class="grid grid-cols-4 gap-3 p-4 bg-white rounded-lg shadow-sm">
                    <div class="col-span-4 md:col-span-2">
                        <x-mary-input label="Número de documento" wire:model='destinatario_code' wire:keydown.enter="searchDestinatario"  wire:keydown.ctrl.enter="next"
                            placeholder="Ingrese documento">
                            <x-slot:prepend>
                                <x-mary-select wire:model='destinatario_type_code' icon="o-user" option-value="codigo"
                                    option-label="sigla" :options="$tipoDocuments" class="rounded-e-none" />
                            </x-slot:prepend>
                            <x-slot:append>
                                <x-mary-button wire:click.prevent='searchDestinatario' icon="o-magnifying-glass"
                                    class="btn-primary rounded-s-none hover:bg-blue-600"
                                    tooltip="Buscar destinatario" />
                            </x-slot:append>
                        </x-mary-input>
                    </div>
                    <div class="col-span-4 md:col-span-2">
                        <x-mary-input label="Nombre/Razón Social" wire:model='destinatario_name'
                            placeholder="Nombre completo" />
                    </div>
                    <div class="col-span-4 md:col-span-2">
                        <div class="p-2 my-2 border rounded-lg border-sky-200 bg-sky-50">
                            <x-mary-toggle label="Reparto a domicilio" wire:model.live="isHome"
                                hint="Active para reparto a domicilio" />
                        </div>
                    </div>
                    @if ($isHome)
                        <div class="col-span-4 md:col-span-3">
                            <x-mary-input label="Dirección" wire:model='destinatario_address'
                                placeholder="Dirección completa" icon="o-home" />
                        </div>
                        <div class="col-span-4 md:col-span-1">
                            <x-mary-input label="Celular" wire:model='destinatario_phone' placeholder="999999999"
                                icon="o-device-phone-mobile" />
                        </div>
                    @endif
                </div>
            </x-mary-step>
            <x-mary-step step="3" text="Paquetes">
                <div class="grid grid-cols-1 gap-3 p-2 bg-white rounded-lg shadow-sm md:grid-cols-4">
                    <div class="col-span-1 md:col-span-1">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <x-mary-input label="CANTIDAD" wire:model="cantidad" class="text-xs font-medium"
                                    placeholder="0" />
                            </div>
                            <div>
                                <x-mary-select label="UNIDAD" :options="$unidadMedidas" wire:model="und_medida"
                                    option-value="codigo" option-label="descripcion" placeholder="Seleccione" />
                            </div>
                        </div>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <x-mary-input label="DESCRIPCIÓN" wire:model="description" placeholder="Descripción del paquete"
                            icon="o-clipboard-document-list" />
                    </div>
                    <div class="col-span-1 md:col-span-1">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <x-mary-input label="PESO" wire:model="peso" suffix="KG" locale="es-PE"
                                    placeholder="0.00" />
                            </div>
                            <div>
                                <div class="flex flex-col">
                                    <x-mary-input label="MONTO" wire:model="amount" suffix="S/" wire:keydown.enter="addPaquete" wire:keydown.ctrl.enter="addPaquete"
                                        placeholder="0.00" />
                                    <div class="flex justify-end gap-2 mt-2">
                                        <x-mary-button icon="o-plus" wire:click='addPaquete'
                                            class="text-white rounded-lg bg-sky-500 hover:bg-sky-600"
                                            tooltip="Agregar paquete" />
                                        <x-mary-button icon="o-trash" wire:click='resetPaquete'
                                            class="text-white bg-red-500 hover:bg-red-600 rounded-lg"
                                            tooltip="Limpiar campos" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-0">
                    <x-mary-card shadow separator>
                        <x-mary-table :headers="$headers_paquetes" :rows="$paquetes" striped hover
                            @row-click="$wire.restPaquete($event.detail.id)">
                            <x-slot:empty>
                                <div class="flex flex-col items-center justify-center py-6 space-y-2 text-gray-500">
                                    <x-mary-icon name="o-cube" class="w-12 h-12" />
                                    <p>No hay paquetes registrados</p>
                                    <p class="text-sm">Agregue paquetes utilizando el formulario superior</p>
                                </div>
                            </x-slot:empty>
                        </x-mary-table>
                    </x-mary-card>
                </div>
            </x-mary-step>
            <x-mary-step step="4" text="Facturacion">
                <div class="grid grid-cols-1 gap-2 p-4 bg-white rounded-lg shadow-sm md:grid-cols-3">
                    <div>
                        <x-mary-select label="Tipo de pago" icon="o-credit-card" :options="$pagos"
                            wire:model.live="estado_pago" class="w-full" />
                    </div>
                    @if ($estado_pago == 'PAGADO')
                        <div>
                            <x-mary-select label="Tipo de comprobante" icon="o-document-text" :options="$comprobantes"
                                wire:model.live="tipo_comprobante" class="w-full" />
                        </div>
                        <div>
                            <x-mary-select label="Método de pago" icon="o-banknotes" :options="$metodoPagos"
                                wire:model="metodo_pago" class="w-full" />
                        </div>
                    @endif
                </div>

                @if ($tipo_comprobante != 'TICKET' && $estado_pago == 'PAGADO')
                    <div class="mt-4 p-4 bg-white rounded-lg shadow-sm">
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                            <div>
                                <x-mary-input label="Número de documento" wire:model='cliFacturacion_code' wire:keydown.enter="searchFacturacion"
                                    class="w-full">
                                    <x-slot:prepend>
                                        <x-mary-select wire:model.live='cliFacturacion_type_code'
                                            icon="o-identification" option-value="codigo" option-label="sigla"
                                            :options="$tipoDocuments" class="rounded-e-none" />
                                    </x-slot:prepend>
                                    <x-slot:append>
                                        <x-mary-button wire:click='searchFacturacion' icon="o-magnifying-glass"
                                            class="btn-primary rounded-s-none" />
                                    </x-slot:append>
                                </x-mary-input>
                            </div>
                            <div>
                                <x-mary-input label="Nombre/Razón Social" wire:model='cliFacturacion_name'
                                    icon="o-user" class="w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                            <div class="md:col-span-2">
                                <x-mary-input label="Dirección" wire:model='cliFacturacion_address' icon="o-map-pin"
                                    class="w-full" />
                            </div>
                            <div class="md:col-span-1">
                                <x-mary-input label="Celular" wire:model='cliFacturacion_phone'
                                    icon="o-device-phone-mobile" class="w-full" />
                            </div>
                        </div>
                    </div>
                @endif
            </x-mary-step>
            <x-mary-step step="5" text="Destino" data-content="✓" step-classes="!step-success">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <!-- Sección de Sucursal y Configuración -->
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <div class="space-y-4">
                            <x-mary-select label="Sucursal Destino" icon="o-home-modern" :options="$sucursales"
                                wire:model.live="sucursal_dest_id" class="w-full" />
                            @if (!$isHome)
                                <div class="grid grid-cols-2 gap-4 mt-3">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <x-mary-icon name="o-hashtag" label="PING" class="mb-2" />
                                        <x-mary-pin ida="pin1" wire:model="pin1" size="3" hide
                                            hide-type="circle" />
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <x-mary-icon name="o-hashtag" label="CONFIRMACIÓN" class="mb-2" />
                                        <x-mary-pin ida="pin2" wire:model="pin2" size="3" hide
                                            hide-type="circle" />
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sección de Documentos de Traslado -->
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <div class="p-2 my-2 border rounded-lg border-sky-200 bg-sky-50">
                            <x-mary-toggle label="Retorno de guía" wire:model="isReturn"
                                hint="Active para retorno de guía" />
                        </div>
                        <div class="p-2 my-2 border rounded-lg border-sky-200 bg-sky-50">
                            <x-mary-toggle label="Documentos de Traslado" wire:model.live="showDocTraslado" wire:change="$set('showDocTraslado', !$showDocTraslado)"
                                hint="Active para agregar documentos de traslado" />
                        </div>

                        @if($showDocTraslado)
                            <div class="flex flex-col md:flex-row gap-2 mb-2">
                                <x-mary-input label="Documento" wire:model="docTraslado" placeholder="Documento"
                                    class="w-full" />

                                <x-mary-input label="RUC del emisor" placeholder="20XXXXXXXXX"
                                    wire:model.live="emisorDocTraslado" class="w-full" />

                                <div class="flex items-end space-x-2 mt-2 md:mt-0">
                                    <x-mary-button icon="o-plus" wire:click='addDocTraslado'
                                        class="text-white bg-sky-500 hover:bg-sky-600 rounded-lg" />

                                    <x-mary-button icon="o-no-symbol" wire:click='resetDocTraslado'
                                        class="text-white bg-red-500 hover:bg-red-600 rounded-lg" />
                                </div>
                            </div>

                            @php
                                $headers_docsTraslado = [
                                    ['key' => 'tipoDoc', 'label' => 'Tipo'],
                                    ['key' => 'documento', 'label' => 'Documento'],
                                    ['key' => 'ruc', 'label' => 'RUC'],
                                ];
                            @endphp
                            <x-mary-table :headers="$headers_docsTraslado" :rows="$docsTraslado" striped
                                @row-click="$wire.deleteDocTraslado($event.detail.id)">
                                <x-slot:empty>
                                    <x-mary-icon name="o-cube" label="No se encontraron registros." />
                                </x-slot:empty>
                            </x-mary-table>
                        @endif
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <!-- Sección de Observaciones -->
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <x-mary-textarea label="Observaciones" wire:model="observation"
                                placeholder="Observaciones adicionales" hint="Max 1000 caracteres" rows="3"
                                class="w-full" />
                        </div>

                        <!-- Sección de Transporte -->
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <div class="grid grid-cols-1 gap-2">
                                <x-mary-select label="Transportista" icon="o-user" :options="$transportistas"
                                    wire:model="transportista_id" />
                                <x-mary-select label="Vehículo" icon="o-truck" :options="$vehiculos"
                                    wire:model="vehiculo_id" />
                            </div>
                        </div>
                    </div>
                    </div>

                </div>
                <!-- Sección de Glosa (oculta) -->
                <div class="hidden">
                    <x-mary-textarea label="Glosa" wire:model="glosa" placeholder="Escribe una glosa"
                        hint="Max 1000 caracteres" rows="4" class="w-full" />
                </div>
            </x-mary-step>
        </x-mary-steps>
        <x-slot:actions>
            @if ($step != 1)
                <x-mary-button label="Anterior" wire:click="prev" class='shadow-xl' />
            @endif
            @if ($step == 5)
                <x-mary-button label="Confirmacion" wire:click="finish" class='shadow-xl'/>
            @else
                <x-mary-button label="Siguiente" wire:click="next" class='shadow-xl'/>
            @endif
        </x-slot:actions>
    </x-mary-card>
    @include('livewire.package.register-modal')
    @include('livewire.package.register-final-modal')
</div>
