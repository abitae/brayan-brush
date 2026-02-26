@props([
    'encomienda',
])

<td class="px-2 py-1 text-gray-700 dark:text-zinc-300 truncate max-w-[180px]">
    <div>
        <span class="font-semibold text-blue-700 dark:text-blue-400">Remitente:</span>
        <span>{{ $encomienda->remitente->name ?? '-' }}</span>
    </div>
    <div>
        <span class="font-semibold text-yellow-700 dark:text-yellow-400">Destinatario:</span>
        <span>{{ $encomienda->destinatario->name ?? '-' }}</span>
    </div>
</td>

