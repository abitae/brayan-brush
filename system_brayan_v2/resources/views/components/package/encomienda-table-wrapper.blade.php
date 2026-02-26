@props([
    'borderColor' => 'blue',
])

<div class="bg-white dark:bg-zinc-900 rounded-lg shadow border border-gray-100 dark:border-zinc-700 p-2">
    @if(isset($filters))
        {{ $filters }}
    @endif
    
    <div class="overflow-x-auto">
        {{ $slot }}
    </div>

    @if(isset($pagination))
        <div class="p-4 border-t border-gray-100 dark:border-zinc-700">
            {{ $pagination }}
        </div>
    @endif
</div>

