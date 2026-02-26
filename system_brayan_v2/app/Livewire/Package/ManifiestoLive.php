<?php

namespace App\Livewire\Package;

use App\Exports\ManifiestoExport;
use App\Models\Package\Manifiesto;
use App\Traits\LogCustom;
use App\Traits\ToastTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ManifiestoLive extends Component
{
    use LogCustom, ToastTrait, WithPagination, WithoutUrlPagination;

    public string $title = 'MANIFIESTOS';
    public string $sub_title = 'Historial de Manifiestos';
    public string $search = '';
    public int $perPage = 10;

    public function render()
    {
        $manifiestos = Manifiesto::where('sucursal_destino_id', Auth::user()->sucursal->id)
            ->orWhere('sucursal_id', Auth::user()->sucursal->id)
            ->with(['sucursal', 'destino'])
            ->when($this->search, function ($query) {
                return $query->where('ids', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);
            
        return view('livewire.package.manifiesto-live', compact('manifiestos'));
    }

    public function excelGenerate(Manifiesto $manifiesto)
    {
        try {
            $this->success('Generando Excel');
            return Excel::download(new ManifiestoExport(json_decode($manifiesto->ids)), 'manifiesto-' . $manifiesto->id . '.xlsx');
        } catch (\Exception $e) {
            $this->error('Error al generar Excel: ' . $e->getMessage());
        }
    }
}

