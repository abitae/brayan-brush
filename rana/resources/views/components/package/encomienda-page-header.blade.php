@props([
    'title' => 'Encomiendas',
    'description' => 'Gestiona encomiendas',
    'icon' => 'cube',
    'iconColor' => 'blue',
    'actionLabel' => null,
    'actionMethod' => null,
    'actionIcon' => null,
    'actionDisabled' => false,
    'actionCount' => null,
])

@php
    $iconBgClasses = [
        'blue' => 'bg-blue-50 dark:bg-blue-900/20',
        'green' => 'bg-green-50 dark:bg-green-900/20',
        'red' => 'bg-red-50 dark:bg-red-900/20',
        'orange' => 'bg-orange-50 dark:bg-orange-900/20',
        'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20',
    ];
    $iconTextClasses = [
        'blue' => 'text-blue-600 dark:text-blue-400',
        'green' => 'text-green-600 dark:text-green-400',
        'red' => 'text-red-600 dark:text-red-400',
        'orange' => 'text-orange-600 dark:text-orange-400',
        'yellow' => 'text-yellow-600 dark:text-yellow-400',
    ];
    $iconBg = $iconBgClasses[$iconColor] ?? 'bg-blue-50 dark:bg-blue-900/20';
    $iconText = $iconTextClasses[$iconColor] ?? 'text-blue-600 dark:text-blue-400';
@endphp

<div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
    <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 {{ $iconBg }} rounded-lg">
                    <flux:icon name="{{ $icon }}" class="w-4 h-4 {{ $iconText }}" />
                </div>
                <div>
                    <h1 class="text-base font-semibold text-zinc-900 dark:text-white">{{ $title }}</h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
                </div>
            </div>
            @if($actionLabel)
                <flux:button 
                    wire:click="{{ $actionMethod }}" 
                    icon="{{ $actionIcon }}" 
                    variant="primary" 
                    size="xs"
                    :disabled="$actionDisabled">
                    {{ $actionLabel }}@if($actionCount && $actionCount > 0) ({{ $actionCount }})@endif
                </flux:button>
            @endif
        </div>
    </div>
</div>

