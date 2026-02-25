@props([
    'amount' => 0,
    'color' => 'green',
])

@php
    $textColorClasses = [
        'blue' => 'text-blue-600 dark:text-blue-400',
        'green' => 'text-green-600 dark:text-green-400',
        'red' => 'text-red-600 dark:text-red-400',
        'orange' => 'text-orange-600 dark:text-orange-400',
        'yellow' => 'text-yellow-600 dark:text-yellow-400',
    ];
    $textColor = $textColorClasses[$color] ?? 'text-green-600 dark:text-green-400';
@endphp

<td class="px-2 py-1 font-semibold {{ $textColor }}">
    <x-package.encomienda-amount-formatter :amount="$amount" />
</td>

