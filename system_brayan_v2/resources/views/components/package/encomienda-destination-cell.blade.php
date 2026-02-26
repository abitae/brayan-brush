@props([
    'encomienda',
    'showFechaCreacion' => true,
    'showFechaEnvio' => false,
    'showFechaRecepcion' => false,
])

<td class="px-2 py-1 text-gray-600 dark:text-zinc-300 truncate max-w-[180px]">
    @if(isset($encomienda->sucursal_destinatario))
        <div>
            <span class="font-semibold">Destino:</span>
            <span>{{ $encomienda->sucursal_destinatario->name ?? '-' }}</span>
        </div>
    @endif
    @if($showFechaCreacion && isset($encomienda->fecha_creacion))
        <div>
            <span class="font-semibold">Fecha:</span>
            <span>
                <x-package.encomienda-date-formatter :date="$encomienda->fecha_creacion" />
            </span>
        </div>
    @endif
    @if($showFechaEnvio && isset($encomienda->fecha_envio))
        <div>
            <span class="font-semibold">Envío:</span>
            <span>
                <x-package.encomienda-date-formatter :date="$encomienda->fecha_envio" />
            </span>
        </div>
    @endif
    @if($showFechaRecepcion && isset($encomienda->fecha_recepcion))
        <div>
            <span class="font-semibold">Recepción:</span>
            <span>
                <x-package.encomienda-date-formatter :date="$encomienda->fecha_recepcion" />
            </span>
        </div>
    @endif
    @if(isset($encomienda->isHome) && $encomienda->isHome)
        <div>
            <span class="font-semibold">Domicilio:</span>
            <span>{{ $encomienda->direccion_envio ?? '-' }}</span>
        </div>
    @endif
    @if(isset($encomienda->isReturn) && $encomienda->isReturn)
        <div>
            <span class="font-semibold">Retorno:</span>
            <span>Sí</span>
        </div>
    @endif
</td>

