@props([
    'amount' => 0,
    'currency' => 'S/',
    'decimals' => 2,
])

{{ $currency }} {{ number_format($amount, $decimals) }}

