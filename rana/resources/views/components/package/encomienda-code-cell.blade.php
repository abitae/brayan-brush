@props([
    'encomienda',
    'color' => 'blue',
    'showPin' => false,
    'showPago' => false,
    'showComprobante' => false,
])

@php
    $textColorClasses = [
        'blue' => 'text-blue-600 dark:text-blue-400',
        'green' => 'text-green-600 dark:text-green-400',
        'red' => 'text-red-600 dark:text-red-400',
        'orange' => 'text-orange-600 dark:text-orange-400',
        'yellow' => 'text-yellow-600 dark:text-yellow-400',
    ];
    $textBoldClasses = [
        'blue' => 'text-blue-700 dark:text-blue-300',
        'green' => 'text-green-700 dark:text-green-300',
        'red' => 'text-red-700 dark:text-red-300',
        'orange' => 'text-orange-700 dark:text-orange-300',
        'yellow' => 'text-yellow-700 dark:text-yellow-300',
    ];
    $textColor = $textColorClasses[$color] ?? 'text-blue-600 dark:text-blue-400';
    $textBold = $textBoldClasses[$color] ?? 'text-blue-700 dark:text-blue-300';
@endphp

<td class="px-2 py-1 font-mono {{ $textColor }}">
    <div class="flex flex-col gap-0.5">
        <span class="text-xs font-semibold {{ $textBold }}">
            {{ $encomienda->code }}
        </span>
        @if($showPin && isset($encomienda->pin))
            <span class="text-[11px] text-gray-500 dark:text-zinc-400">PIN: {{ $encomienda->pin }}</span>
        @endif
        @if($showPago && isset($encomienda->estado_pago))
            <span class="text-[11px] text-gray-500 dark:text-zinc-400">
                <span class="font-semibold">Pago:</span>
                {{ $encomienda->estado_pago }}
            </span>
        @endif
        @if($showComprobante && isset($encomienda->tipo_comprobante))
            <span class="text-[11px] text-gray-500 dark:text-zinc-400">
                <span class="font-semibold">Comp.:</span>
                {{ $encomienda->tipo_comprobante }}
            </span>
        @endif
    </div>
</td>

