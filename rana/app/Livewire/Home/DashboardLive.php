<?php

namespace App\Livewire\Home;

use App\Models\Configuration\Sucursal;
use App\Models\Package\Encomienda;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardLive extends Component
{
    public string $title = 'DASHBOARD';
    public string $sub_title = 'Estadísticas';

    public $selectedTipe = 'Y';
    public $date_ini;
    public $date_end;

    public function mount()
    {
        $this->date_ini = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->date_end = Carbon::now()->endOfDay()->format('Y-m-d');
    }

    public function render()
    {
        $sucursalId = Auth::user()->sucursal->id;
        
        // Estadísticas generales
        $totalEncomiendas = Encomienda::where('sucursal_id', $sucursalId)
            ->whereBetween('fecha_creacion', [
                Carbon::parse($this->date_ini)->startOfDay(),
                Carbon::parse($this->date_end)->endOfDay()
            ])
            ->count();

        $encomiendasRegistradas = Encomienda::where('sucursal_id', $sucursalId)
            ->where('estado_encomienda', 'REGISTRADO')
            ->whereBetween('fecha_creacion', [
                Carbon::parse($this->date_ini)->startOfDay(),
                Carbon::parse($this->date_end)->endOfDay()
            ])
            ->count();

        $encomiendasEnviadas = Encomienda::where('sucursal_id', $sucursalId)
            ->where('estado_encomienda', 'ENVIADO')
            ->whereBetween('fecha_creacion', [
                Carbon::parse($this->date_ini)->startOfDay(),
                Carbon::parse($this->date_end)->endOfDay()
            ])
            ->count();

        $encomiendasRecibidas = Encomienda::where('sucursal_dest_id', $sucursalId)
            ->where('estado_encomienda', 'RECIBIDO')
            ->whereBetween('fecha_recepcion', [
                Carbon::parse($this->date_ini)->startOfDay(),
                Carbon::parse($this->date_end)->endOfDay()
            ])
            ->count();

        $encomiendasEntregadas = Encomienda::where('sucursal_dest_id', $sucursalId)
            ->where('estado_encomienda', 'ENTREGADO')
            ->whereBetween('fecha_entrega', [
                Carbon::parse($this->date_ini)->startOfDay(),
                Carbon::parse($this->date_end)->endOfDay()
            ])
            ->count();

        $totalIngresos = Encomienda::where('sucursal_id', $sucursalId)
            ->where('estado_pago', 'PAGADO')
            ->whereBetween('fecha_creacion', [
                Carbon::parse($this->date_ini)->startOfDay(),
                Carbon::parse($this->date_end)->endOfDay()
            ])
            ->sum('monto');

        $pendientesCobro = Encomienda::where('sucursal_id', $sucursalId)
            ->where('estado_credito', 'Pendiente')
            ->sum('monto');

        // Estadísticas por estado
        $estadosData = Encomienda::where('sucursal_id', $sucursalId)
            ->whereBetween('fecha_creacion', [
                Carbon::parse($this->date_ini)->startOfDay(),
                Carbon::parse($this->date_end)->endOfDay()
            ])
            ->select('estado_encomienda', DB::raw('count(*) as total'))
            ->groupBy('estado_encomienda')
            ->get()
            ->pluck('total', 'estado_encomienda')
            ->toArray();

        // Estadísticas por método de pago
        $metodosPagoData = Encomienda::where('sucursal_id', $sucursalId)
            ->whereBetween('fecha_creacion', [
                Carbon::parse($this->date_ini)->startOfDay(),
                Carbon::parse($this->date_end)->endOfDay()
            ])
            ->whereNotNull('metodo_pago')
            ->select('metodo_pago', DB::raw('count(*) as total'))
            ->groupBy('metodo_pago')
            ->get()
            ->pluck('total', 'metodo_pago')
            ->toArray();

        return view('livewire.home.dashboard-live', compact(
            'totalEncomiendas',
            'encomiendasRegistradas',
            'encomiendasEnviadas',
            'encomiendasRecibidas',
            'encomiendasEntregadas',
            'totalIngresos',
            'pendientesCobro',
            'estadosData',
            'metodosPagoData'
        ));
    }
}

