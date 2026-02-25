@props([
    'date' => null,
    'format' => 'd/m/Y H:i',
    'fallback' => 'N/A',
])

@if($date)
    {{ \Carbon\Carbon::parse($date)->format($format) }}
@else
    {{ $fallback }}
@endif

