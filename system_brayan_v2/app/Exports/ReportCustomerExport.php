<?php

namespace App\Exports;

use App\Models\Package\Customer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportCustomerExport implements FromView, WithColumnWidths, WithStyles
{
    use Exportable;
    
    public $customers;
    
    public function __construct($customers)
    {
        $this->customers = $customers;
    }
    
    public function view(): View
    {
        return view('report.excel.reporte-customer', [
            'customers' => $this->customers,
        ]);
    }
    
    public function title(): string
    {
        return 'Reporte de Clientes';
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 15,
            'D' => 40,
            'E' => 40,
            'F' => 20,
            'G' => 15,
            'H' => 15,
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']],
            2 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F3F4F6']]],
        ];
    }
}
