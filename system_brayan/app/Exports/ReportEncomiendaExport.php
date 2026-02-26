<?php

namespace App\Exports;

use App\Models\Package\Encomienda;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportEncomiendaExport implements FromView, WithColumnWidths, WithStyles
{
    use Exportable;
    
    public $encomiendas;
    
    public function __construct($encomiendas)
    {
        $this->encomiendas = $encomiendas;
    }
    
    public function view(): View
    {
        return view('report.excel.reporte-encomienda', [
            'encomiendas' => $this->encomiendas,
        ]);
    }
    
    public function title(): string
    {
        return 'Reporte de Encomiendas';
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // CÓDIGO
            'B' => 20,  // GUÍA CLIENTE
            'C' => 30,  // REMITENTE
            'D' => 30,  // DESTINATARIO
            'E' => 15,  // TELÉFONO
            'F' => 10,  // CANTIDAD
            'G' => 40,  // PAQUETES
            'H' => 15,  // MONTO
            'I' => 15,  // DESCUENTO
            'J' => 15,  // MÉTODO DE PAGO
            'K' => 15,  // TIPO DE PAGO
            'L' => 15,  // TIPO COMPROBANTE
            'M' => 15,  // ESTADO
            'N' => 10,  // RETORNO
            'O' => 10,  // DOMICILIO
            'P' => 18,  // FECHA REGISTRO
            'Q' => 18, // FECHA ENTREGA
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => 'center']],
            2 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F3F4F6']]],
        ];
    }
}

