@props([
    'encomienda',
    'showPin' => false,
    'showEstado' => false,
    'showPago' => false,
    'showComprobante' => false,
])

<div class="bg-gray-50 dark:bg-zinc-800 p-3 rounded-lg space-y-2 text-xs text-gray-900 dark:text-zinc-100">
    <div><strong>Remitente:</strong> {{ $encomienda->remitente->name ?? 'N/A' }}</div>
    <div><strong>Destinatario:</strong> {{ $encomienda->destinatario->name ?? 'N/A' }}</div>
    <div><strong>Monto:</strong> S/ {{ number_format($encomienda->monto ?? 0, 2) }}</div>
    
    @if($showPin && isset($encomienda->pin))
        <div><strong>PIN:</strong> {{ $encomienda->pin }}</div>
    @endif
    @if($showEstado && isset($encomienda->estado_encomienda))
        <div><strong>Estado:</strong> {{ $encomienda->estado_encomienda }}</div>
    @endif
    @if($showPago && isset($encomienda->estado_pago))
        <div><strong>Pago:</strong> {{ $encomienda->estado_pago }}</div>
    @endif
    @if($showComprobante && isset($encomienda->tipo_comprobante))
        <div><strong>Comprobante:</strong> {{ $encomienda->tipo_comprobante }}</div>
    @endif
    @if(isset($encomienda->isHome) && $encomienda->isHome)
        <div class="text-blue-600 dark:text-blue-400"><strong>Entrega a domicilio</strong></div>
    @endif
    {{ $slot }}
</div>

