<div class="p-4 w-full max-w-7xl mx-auto space-y-4">
    <!-- Mensaje de error si no hay caja abierta -->
    @if (!$tieneCajaAbierta)
        <div class="mb-4" role="alert" aria-live="assertive">
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3">
                <div class="flex-shrink-0" aria-hidden="true">
                    <flux:icon name="exclamation-circle" class="w-5 h-5 text-red-600" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800">Es necesario aperturar caja para esta operacion</p>
                </div>
            </div>
        </div>
    @endif
    @if ($ruta_id && $tieneCajaAbierta)
        <!-- creacion de encomiendas -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                <!-- Información de Sucursal -->
                <div class="col-span-1 md:col-span-2">
                    <div class="p-2 my-2 border rounded-lg border-sky-200 bg-sky-50">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <flux:icon name="building-office" class="w-5 h-5 text-blue-600" />
                                </div>
                            </div>
                            <div class="flex-1 flex flex-col items-center justify-center text-center">
                                <div class="font-medium text-blue-800">Sucursal de Origen</div>
                                <div class="flex items-center gap-2 justify-center">
                                    <span class="text-sm text-blue-600">{{ $userSucursal->name ?? '-' }} -
                                        {{ $userSucursal->code ?? '-' }}</span>
                                    <span class="inline-flex items-center gap-2">
                                        <span
                                            class="inline-block w-5 h-5 rounded-full border border-zinc-200 dark:border-zinc-700"
                                            style="background: {{ $userSucursal->color ?? '#3B82F6' }}"></span>
                                        <span class="text-xs text-zinc-700">{{ $userSucursal->color ?? '-' }}</span>
                                    </span>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($errors->any())
                <div class="px-6 py-3">
                    <div class="bg-red-50 border border-red-200 rounded-md px-3 py-2 text-xs text-red-700 flex flex-col gap-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2">
                                <flux:icon name="exclamation-circle" class="w-4 h-4 text-red-500" />
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <!-- Content -->
            <div class="p-6">
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200 dark:border-zinc-700 mb-6">
                    <nav class="flex space-x-1" aria-label="Tabs">
                        <button type="button" wire:click="$set('selectedTab', 'remitente')"
                            class="@if ($selectedTab == 'remitente') border-blue-500 text-blue-600 @else border-transparent text-gray-500 dark:text-zinc-400 dark:text-zinc-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                            Remitente
                        </button>
                        <button type="button" wire:click="$set('selectedTab', 'destinatario')"
                            class="@if ($selectedTab == 'destinatario') border-blue-500 text-blue-600 @else border-transparent text-gray-500 dark:text-zinc-400 dark:text-zinc-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                            Destinatario
                        </button>
                        <button type="button" wire:click="$set('selectedTab', 'paquetes')"
                            class="@if ($selectedTab == 'paquetes') border-blue-500 text-blue-600 @else border-transparent text-gray-500 dark:text-zinc-400 dark:text-zinc-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                            Paquetes
                        </button>
                        <button type="button" wire:click="$set('selectedTab', 'envio')"
                            class="@if ($selectedTab == 'envio') border-blue-500 text-blue-600 @else border-transparent text-gray-500 dark:text-zinc-400 dark:text-zinc-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                            Envío
                        </button>
                        <button type="button" wire:click="$set('selectedTab', 'facturacion')"
                            class="@if ($selectedTab == 'facturacion') border-blue-500 text-blue-600 @else border-transparent text-gray-500 dark:text-zinc-400 dark:text-zinc-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                            Facturación
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                @if ($selectedTab == 'remitente')
                    <!-- Tab Remitente -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-white dark:bg-zinc-900 rounded-lg shadow-sm">
                        <!-- Columna 1: Documento, Razón Social, Dirección, Ubigeo -->
                        <div class="flex flex-col gap-4">
                            <div class="flex gap-2">
                                <flux:input.group>
                                    <flux:select class="max-w-fit" wire:model="type_code_remitente" size="xs">
                                        <flux:select.option value="DNI" selected>DNI</flux:select.option>
                                        <flux:select.option value="RUC">RUC</flux:select.option>
                                        <flux:select.option value="CE">CE</flux:select.option>
                                        <flux:select.option value="OTRO">OTRO</flux:select.option>
                                    </flux:select>
                                    <flux:input wire:model="code_remitente" placeholder="Documento" size="xs"
                                        wire:keydown.enter="searchRemitente" autofocus />
                                    <flux:button wire:click="searchRemitente" icon="magnifying-glass" size="xs">
                                        Buscar</flux:button>
                                </flux:input.group>
                            </div>
                            <flux:input placeholder="Dirección" wire:model="address_remitente" size="xs" />
                        </div>
                        <div class="flex flex-col gap-4 justify-between h-full">
                            <div class="flex flex-col gap-4 flex-1">
                                <flux:input placeholder="Razón Social o Nombre Remitente" wire:model="name_remitente"
                                    size="xs" />
                                <flux:select placeholder="Ubigeo" wire:model="ubigeo_remitente" size="xs">
                                    <flux:select.option value=""></flux:select.option>
                                    @foreach ($ubigeos as $ubigeo)
                                        <flux:select.option value="{{ $ubigeo->ubigeo2 }}">
                                            {{ $ubigeo->texto_ubigeo }}
                                        </flux:select.option>
                                    @endforeach

                                </flux:select>
                            </div>
                            <!-- Columna 2: Teléfono y Email al final -->
                            <div class="grid grid-cols-2 gap-2">
                                <flux:input placeholder="Teléfono" wire:model="phone_remitente" size="xs" />
                                <flux:input placeholder="Email" wire:model="email_remitente" size="xs" />
                            </div>
                        </div>
                    </div>
                @endif

                @if ($selectedTab == 'destinatario')
                    <!-- Tab Destinatario -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-white dark:bg-zinc-900 rounded-lg shadow-sm">
                        <!-- Columna 1: Documento, Razón Social, Dirección, Ubigeo -->
                        <div class="flex flex-col gap-4">
                            <div class="flex gap-2">
                                <flux:input.group>
                                    <flux:select class="max-w-fit" wire:model="type_code_destinatario" size="xs">
                                        <flux:select.option value="DNI" selected>DNI</flux:select.option>
                                        <flux:select.option value="RUC">RUC</flux:select.option>
                                        <flux:select.option value="CE">CE</flux:select.option>
                                        <flux:select.option value="PASAPORTE">PASAPORTE</flux:select.option>
                                    </flux:select>
                                    <flux:input wire:model="code_destinatario" placeholder="Documento"
                                        wire:keydown.enter="searchDestinatario" autofocus size="xs" />
                                    <flux:button wire:click="searchDestinatario" icon="magnifying-glass"
                                        size="xs">
                                        Buscar</flux:button>
                                </flux:input.group>
                            </div>
                            <flux:input placeholder="Dirección" wire:model="address_destinatario" size="xs" />
                        </div>
                        <div class="flex flex-col gap-4 justify-between h-full">
                            <div class="flex flex-col gap-4 flex-1">
                                <flux:input placeholder="Razón Social o Nombre Destinatario"
                                    wire:model="name_destinatario" size="xs" />
                                <flux:select placeholder="Ubigeo" wire:model="ubigeo_destinatario" size="xs">
                                    <flux:select.option value=""></flux:select.option>
                                    @foreach ($ubigeos as $ubigeo)
                                        <flux:select.option value="{{ $ubigeo->ubigeo2 }}">
                                            {{ $ubigeo->texto_ubigeo }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <!-- Columna 2: Teléfono y Email al final -->
                            <div class="grid grid-cols-2 gap-2">
                                <flux:input placeholder="Teléfono" wire:model="phone_destinatario" size="xs" />
                                <flux:input placeholder="Email" wire:model="email_destinatario" size="xs" />
                            </div>
                        </div>
                    </div>
                @endif

                @if ($selectedTab == 'paquetes')
                    <!-- Tab Paquetes -->
                    <div class="p-2 bg-white dark:bg-zinc-900 rounded-lg shadow-sm">
                        <div class="mb-4">

                            <div class="flex flex-row gap-1 items-end">
                                <div class="w-24">
                                    <flux:input label="Cantidad" wire:model="paquete_cantidad" type="number"
                                        min="1" placeholder="0" class="text-xs px-1" size="xs" />
                                </div>
                                <div class="w-32">
                                    <flux:select label="Unidad" wire:model="paquete_unidad" class="text-xs px-1"
                                        size="xs">
                                        @foreach ($unidades as $unidad)
                                            <flux:select.option value="{{ $unidad->codigo }}">
                                                {{ $unidad->descripcion }}
                                            </flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </div>
                                <div class="flex-1">
                                    <flux:input label="Descripción" wire:model="paquete_descripcion"
                                        placeholder="Descripción del paquete" size="xs" />
                                </div>
                                <div class="w-24">
                                    <flux:input label="Peso (kg)" wire:model="paquete_peso" type="number"
                                        min="1" step="0.01" placeholder="0.00" size="xs" />
                                </div>
                                <div class="w-24">
                                    <flux:input label="Valor (S/)" wire:model="paquete_valor" type="number"
                                        min="0.01" step="0.01" suffix="S/" placeholder="0.00"
                                        size="xs" />
                                </div>
                                <div>
                                    <flux:button color="primary" wire:click="addPaquete" icon="plus"
                                        size="xs">
                                        Agregar
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-semibold">Lista de Paquetes</h4>
                                @if (count($paquetes) > 0)
                                    <div class="flex items-center gap-2">
                                        <flux:button wire:click="limpiarPaquetes" color="danger" size="xs"
                                            icon="trash">
                                            Limpiar todo
                                        </flux:button>
                                    </div>
                                @endif
                            </div>


                            @if (count($paquetes) > 0)
                                <div class="overflow-x-auto">
                                    <table
                                        class="min-w-full text-xs border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-sm">
                                        <thead>
                                            <tr class="bg-zinc-100 text-zinc-700">
                                                <th
                                                    class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700 text-center font-semibold">
                                                    Cant.</th>
                                                <th
                                                    class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700 text-center font-semibold">
                                                    Unidad</th>
                                                <th
                                                    class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700 font-semibold">
                                                    Descripción</th>
                                                <th
                                                    class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700 text-right font-semibold">
                                                    Peso (kg)</th>
                                                <th
                                                    class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700 text-right font-semibold">
                                                    Valor (S/)</th>
                                                <th
                                                    class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700 text-right font-semibold">
                                                    Subtotal (S/)</th>
                                                <th
                                                    class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700 text-center font-semibold">
                                                    Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($paquetes as $index => $paq)
                                                <tr wire:key="paquete-{{ $index }}-{{ $paq['descripcion'] ?? '' }}"
                                                    class="hover:bg-zinc-50 dark:bg-zinc-800 transition">
                                                    <td
                                                        class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-700 text-center font-medium">
                                                        {{ $paq['cantidad'] ?? 0 }}</td>
                                                    <td
                                                        class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-700 text-center text-sm">
                                                        {{ $paq['unidad'] ?? '' }}</td>
                                                    <td class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-700 truncate max-w-xs"
                                                        title="{{ $paq['descripcion'] ?? '' }}">
                                                        <span
                                                            class="font-medium">{{ $paq['descripcion'] ?? '' }}</span>
                                                    </td>
                                                    <td
                                                        class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-700 text-right">
                                                        <div class="text-sm">{{ number_format($paq['peso'] ?? 0, 2) }}
                                                        </div>
                                                        <div
                                                            class="text-xs text-gray-500 dark:text-zinc-400 dark:text-zinc-500">
                                                            Total:
                                                            {{ number_format($paq['peso_total'] ?? 0, 2) }} kg</div>
                                                    </td>
                                                    <td
                                                        class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-700 text-right">
                                                        <div class="text-sm">
                                                            {{ number_format($paq['valor'] ?? 0, 2) }}</div>
                                                    </td>
                                                    <td
                                                        class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-700 text-right font-semibold text-green-600">
                                                        {{ number_format($paq['subtotal'] ?? 0, 2) }}
                                                    </td>
                                                    <td
                                                        class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-700 text-center">
                                                        <flux:button wire:click="removePaquete({{ $index }})"
                                                            color="danger" size="xs" icon="trash"
                                                            class="rounded-full hover:bg-red-100 transition"
                                                            title="Eliminar paquete">
                                                        </flux:button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-zinc-50 dark:bg-zinc-800 font-bold">
                                                <td
                                                    class="px-3 py-2 border-t border-zinc-200 dark:border-zinc-700 text-center">
                                                    {{ $total_cantidad }}</td>
                                                <td class="px-3 py-2 border-t border-zinc-200 dark:border-zinc-700 text-center"
                                                    colspan="2">Totales</td>
                                                <td
                                                    class="px-3 py-2 border-t border-zinc-200 dark:border-zinc-700 text-right">
                                                    {{ $total_peso }} kg</td>
                                                <td
                                                    class="px-3 py-2 border-t border-zinc-200 dark:border-zinc-700 text-right">
                                                </td>
                                                <td
                                                    class="px-3 py-2 border-t border-zinc-200 dark:border-zinc-700 text-right text-green-600">
                                                    S/ {{ number_format($total_valor, 2) }}
                                                </td>
                                                <td class="px-3 py-2 border-t border-zinc-200 dark:border-zinc-700">
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-gray-500 dark:text-zinc-400 dark:text-zinc-500 text-sm"
                                    role="status" aria-live="polite">
                                    No hay paquetes agregados.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($selectedTab == 'envio')
                    <!-- Tab Envío -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-white dark:bg-zinc-900 rounded-xl shadow-lg">
                        <!-- Columna 1: Sucursal de destino y tipo de entrega -->
                        <div class="flex flex-col gap-4">
                            <flux:select label="Ruta de destino" wire:model.live="ruta_id"
                                placeholder="Seleccione una ruta" class="w-full" size="xs">
                                @forelse ($rutas as $rutaItem)
                                    <flux:select.option value="{{ $rutaItem->id }}">
                                        {{ $rutaItem->sucursalDestino->code }} -
                                        {{ $rutaItem->fecha_salida->format('d/m') }} -
                                        {{ $rutaItem->hora_salida->format('H:i') }} -
                                        {{ $rutaItem->vehiculo->name }} - {{ $rutaItem->transportista->name }}
                                    </flux:select.option>
                                @empty
                                    <flux:select.option value="">No hay rutas disponibles
                                    </flux:select.option>
                                @endforelse
                            </flux:select>

                            <div class="flex items-center gap-4">
                                <flux:checkbox label="¿Entrega a domicilio?" wire:model.live="isHome"
                                    size="xs" />
                                <flux:checkbox label="¿Encomienda de retorno?" wire:model.live="isReturn"
                                    size="xs" />
                            </div>
                        </div>
                        <!-- Columna 2: Seguridad y observaciones -->
                        <div class="flex flex-col gap-4">
                            @if ($isHome)
                                <flux:field>
                                    <flux:label>Dirección de envío</flux:label>
                                    <flux:input wire:model="direccion_envio"
                                        placeholder="Ingrese la dirección de envío" class="mt-2" size="xs" />
                                </flux:field>
                            @else
                                <div class="grid grid-cols-2 gap-2">
                                    <flux:field>
                                        <flux:otp label="PIN de seguridad" private wire:model="pin_seguridad"
                                            length="3" />
                                    </flux:field>
                                    <flux:field>
                                        <flux:otp label="Confirmar PIN" private wire:model="pin_seguridad_confirm"
                                            length="3" />
                                    </flux:field>
                                </div>
                            @endif
                            <flux:textarea label="Observaciones" wire:model="observaciones"
                                placeholder="Ingrese observaciones adicionales" rows="2" class="w-full"
                                size="xs" />
                        </div>
                    </div>
                    <!-- Documentos de traslado al final y a todo el ancho -->
                    <div class="w-full mt-6 bg-white dark:bg-zinc-900 rounded-xl shadow-sm p-6">
                        <flux:checkbox label="¿Agregar documentos de traslado?" wire:model.live="isDocumentosTraslado"
                            class="mt-2" size="xs" />
                        @if ($isDocumentosTraslado)
                            <div>
                                <div class="mb-3 font-semibold text-gray-700 flex items-center gap-2">
                                    <flux:icon name="document-text" class="w-6 h-6 text-blue-600" />
                                    <span class="text-lg">Documentos de traslado</span>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-4 shadow-inner mb-4">
                                    <div class="flex flex-col md:flex-row gap-4 items-end">
                                        <div class="flex-1">
                                            <flux:field>
                                                <flux:label>Tipo de documento</flux:label>
                                                <flux:select wire:model="documento_tipo" placeholder="Seleccione tipo"
                                                    class="w-full" size="xs">
                                                    <flux:select.option value="guia">Guía de Remisión
                                                    </flux:select.option>
                                                    <flux:select.option value="factura">Factura
                                                    </flux:select.option>
                                                    <flux:select.option value="boleta">Boleta</flux:select.option>
                                                    <flux:select.option value="otro">Otro</flux:select.option>
                                                </flux:select>
                                            </flux:field>
                                        </div>
                                        <div class="flex-1">
                                            <flux:field>
                                                <flux:label>N° de documento</flux:label>
                                                <flux:input wire:model="documento_numero"
                                                    placeholder="Ingrese el número" class="w-full" size="xs" />
                                            </flux:field>
                                        </div>
                                        <div class="flex-1">
                                            <flux:field>
                                                <flux:label>RUC del emisor</flux:label>
                                                <flux:input wire:model="documento_ruc_emisor"
                                                    placeholder="Ingrese RUC del emisor" maxlength="11"
                                                    class="w-full" size="xs" />
                                            </flux:field>
                                        </div>
                                        <div class="flex items-end">
                                            <flux:button color="primary" icon="plus"
                                                wire:click="addDocumentoTraslado" tooltip="Agregar documento"
                                                class="transition-transform hover:scale-105" size="xs">
                                                Agregar
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Lista de documentos agregados -->
                                <div class="mt-2">
                                    @if (!empty($documentos_traslado))
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-xs border rounded-lg overflow-hidden shadow">
                                                <thead>
                                                    <tr class="bg-blue-100 text-blue-900">
                                                        <th class="px-4 py-2 border text-left">#</th>
                                                        <th class="px-4 py-2 border text-left">Tipo</th>
                                                        <th class="px-4 py-2 border text-left">N° Documento</th>
                                                        <th class="px-4 py-2 border text-left">RUC Emisor</th>
                                                        <th class="px-4 py-2 border text-center"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($documentos_traslado as $i => $doc)
                                                        <tr wire:key="documento-{{ $i }}-{{ $doc['numero'] ?? '' }}"
                                                            class="hover:bg-blue-50 transition">
                                                            <td
                                                                class="px-4 py-2 border text-gray-600 dark:text-zinc-400 dark:text-zinc-500 text-center">
                                                                {{ $i + 1 }}
                                                            </td>
                                                            <td class="px-4 py-2 border font-medium text-gray-700">
                                                                {{ ucfirst($doc['tipo']) }}
                                                            </td>
                                                            <td class="px-4 py-2 border text-gray-700">
                                                                {{ $doc['numero'] }}
                                                            </td>
                                                            <td class="px-4 py-2 border text-gray-700">
                                                                {{ $doc['ruc_emisor'] }}
                                                            </td>
                                                            <td class="px-4 py-2 border text-center">
                                                                <flux:button color="danger" size="xs"
                                                                    wire:click="removeDocumentoTraslado({{ $i }})"
                                                                    icon="trash" tooltip="Eliminar" />
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-gray-500 dark:text-zinc-400 dark:text-zinc-500 text-sm mt-2 flex items-center gap-2"
                                            role="status" aria-live="polite">
                                            <flux:icon name="exclamation-circle"
                                                class="w-4 h-4 text-gray-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500"
                                                aria-hidden="true" />
                                            No hay documentos de traslado agregados.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($selectedTab == 'facturacion')
                    <!-- Tab Facturación -->

                    <div class="w-full mt-6 bg-white dark:bg-zinc-900 rounded-xl shadow-sm p-6">
                        <!-- Primera fila: Tipo de envío (ocupa todo el ancho) -->
                        <div class="mb-4">
                            <flux:select label="Tipo de envío" wire:model.live="estado_pago"
                                class="w-full text-base font-semibold" size="xs">
                                <flux:select.option value="ENVIO PAGADO">ENVIO PAGADO</flux:select.option>
                                <flux:select.option value="CONTRA ENTREGA">CONTRA ENTREGA</flux:select.option>
                            </flux:select>
                        </div>
                        @if ($estado_pago == 'ENVIO PAGADO')
                            <!-- Segunda fila: Tipo de comprobante y Tipo de pago -->
                            <div class="flex flex-col md:flex-row gap-4 mb-4">
                                <div class="w-full md:w-1/2">
                                    <flux:select label="Tipo de comprobante" wire:model.live="tipo_comprobante"
                                        class="w-full" size="xs">
                                        <flux:select.option value="TICKET">TICKET</flux:select.option>
                                        <flux:select.option value="FACTURA">FACTURA</flux:select.option>
                                        <flux:select.option value="BOLETA">BOLETA</flux:select.option>
                                    </flux:select>
                                </div>
                                <div class="w-full md:w-1/2">
                                    <flux:select label="Tipo de pago" wire:model.live="tipo_pago" class="w-full"
                                        size="xs">
                                        <flux:select.option value="CONTADO">CONTADO</flux:select.option>
                                        <flux:select.option value="CREDITO">CREDITO</flux:select.option>
                                    </flux:select>
                                </div>
                            </div>
                            @if ($tipo_pago == 'CONTADO')
                                <!-- Tercera fila: Método de pago -->
                                <div class="mb-2">
                                    <flux:select label="Método de pago" wire:model.live="metodo_pago" class="w-full"
                                        size="xs">
                                        <flux:select.option value="EFECTIVO">EFECTIVO</flux:select.option>
                                        <flux:select.option value="YAPE">YAPE</flux:select.option>
                                        <flux:select.option value="TARJETA">TARJETA</flux:select.option>
                                        <flux:select.option value="CHEQUE">CHEQUE</flux:select.option>
                                        <flux:select.option value="TRANSFERENCIA">TRANSFERENCIA
                                        </flux:select.option>
                                        <flux:select.option value="OTRO">OTRO</flux:select.option>
                                    </flux:select>
                                </div>
                            @endif

                            @if ($tipo_comprobante != 'TICKET')
                                <!-- Título de la sección de datos de facturación -->
                                <div class="mb-4 mt-6">
                                    <h4 class="text-lg font-semibold text-blue-700 flex items-center gap-2">
                                        <flux:icon name="document-text" class="w-5 h-5 text-blue-600" />
                                        Datos de facturación
                                    </h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Columna 1: Documento y Dirección -->
                                    <div class="flex flex-col gap-4">
                                        <div class="flex gap-2">
                                            <flux:input.group>
                                                <flux:select class="min-w-[90px]" wire:model="type_code_facturacion"
                                                    size="xs">
                                                    @if ($tipo_comprobante == 'FACTURA')
                                                        <flux:select.option selected value="RUC">RUC
                                                        </flux:select.option>
                                                    @elseif($tipo_comprobante == 'BOLETA')
                                                        <flux:select.option selected value="DNI">DNI
                                                        </flux:select.option>
                                                        <flux:select.option value="RUC">RUC</flux:select.option>
                                                        <flux:select.option value="CE">CE</flux:select.option>
                                                        <flux:select.option value="PASAPORTE">PASAPORTE
                                                        </flux:select.option>
                                                    @endif
                                                </flux:select>
                                                <flux:input class="w-full" wire:model="code_facturacion"
                                                    placeholder="Documento" size="xs" />
                                                <flux:button wire:click="searchFacturacion" icon="magnifying-glass"
                                                    class="" size="xs">
                                                    Buscar
                                                </flux:button>
                                            </flux:input.group>
                                        </div>
                                        <flux:input placeholder="Dirección" wire:model="address_facturacion"
                                            size="xs" />
                                        <flux:select placeholder="Ubigeo" wire:model="ubigeo_facturacion"
                                            size="xs">
                                            <flux:select.option value=""></flux:select.option>
                                            @foreach ($ubigeos as $ubigeo)
                                                <flux:select.option value="{{ $ubigeo->ubigeo2 }}">
                                                    {{ $ubigeo->texto_ubigeo }}
                                                </flux:select.option>
                                            @endforeach
                                        </flux:select>
                                    </div>
                                    <!-- Columna 2: Razón Social, Teléfono y Email -->
                                    <div class="flex flex-col gap-4 h-full justify-between">
                                        <flux:input placeholder="Razón Social o Nombre Facturación"
                                            wire:model="name_facturacion" size="xs" />
                                        <div class="grid grid-cols-2 gap-2">
                                            <flux:input placeholder="Teléfono" wire:model="phone_facturacion"
                                                size="xs" />
                                            <flux:input placeholder="Email" wire:model="email_facturacion"
                                                size="xs" />
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
            <!-- Actions -->
            <div
                class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 flex justify-between items-center">
                <flux:button wire:click="leftTabs" icon="arrow-left" class="shadow-xl"
                    :disabled="$selectedTab == 'remitente'" size="xs">
                    Anterior
                </flux:button>
                @if ($selectedTab == 'finalizar')
                    <flux:button variant="primary" wire:click="confirmarEnvio" icon:trailing="check"
                        class="shadow-xl" size="xs">
                        Confirmar
                    </flux:button>
                @else
                    <flux:button wire:click="validateTabs" icon:trailing="arrow-right" class="shadow-xl"
                        size="xs">
                        Siguiente
                    </flux:button>
                @endif

            </div>

        </div>
    @else
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 mt-6">
            <div class="p-6">
                @if (!$tieneCajaAbierta)
                    <h3 class="text-lg font-semibold text-red-700">Caja no abierta</h3>
                    <p class="text-sm text-gray-600 dark:text-zinc-400 dark:text-zinc-500">Es necesario aperturar caja
                        para esta operacion</p>
                    <flux:button href="{{ route('caja') }}" icon:trailing="arrow-right" class="shadow-xl mt-4"
                        size="xs">
                        Ir a Caja
                    </flux:button>
                @else
                    <h3 class="text-lg font-semibold text-blue-700">No hay rutas disponibles</h3>
                    <p class="text-sm text-gray-600 dark:text-zinc-400 dark:text-zinc-500">Configure una ruta para
                        poder crear una encomienda</p>
                    <flux:button href="{{ route('package.encomienda.ruta') }}" icon:trailing="plus"
                        class="shadow-xl" size="xs">
                        Crear ruta
                    </flux:button>
                @endif
            </div>
        </div>
    @endif
    <div class="mt-6">
        <!-- Listado de Encomiendas (Minimalista) -->
        <div class="mt-6 bg-white dark:bg-zinc-900 rounded-lg shadow border border-gray-100 dark:border-zinc-700 p-2">
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 border-b border-gray-100 dark:border-zinc-700 bg-yellow-50/30">
                <div class="flex items-center gap-2 mb-2 md:mb-0">
                    <flux:icon name="cube" class="w-6 h-6 text-yellow-500" />
                    <h3 class="font-semibold text-yellow-700 text-lg">Encomiendas registradas</h3>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label
                            class="text-xs text-gray-600 dark:text-zinc-400 dark:text-zinc-500 font-medium hidden sm:block">
                            <flux:icon name="magnifying-glass"
                                class="w-4 h-4 text-gray-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 mr-1" />
                            Buscar
                        </label>
                        <flux:input type="search" wire:model.live="searchEncomienda"
                            placeholder="Código, remitente o destinatario..." size="xs"
                            class="w-full sm:w-64 max-w-full border border-yellow-200 focus:border-yellow-400 transition" />
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label
                            class="text-xs text-gray-600 dark:text-zinc-400 dark:text-zinc-500 font-medium hidden sm:block">
                            <flux:icon name="building-office"
                                class="w-4 h-4 text-gray-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 mr-1" />
                            Sucursal
                        </label>
                        <flux:select wire:model.live="sucursal_destino_filter" size="xs"
                            class="w-full sm:w-56 max-w-full border border-yellow-200 focus:border-yellow-400 transition">
                            <flux:select.option value="">Todas</flux:select.option>
                            @foreach ($sucursales as $sucursal)
                                <flux:select.option value="{{ $sucursal->id }}">{{ $sucursal->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label
                            class="text-xs text-gray-600 dark:text-zinc-400 dark:text-zinc-500 font-medium hidden sm:block">
                            <flux:icon name="calendar"
                                class="w-4 h-4 text-gray-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 mr-1" />
                            Fecha
                        </label>
                        <flux:input type="date" wire:model.live="fecha_creacion_filter" size="xs"
                            class="w-full sm:w-36 max-w-full border border-yellow-200 focus:border-yellow-400 transition" />
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border rounded-lg overflow-hidden shadow p-2">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-zinc-800">
                            <th
                                class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 dark:text-zinc-500 font-medium">
                                #</th>
                            <th
                                class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 dark:text-zinc-500 font-medium">
                                Código</th>
                            <th
                                class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 dark:text-zinc-500 font-medium">
                                Remitente y Destinatario</th>
                            <th
                                class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 dark:text-zinc-500 font-medium">
                                Destino y Fecha</th>
                            <th
                                class="px-2 py-2 text-left text-gray-500 dark:text-zinc-400 dark:text-zinc-500 font-medium">
                                Monto S/</th>
                            <th
                                class="px-2 py-2 text-center text-gray-500 dark:text-zinc-400 dark:text-zinc-500 font-medium">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($encomiendas as $i => $encomienda)
                            <tr wire:key="encomienda-{{ $encomienda->id }}"
                                class="hover:bg-gray-50 dark:hover:bg-zinc-800 dark:bg-zinc-800 transition">
                                <td
                                    class="px-2 py-1 text-gray-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500">
                                    {{ $loop->iteration }}</td>
                                <td class="px-2 py-1 font-mono text-blue-600">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-xs font-semibold text-blue-700">
                                            {{ $encomienda->code }}
                                        </span>
                                        <span class="text-[11px] text-gray-500 dark:text-zinc-400 dark:text-zinc-500">
                                            <span class="font-semibold">Pago:</span>
                                            {{ $encomienda->estado_pago }}
                                        </span>
                                        <span class="text-[11px] text-gray-500 dark:text-zinc-400 dark:text-zinc-500">
                                            <span class="font-semibold">Comp.:</span>
                                            {{ $encomienda->tipo_comprobante }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-2 py-1 text-gray-700 truncate max-w-[180px]">
                                    <div>
                                        <span class="font-semibold text-blue-700">Remitente:</span>
                                        <span>{{ $encomienda->remitente->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-yellow-700">Destinatario:</span>
                                        <span>{{ $encomienda->destinatario->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td
                                    class="px-2 py-1 text-gray-600 dark:text-zinc-400 dark:text-zinc-500 truncate max-w-[220px] align-top">
                                    <div class="mb-0.5 leading-tight">
                                        <div class="flex items-center gap-1">
                                            <span class="font-semibold text-zinc-700">Destino:</span>
                                            <span class="text-blue-700 font-medium">
                                                {{ $encomienda->sucursal_destinatario->name ?? '-' }}
                                            </span>
                                            @if ($encomienda->isReturn)
                                                <span
                                                    class="ml-1.5 text-xs font-semibold border border-yellow-200 bg-yellow-50 rounded px-1 text-yellow-800">Retorno</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1 mt-1">
                                            <flux:icon name="calendar-days" class="w-3.5 h-3.5 text-yellow-600" />
                                            <span class="font-semibold text-zinc-700">Fecha:</span>
                                            <span class="text-gray-600 dark:text-zinc-400 dark:text-zinc-500">
                                                {{ $encomienda->fecha_creacion ? \Carbon\Carbon::parse($encomienda->fecha_creacion)->format('d/m/Y H:i') : '-' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span
                                                class="inline-flex items-center text-xs font-semibold 
                                            @if ($encomienda->isHome) border border-green-200 bg-green-50 text-green-700 @else border border-cyan-200 bg-cyan-50 text-cyan-700 @endif
                                            rounded px-1 py-0.5"
                                                title="{{ $encomienda->isHome ? 'Entrega en domicilio' : 'Recojo en agencia' }}">
                                                {{ $encomienda->isHome ? 'Domicilio' : 'Agencia' }}
                                            </span>
                                            <span class="truncate text-gray-700"
                                                title="{{ $encomienda->isHome ? $encomienda->direccion_envio : $encomienda->sucursal_destinatario->name ?? '-' }}">
                                                {{ $encomienda->isHome ? ($encomienda->direccion_envio ?: '-') : $encomienda->sucursal_destinatario->name ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-1">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs">
                                        {{ $encomienda->monto ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-2 py-1 text-center">
                                    <flux:dropdown>
                                        <flux:button icon:trailing="bars-3" size="xs" color="zinc">
                                        </flux:button>

                                        <flux:menu>
                                            <flux:menu.item icon="document"
                                                wire:click="verTicketPDF({{ $encomienda->id }})">
                                                Ver Ticket
                                            </flux:menu.item>

                                            @if ($encomienda->doc_factura)
                                                <flux:menu.item icon="document-text"
                                                    wire:click="verInvoicePDF({{ $encomienda->id }})">
                                                    VerFactura/Boleta
                                                </flux:menu.item>
                                            @endif

                                            @if ($encomienda->doc_guia)
                                                <flux:menu.item icon="document"
                                                    wire:click="verGuiaPDF({{ $encomienda->id }})">
                                                    Ver Guía
                                                </flux:menu.item>
                                            @else
                                                <flux:menu.item icon="plus"
                                                    wire:click="crearGuiaPDF({{ $encomienda->id }})">
                                                    Crear Guía
                                                </flux:menu.item>
                                            @endif

                                            <flux:menu.item icon="document"
                                                wire:click="verStickerPDF({{ $encomienda->id }})">
                                                Ver Sticker
                                            </flux:menu.item>
                                            <flux:menu.item icon="document-text"
                                                wire:click="verDeclaracionPDF({{ $encomienda->id }})">
                                                Ver Declaración jurada
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="px-2 py-4 text-center text-gray-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500">
                                    No hay encomiendas
                                    registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-2 flex justify-end">
                {{ $encomiendas->links() }}
            </div>
        </div>

        <!-- Modal de Confirmación de Encomienda -->
        <flux:modal wire:model="modalConfirmacionEncomienda" class="w-full max-w-3xl mx-auto"
            aria-labelledby="modal-confirmacion-title" aria-describedby="modal-confirmacion-description">
            <div class="px-2 py-4 border-b border-gray-200 dark:border-zinc-700 space-y-4" role="dialog"
                aria-live="polite">
                {{-- Remitente y Destinatario en dos columnas --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($this->getPersonasModalData() as $persona)
                        <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-md border p-3">
                            <div class="flex items-center mb-2 justify-between">
                                <div class="flex items-center">
                                    <flux:icon :name="$loop->first ? 'user' : 'user-group'"
                                        class="w-6 h-6 text-blue-500 mr-1" />
                                    <h3 class="font-bold text-blue-700 text-base">{{ $persona['tipo'] }}</h3>
                                </div>
                                <flux:icon :name="$persona['id'] ? 'check' : 'x-mark'"
                                    class="w-6 h-6 {{ $persona['id'] ? 'text-green-500 border-green-500' : 'text-red-500 border-red-500' }} border rounded-full p-1" />
                            </div>
                            <div class="text-xs space-y-1">
                                <div><span class="font-semibold">Tipo Doc:</span> {{ $persona['type_code'] ?? '-' }}
                                </div>
                                <div><span class="font-semibold">N° Doc:</span> {{ $persona['code'] ?? '-' }}</div>
                                <div><span class="font-semibold">Nombre:</span> {{ $persona['name'] ?? '-' }}</div>
                                <div><span class="font-semibold">Dirección:</span> {{ $persona['address'] ?? '-' }}
                                </div>
                                <div><span class="font-semibold">Ubigeo:</span>
                                    {{ $persona['texto_ubigeo'] ?? '-' }}-{{ $persona['ubigeo'] ?? '-' }}</div>
                                <div><span class="font-semibold">Teléfono:</span> {{ $persona['phone'] ?? '-' }}
                                </div>
                                <div><span class="font-semibold">Email:</span> {{ $persona['email'] ?? '-' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Envío y Facturación en dos columnas --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-1">
                        <div class="font-semibold text-blue-700 mb-1">Datos de Envío</div>
                        @if ($ruta)
                            <div>
                                <span class="font-semibold">Sucursal destino:</span>
                                {{ $ruta->sucursalDestino->name ?? '-' }} <br>
                                <span class="font-semibold">Fecha salida:</span>
                                {{ $ruta->fecha_salida->format('d/m') ?? '-' }} <br>
                                <span class="font-semibold">Hora salida:</span>
                                {{ $ruta->hora_salida->format('H:i') ?? '-' }} <br>
                                <span class="font-semibold">Vehículo:</span> {{ $ruta->vehiculo->name ?? '-' }} <br>
                                <span class="font-semibold">Transportista:</span>
                                {{ $ruta->transportista->name ?? '-' }}
                                <br>
                            </div>
                        @endif
                        <div><span class="font-semibold">¿Domicilio?</span> {{ $isHome ? 'Sí' : 'No' }}</div>
                        <div><span class="font-semibold">¿Retorno?</span> {{ $isReturn ? 'Sí' : 'No' }}</div>
                        <div><span class="font-semibold">Dirección de Envío:</span> {{ $direccion_envio ?? '-' }}
                        </div>
                        @if (!$isHome)
                            <div><span class="font-semibold">PIN:</span> {{ $pin_seguridad ? '***' : '-' }}</div>
                        @endif
                        <div><span class="font-semibold">Observaciones:</span> {{ $observaciones ?? '-' }}</div>
                    </div>
                    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border p-3 text-xs space-y-1">
                        <div class="font-semibold text-blue-700 mb-1">Facturación</div>
                        <div><span class="font-semibold">Tipo de Comprobante:</span> {{ $tipo_comprobante ?? '-' }}
                        </div>
                        <div><span class="font-semibold">Tipo de Pago:</span> {{ $tipo_pago ?? '-' }}</div>
                        <div><span class="font-semibold">Método de Pago:</span> {{ $metodo_pago ?? '-' }}</div>
                        <div><span class="font-semibold">Estado de Pago:</span> {{ $estado_pago ?? '-' }}</div>
                        <div><span class="font-semibold">Documento:</span>
                            {{ ($type_code_facturacion ?? '-') . ': ' . ($code_facturacion ?? '-') }}</div>
                        <div><span class="font-semibold">Nombre/Razón Social:</span> {{ $name_facturacion ?? '-' }}
                        </div>
                        <div><span class="font-semibold">Dirección:</span> {{ $address_facturacion ?? '-' }}</div>

                    </div>
                </div>

                {{-- Paquetes al final y a todo el ancho --}}
                <div class="bg-gradient-to-br from-yellow-50 to-white rounded-xl shadow-md border p-3">
                    <div class="flex items-center mb-2">
                        <flux:icon name="cube" class="w-6 h-6 text-yellow-500 mr-1" />
                        <h3 class="font-bold text-yellow-700 text-base">Paquetes</h3>
                    </div>
                    @if (is_array($paquetes) && count($paquetes) > 0)
                        <table class="min-w-full text-xs text-left text-gray-700">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Peso</th>
                                    <th>Valor</th>
                                    <th>Cant.</th>
                                    <th>Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paquetes as $index => $paquete)
                                    <tr wire:key="modal-paquete-{{ $index }}">
                                        <td>{{ $paquete['descripcion'] ?? '-' }}</td>
                                        <td>{{ $paquete['peso'] ?? '-' }}</td>
                                        <td>{{ $paquete['valor'] ?? '-' }}</td>
                                        <td>{{ $paquete['cantidad'] ?? '-' }}</td>
                                        <td>{{ $paquete['unidad'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-gray-500 dark:text-zinc-400 dark:text-zinc-500 text-xs py-2" role="status"
                            aria-live="polite">
                            No se han agregado paquetes.
                        </div>
                    @endif
                </div>

                {{-- Botón Confirmar al final --}}
                <div class="flex justify-center mt-6 gap-2">
                    <flux:button wire:click="$set('modalConfirmacionEncomienda', false)" icon:trailing="x-mark"
                        class="shadow-xl" size="xs">
                        Cancelar
                    </flux:button>
                    <flux:button variant="primary" wire:click="confirmacionEncomienda" icon:trailing="check"
                        class="shadow-xl" size="xs">
                        Confirmar
                    </flux:button>
                </div>
            </div>
        </flux:modal>

        <!-- Modal de Imprimir Ticket (Minimalista) -->
        <flux:modal wire:model="modalImprimirTicket" @close="resetComponent" :dismissible="false"
            class="w-full max-w-2xl mx-auto">
            <div class="p-4 space-y-3">
                <!-- Encabezado minimalista -->
                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                    <div class="flex items-center gap-2">
                        <flux:icon name="document-text" class="w-6 h-6 text-blue-500" />
                        <span class="font-semibold text-gray-800 text-base">Encomienda</span>
                    </div>
                    @if ($encomienda_id)
                        <a href="{{ route('encomienda.ticket.pdf', ['id' => $encomienda_id]) }}" target="_blank"
                            class="flex items-center gap-1 px-2 py-1 text-xs text-blue-600 hover:underline"
                            title="Abrir PDF en nueva pestaña">
                            <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                            PDF
                        </a>
                    @endif
                </div>

                <!-- Contenido -->
                <div>
                    <!-- Estado de carga -->
                    <div id="loadingState" class="flex items-center justify-center py-8">
                        <div>
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400 mx-auto mb-2">
                            </div>
                            <p
                                class="text-xs text-gray-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 text-center">
                                Cargando ticket...</p>
                        </div>
                    </div>

                    <!-- PDF -->
                    <div id="pdfContainer" class="hidden">
                        <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                            <div class="flex justify-end mb-1">
                                <button type="button" onclick="toggleFullscreen_pdfFrame()"
                                    class="text-gray-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 hover:text-gray-600 dark:text-zinc-400 dark:text-zinc-500 text-xs flex items-center gap-1"
                                    title="Pantalla completa">
                                    <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                                </button>
                            </div>
                            @if ($encomienda_id)
                                <iframe id="pdfFrame"
                                    src="{{ route('encomienda.ticket.pdf', ['id' => $encomienda_id]) }}"
                                    class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                                    onload="hideLoading_loadingState()" onerror="showError_errorState()">
                                </iframe>
                            @else
                                <div class="text-center py-8">
                                    <flux:icon name="exclamation-triangle"
                                        class="w-10 h-10 text-yellow-400 mx-auto mb-2" />
                                    <div class="text-sm text-gray-700 mb-2">No hay encomienda seleccionada.</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Error -->
                    <div id="errorState" class="hidden">
                        <div class="text-center py-8">
                            <flux:icon name="exclamation-triangle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                            <div class="text-sm text-gray-700 mb-2">No se pudo cargar el ticket.</div>
                            <div class="flex justify-center gap-2">
                                <flux:button wire:click="refreshTicket" icon="arrow-path" size="xs"
                                    class="bg-blue-500 text-white">
                                    Reintentar
                                </flux:button>
                                @if ($encomienda_id)
                                    <a href="{{ route('encomienda.ticket.pdf', ['id' => $encomienda_id]) }}"
                                        target="_blank"
                                        class="flex items-center gap-1 px-2 py-1 text-xs text-blue-600 hover:underline">
                                        <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                        PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                    <flux:button wire:click="$set('modalImprimirTicket', false)" size="xs"
                        class="text-gray-500 dark:text-zinc-400 dark:text-zinc-500 hover:text-gray-700">
                        Cerrar
                    </flux:button>
                </div>
            </div>

        </flux:modal>

        <!-- Modal de Ver Guía de Remisión (Minimalista) -->
        <flux:modal wire:model="modalVerGuia" @close="resetComponent" :dismissible="false"
            class="w-full max-w-2xl mx-auto">
            <div class="p-4 space-y-3">
                <!-- Encabezado minimalista -->
                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                    <div class="flex items-center gap-2">
                        <flux:icon name="document-text" class="w-6 h-6 text-purple-500" />
                        <span class="font-semibold text-gray-800 text-base">Guía de Remisión</span>
                    </div>
                    <a href="{{ route('encomienda.guia.pdf', ['id' => $encomienda_id ?? 0]) }}" target="_blank"
                        class="flex items-center gap-1 px-2 py-1 text-xs text-purple-600 hover:underline"
                        title="Abrir PDF en nueva pestaña">
                        <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                        PDF
                    </a>
                </div>

                <!-- Contenido -->
                <div>
                    <!-- Estado de carga -->
                    <div id="loadingStateGuia" class="flex items-center justify-center py-8">
                        <div>
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-400 mx-auto mb-2">
                            </div>
                            <p
                                class="text-xs text-gray-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 text-center">
                                Cargando guía de remisión...</p>
                        </div>
                    </div>

                    <!-- PDF -->
                    <div id="pdfContainerGuia" class="hidden">
                        <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                            <div class="flex justify-end mb-1">
                                <button type="button" onclick="toggleFullscreen_pdfFrameGuia()"
                                    class="text-gray-400 dark:text-zinc-500 dark:text-zinc-400 dark:text-zinc-500 hover:text-gray-600 dark:text-zinc-400 dark:text-zinc-500 text-xs flex items-center gap-1"
                                    title="Pantalla completa">
                                    <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                                </button>
                            </div>
                            <iframe id="pdfFrameGuia"
                                src="{{ route('encomienda.guia.pdf', ['id' => $encomienda_id ?? 0]) }}"
                                class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                                onload="hideLoading_loadingStateGuia()" onerror="showError_errorStateGuia()">
                            </iframe>
                        </div>
                    </div>

                    <!-- Error -->
                    <div id="errorStateGuia" class="hidden">
                        <div class="text-center py-8">
                            <flux:icon name="exclamation-triangle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                            <div class="text-sm text-gray-700 mb-2">No se pudo cargar la guía de remisión.</div>
                            <div class="flex justify-center gap-2">
                                <flux:button wire:click="refreshGuia" icon="arrow-path" size="xs"
                                    class="bg-purple-500 text-white">
                                    Reintentar
                                </flux:button>
                                <a href="{{ route('encomienda.guia.pdf', ['id' => $encomienda_id ?? 0]) }}"
                                    target="_blank"
                                    class="flex items-center gap-1 px-2 py-1 text-xs text-purple-600 hover:underline">
                                    <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                    PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                    <flux:button wire:click="$set('modalVerGuia', false)" size="xs"
                        class="text-gray-500 dark:text-zinc-400 dark:text-zinc-500 hover:text-gray-700">
                        Cerrar
                    </flux:button>
                </div>
            </div>

        </flux:modal>

        <!-- Modal de Ver Invoice/Factura (Minimalista) -->
        <flux:modal wire:model="modalVerInvoice" :dismissible="true" class="w-full max-w-2xl mx-auto">
            <div class="p-4 space-y-3">
                <!-- Encabezado minimalista -->
                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                    <div class="flex items-center gap-2">
                        <flux:icon name="document-text" class="w-6 h-6 text-green-500" />
                        <span class="font-semibold text-gray-800 dark:text-white text-base">Factura</span>
                    </div>
                    <a href="{{ route('pdf.invoice.80mm', ['invoice' => $invoice_id ?? 0]) }}" target="_blank"
                        class="flex items-center gap-1 px-2 py-1 text-xs text-green-600 hover:underline"
                        title="Abrir PDF en nueva pestaña">
                        <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                        PDF
                    </a>
                </div>

                <!-- Contenido -->
                <div>
                    <!-- Estado de carga -->
                    <div id="loadingStateInvoiceCreate" class="flex items-center justify-center py-8">
                        <div>
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-400 mx-auto mb-2">
                            </div>
                            <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando factura...</p>
                        </div>
                    </div>

                    <!-- PDF -->
                    <div id="pdfContainerInvoiceCreate" class="hidden">
                        <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                            <div class="flex justify-end mb-1">
                                <button type="button" onclick="toggleFullscreen_pdfFrameInvoiceCreate()"
                                    class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                    title="Pantalla completa">
                                    <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                                </button>
                            </div>
                            <iframe id="pdfFrameInvoiceCreate"
                                src="{{ route('pdf.invoice.80mm', ['invoice' => $invoice_id ?? 0]) }}"
                                class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                                onload="hideLoading_loadingStateInvoiceCreate()"
                                onerror="showError_errorStateInvoiceCreate()">
                            </iframe>
                        </div>
                    </div>

                    <!-- Error -->
                    <div id="errorStateInvoiceCreate" class="hidden">
                        <div class="text-center py-8">
                            <flux:icon name="exclamation-triangle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">No se pudo cargar la factura.
                            </div>
                            <div class="flex justify-center gap-2">
                                <flux:button wire:click="refreshInvoice" icon="arrow-path" size="xs"
                                    class="bg-green-500 text-white">
                                    Reintentar
                                </flux:button>
                                <a href="{{ route('pdf.invoice.80mm', ['invoice' => $invoice_id ?? 0]) }}"
                                    target="_blank"
                                    class="flex items-center gap-1 px-2 py-1 text-xs text-green-600 hover:underline">
                                    <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                    PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                    <flux:button wire:click="$set('modalVerInvoice', false)" size="xs"
                        class="text-gray-500 dark:text-zinc-400 hover:text-gray-700">
                        Cerrar
                    </flux:button>
                </div>
            </div>

        </flux:modal>

        <!-- Modal de Ver Sticker A6 (Minimalista) -->
        <flux:modal wire:model="modalVerSticker" @close="resetComponent" :dismissible="false"
            class="w-full max-w-2xl mx-auto">
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                    <div class="flex items-center gap-2">
                        <flux:icon name="document" class="w-6 h-6 text-amber-500" />
                        <span class="font-semibold text-gray-800 text-base">Sticker A6</span>
                    </div>
                    @if ($encomienda_id)
                        <a href="{{ route('pdf.sticker.a6', ['encomienda' => $encomienda_id]) }}" target="_blank"
                            class="flex items-center gap-1 px-2 py-1 text-xs text-amber-600 hover:underline"
                            title="Abrir PDF en nueva pestaña">
                            <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                            PDF
                        </a>
                    @endif
                </div>

                <div>
                    <div id="loadingStateSticker" class="flex items-center justify-center py-8">
                        <div>
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-400 mx-auto mb-2">
                            </div>
                            <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando sticker...</p>
                        </div>
                    </div>

                    <div id="pdfContainerSticker" class="hidden">
                        <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                            <div class="flex justify-end mb-1">
                                <button type="button" onclick="toggleFullscreen_pdfFrameSticker()"
                                    class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                    title="Pantalla completa">
                                    <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                                </button>
                            </div>
                            @if ($encomienda_id)
                                <iframe id="pdfFrameSticker"
                                    src="{{ route('pdf.sticker.a6', ['encomienda' => $encomienda_id]) }}"
                                    class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                                    onload="hideLoading_loadingStateSticker()"
                                    onerror="showError_errorStateSticker()">
                                </iframe>
                            @else
                                <div class="text-center py-8">
                                    <flux:icon name="exclamation-triangle"
                                        class="w-10 h-10 text-yellow-400 mx-auto mb-2" />
                                    <div class="text-sm text-gray-700 mb-2">No hay encomienda seleccionada.</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div id="errorStateSticker" class="hidden">
                        <div class="text-center py-8">
                            <flux:icon name="exclamation-triangle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                            <div class="text-sm text-gray-700 mb-2">No se pudo cargar el sticker.</div>
                            <div class="flex justify-center gap-2">
                                <flux:button wire:click="refreshSticker" icon="arrow-path" size="xs"
                                    class="bg-amber-500 text-white">
                                    Reintentar
                                </flux:button>
                                @if ($encomienda_id)
                                    <a href="{{ route('pdf.sticker.a6', ['encomienda' => $encomienda_id]) }}"
                                        target="_blank"
                                        class="flex items-center gap-1 px-2 py-1 text-xs text-amber-600 hover:underline">
                                        <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                        PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                    <flux:button wire:click="$set('modalVerSticker', false)" size="xs"
                        class="text-gray-500 dark:text-zinc-400 hover:text-gray-700">
                        Cerrar
                    </flux:button>
                </div>
            </div>
        </flux:modal>

        <!-- Modal de Ver Declaración Jurada (Minimalista) -->
        <flux:modal wire:model="modalVerDeclaracion" @close="resetComponent" :dismissible="false"
            class="w-full max-w-2xl mx-auto">
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-zinc-700">
                    <div class="flex items-center gap-2">
                        <flux:icon name="document-text" class="w-6 h-6 text-indigo-500" />
                        <span class="font-semibold text-gray-800 text-base">Declaración Jurada</span>
                    </div>
                    @if ($encomienda_id)
                        <a href="{{ route('pdf.declaracion', ['encomienda' => $encomienda_id]) }}" target="_blank"
                            class="flex items-center gap-1 px-2 py-1 text-xs text-indigo-600 hover:underline"
                            title="Abrir PDF en nueva pestaña">
                            <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                            PDF
                        </a>
                    @endif
                </div>

                <div>
                    <div id="loadingStateDeclaracion" class="flex items-center justify-center py-8">
                        <div>
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-400 mx-auto mb-2">
                            </div>
                            <p class="text-xs text-gray-400 dark:text-zinc-500 text-center">Cargando declaración...</p>
                        </div>
                    </div>

                    <div id="pdfContainerDeclaracion" class="hidden">
                        <div class="rounded border border-gray-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2">
                            <div class="flex justify-end mb-1">
                                <button type="button" onclick="toggleFullscreen_pdfFrameDeclaracion()"
                                    class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 text-xs flex items-center gap-1"
                                    title="Pantalla completa">
                                    <flux:icon name="arrows-pointing-out" class="w-4 h-4" />
                                </button>
                            </div>
                            @if ($encomienda_id)
                                <iframe id="pdfFrameDeclaracion"
                                    src="{{ route('pdf.declaracion', ['encomienda' => $encomienda_id]) }}"
                                    class="w-full border-0" style="min-height: 400px; max-height: 60vh;"
                                    onload="hideLoading_loadingStateDeclaracion()"
                                    onerror="showError_errorStateDeclaracion()">
                                </iframe>
                            @else
                                <div class="text-center py-8">
                                    <flux:icon name="exclamation-triangle"
                                        class="w-10 h-10 text-yellow-400 mx-auto mb-2" />
                                    <div class="text-sm text-gray-700 mb-2">No hay encomienda seleccionada.</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div id="errorStateDeclaracion" class="hidden">
                        <div class="text-center py-8">
                            <flux:icon name="exclamation-triangle" class="w-10 h-10 text-red-400 mx-auto mb-2" />
                            <div class="text-sm text-gray-700 mb-2">No se pudo cargar la declaración.</div>
                            <div class="flex justify-center gap-2">
                                <flux:button wire:click="refreshDeclaracion" icon="arrow-path" size="xs"
                                    class="bg-indigo-500 text-white">
                                    Reintentar
                                </flux:button>
                                @if ($encomienda_id)
                                    <a href="{{ route('pdf.declaracion', ['encomienda' => $encomienda_id]) }}"
                                        target="_blank"
                                        class="flex items-center gap-1 px-2 py-1 text-xs text-indigo-600 hover:underline">
                                        <flux:icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                        PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-zinc-700">
                    <flux:button wire:click="$set('modalVerDeclaracion', false)" size="xs"
                        class="text-gray-500 dark:text-zinc-400 hover:text-gray-700">
                        Cerrar
                    </flux:button>
                </div>
            </div>
        </flux:modal>

        <!-- Scripts unificados para modales PDF -->
        <script>
            /**
             * Funciones genéricas para manejo de modales PDF
             */
            function initPdfModal(config) {
                const {
                    loadingId,
                    containerId,
                    errorId,
                    frameId,
                    refreshEvent
                } = config;

                // Función para ocultar loading y mostrar PDF
                window[`hideLoading_${loadingId}`] = function() {
                    const loadingEl = document.getElementById(loadingId);
                    const containerEl = document.getElementById(containerId);
                    if (loadingEl) loadingEl.classList.add('hidden');
                    if (containerEl) containerEl.classList.remove('hidden');
                };

                // Función para mostrar error
                window[`showError_${errorId}`] = function() {
                    const loadingEl = document.getElementById(loadingId);
                    const errorEl = document.getElementById(errorId);
                    if (loadingEl) loadingEl.classList.add('hidden');
                    if (errorEl) errorEl.classList.remove('hidden');
                };

                // Función para pantalla completa
                window[`toggleFullscreen_${frameId}`] = function() {
                    const iframe = document.getElementById(frameId);
                    if (!iframe) return;

                    if (iframe.requestFullscreen) {
                        iframe.requestFullscreen();
                    } else if (iframe.webkitRequestFullscreen) {
                        iframe.webkitRequestFullscreen();
                    } else if (iframe.msRequestFullscreen) {
                        iframe.msRequestFullscreen();
                    }
                };

                // Fallback para mostrar PDF si tarda mucho
                setTimeout(function() {
                    const containerEl = document.getElementById(containerId);
                    if (containerEl && !containerEl.classList.contains('hidden')) {
                        window[`hideLoading_${loadingId}`]();
                    }
                }, 10000);

                // Listener para evento de refresh de Livewire
                if (refreshEvent) {
                    document.addEventListener('livewire:init', () => {
                        Livewire.on(refreshEvent, () => {
                            const errorEl = document.getElementById(errorId);
                            const loadingEl = document.getElementById(loadingId);
                            const containerEl = document.getElementById(containerId);
                            const iframe = document.getElementById(frameId);

                            if (errorEl) errorEl.classList.add('hidden');
                            if (loadingEl) loadingEl.classList.remove('hidden');
                            if (containerEl) containerEl.classList.add('hidden');
                            if (iframe) iframe.src = iframe.src;
                        });
                    });
                }
            }

            // Inicializar modales PDF al cargar
            document.addEventListener('DOMContentLoaded', function() {
                // Modal de Ticket
                initPdfModal({
                    loadingId: 'loadingState',
                    containerId: 'pdfContainer',
                    errorId: 'errorState',
                    frameId: 'pdfFrame',
                    refreshEvent: 'ticket-refreshed'
                });

                // Modal de Guía
                initPdfModal({
                    loadingId: 'loadingStateGuia',
                    containerId: 'pdfContainerGuia',
                    errorId: 'errorStateGuia',
                    frameId: 'pdfFrameGuia',
                    refreshEvent: 'guia-refreshed'
                });

                // Modal de Invoice
                initPdfModal({
                    loadingId: 'loadingStateInvoiceCreate',
                    containerId: 'pdfContainerInvoiceCreate',
                    errorId: 'errorStateInvoiceCreate',
                    frameId: 'pdfFrameInvoiceCreate',
                    refreshEvent: 'invoice-refreshed'
                });

                // Modal de Sticker
                initPdfModal({
                    loadingId: 'loadingStateSticker',
                    containerId: 'pdfContainerSticker',
                    errorId: 'errorStateSticker',
                    frameId: 'pdfFrameSticker',
                    refreshEvent: 'sticker-refreshed'
                });

                // Modal de Declaración
                initPdfModal({
                    loadingId: 'loadingStateDeclaracion',
                    containerId: 'pdfContainerDeclaracion',
                    errorId: 'errorStateDeclaracion',
                    frameId: 'pdfFrameDeclaracion',
                    refreshEvent: 'declaracion-refreshed'
                });
            });
        </script>
    </div>
