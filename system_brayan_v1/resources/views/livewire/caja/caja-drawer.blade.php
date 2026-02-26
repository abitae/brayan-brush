<x-mary-drawer wire:model="showHistory" class="w-11/12 lg:w-2/3" right>
    <div>
        @isset($cajas)
                @php
                    $row_decoration = [
                        'bg-yellow-500' => fn(App\Models\Caja\Caja $caja) => $caja->isActive,
                    ];
                @endphp
                <x-mary-table :headers="$headersHistory" :rows="$cajas" striped :row-decoration="$row_decoration"
                    with-pagination per-page="perPage" :per-page-values="[5, 20, 10, 50]">
                    @scope('cell_created_at', $stuff)
                    <x-mary-badge :value="$stuff->created_at->format('d/m/Y')" class="badge-info" />
                    @endscope
                    @scope('cell_updated_at', $stuff)
                    <x-mary-badge :value="$stuff->updated_at->format('d/m/Y')" class="badge-warning" />
                    @endscope
                    @scope('cell_ingresos', $stuff)
                    <div class="grid grid-cols-2 text-white">
                        <div>Efectivo: S/</div>
                        <div>{{$stuff->entries->whereIn('metodo_pago', ['Efectivo'])->sum('monto_entry')}}</div>
                        <div>Yape: S/</div>
                        <div>{{$stuff->entries->whereIn('metodo_pago', ['Yape'])->sum('monto_entry')}}</div>
                        <div>Transferencia: S/</div>
                        <div>{{$stuff->entries->whereIn('metodo_pago', ['Transferencia'])->sum('monto_entry')}}</div>
                        <div>Deposito: S/</div>
                        <div>{{$stuff->entries->whereIn('metodo_pago', ['Deposito'])->sum('monto_entry')}}</div>
                    </div>
                    @endscope
                    @scope('cell_egresos', $stuff)
                    <div class="grid grid-cols-2 text-white">
                        <div>Efectivo: S/</div>
                        <div>{{$stuff->exits->whereIn('metodo_pago', ['Efectivo'])->sum('monto_exit')}}</div>
                        <div>Yape: S/</div>
                        <div>{{$stuff->exits->whereIn('metodo_pago', ['Yape'])->sum('monto_exit')}}</div>
                        <div>Transferencia: S/</div>
                        <div>{{$stuff->exits->whereIn('metodo_pago', ['Transferencia'])->sum('monto_exit')}}</div>
                        <div>Deposito: S/</div>
                        <div>{{$stuff->exits->whereIn('metodo_pago', ['Deposito'])->sum('monto_exit')}}</div>
                    </div>
                    @endscope
                    @scope('cell_action', $stuff)
                    @if (!$stuff->isActive)
                    <x-mary-button icon="o-printer" target="_blank" no-wire-navigate
                            link="/caja/80mm/{{ $stuff->id }}" spinner class="text-white bg-purple-500 btn-xs" />
                    @endif
                    @endscope
                </x-mary-table>
        @else
            <p>No tiene historial.</p>
        @endisset
    </div>
</x-mary-drawer>