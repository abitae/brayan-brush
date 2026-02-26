@props([
    'searchPlaceholder' => 'Código, PIN, remitente o destinatario...',
    'searchModel' => 'search',
    'dateModel' => 'fecha_creacion_filter',
    'dateLabel' => 'Fecha',
    'borderColor' => 'blue',
    'showSucursalFilter' => false,
    'sucursales' => [],
    'sucursalModel' => 'filterSucursalDest',
])

@php
    $bgColorClasses = [
        'blue' => 'bg-blue-50/30 dark:bg-blue-900/20',
        'green' => 'bg-green-50/30 dark:bg-green-900/20',
        'red' => 'bg-red-50/30 dark:bg-red-900/20',
        'orange' => 'bg-orange-50/30 dark:bg-orange-900/20',
        'yellow' => 'bg-yellow-50/30 dark:bg-yellow-900/20',
    ];
    $focusBorderClasses = [
        'blue' => 'focus:border-blue-400 dark:focus:border-blue-500',
        'green' => 'focus:border-green-400 dark:focus:border-green-500',
        'red' => 'focus:border-red-400 dark:focus:border-red-500',
        'orange' => 'focus:border-orange-400 dark:focus:border-orange-500',
        'yellow' => 'focus:border-yellow-400 dark:focus:border-yellow-500',
    ];
    $bgColor = $bgColorClasses[$borderColor] ?? 'bg-blue-50/30 dark:bg-blue-900/20';
    $focusBorder = $focusBorderClasses[$borderColor] ?? 'focus:border-blue-400 dark:focus:border-blue-500';
@endphp

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 border-b border-gray-100 dark:border-zinc-700 {{ $bgColor }}">
    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                <flux:icon name="magnifying-glass" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                Buscar
            </label>
            <flux:input 
                type="search" 
                wire:model.live="{{ $searchModel }}"
                placeholder="{{ $searchPlaceholder }}" 
                size="xs"
                class="w-full sm:w-64 max-w-full {{ $focusBorder }} transition" />
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                <flux:icon name="calendar" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                {{ $dateLabel }}
            </label>
            <flux:input 
                type="date" 
                wire:model.live="{{ $dateModel }}" 
                size="xs"
                class="w-full sm:w-36 max-w-full {{ $focusBorder }} transition" />
        </div>
        @if($showSucursalFilter && count($sucursales) > 0)
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <label class="text-xs text-gray-600 dark:text-zinc-400 font-medium hidden sm:block">
                    <flux:icon name="building-office" class="w-4 h-4 text-gray-400 dark:text-zinc-500 mr-1" />
                    Destino
                </label>
                <flux:select wire:model.live="{{ $sucursalModel }}" size="xs" class="w-48">
                    <option value="">Todas las sucursales</option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">{{ $sucursal->name }}</option>
                    @endforeach
                </flux:select>
            </div>
        @endif
    </div>
</div>

