<?php

namespace App\Exports;

use App\Models\Package\Encomienda;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ManifiestoExport implements FromView, WithColumnWidths, WithStyles
{
    use Exportable;
    
    public $ids;
    
    public function __construct($ids)
    {
        $this->ids = $ids;
    }
    
    public function view(): View
    {
        $encomiendas = Encomienda::whereIn('id', $this->ids)
            ->with([
                'remitente', 
                'destinatario', 
                'sucursal_remitente', 
                'sucursal_destinatario', 
                'paquetes',
                'transportista',
                'vehiculo',
                'ruta.transportista',
                'ruta.vehiculo'
            ])
            ->get();
        
        // Obtener la primera encomienda para datos del conductor y vehículo
        $encomienda = $encomiendas->first();
        
        // Separar encomiendas por tipo
        $encomiendasNormales = $encomiendas->where('isHome', false)->where('isReturn', false);
        $encomiendasIsHome = $encomiendas->where('isHome', true);
        $encomiendasIsReturn = $encomiendas->where('isReturn', true);
            
        return view('report.excel.manifiesto', [
            'encomienda' => $encomienda,
            'encomiendas' => $encomiendasNormales,
            'encomiendasIsHome' => $encomiendasIsHome,
            'encomiendasIsReturn' => $encomiendasIsReturn,
        ]);
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 30,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 15,
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}

