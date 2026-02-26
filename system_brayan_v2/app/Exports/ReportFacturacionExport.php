<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportFacturacionExport implements FromView, WithColumnWidths, WithStyles
{
    use Exportable;
    
    public $invoices;
    public $notes;
    public $tickets;
    public $totalFacturas;
    public $totalNotas;
    public $totalTickets;
    public $totalGeneral;
    
    public function __construct($invoices, $notes, $tickets, $totalFacturas, $totalNotas, $totalTickets, $totalGeneral)
    {
        $this->invoices = $invoices;
        $this->notes = $notes;
        $this->tickets = $tickets;
        $this->totalFacturas = $totalFacturas;
        $this->totalNotas = $totalNotas;
        $this->totalTickets = $totalTickets;
        $this->totalGeneral = $totalGeneral;
    }
    
    public function view(): View
    {
        return view('report.excel.reporte-facturacion', [
            'invoices' => $this->invoices,
            'notes' => $this->notes,
            'tickets' => $this->tickets,
            'totalFacturas' => $this->totalFacturas,
            'totalNotas' => $this->totalNotas,
            'totalTickets' => $this->totalTickets,
            'totalGeneral' => $this->totalGeneral,
        ]);
    }
    
    public function title(): string
    {
        return 'Reporte de Facturación';
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 25,  // Documento
            'B' => 40,  // Cliente
            'C' => 18,  // Monto
            'D' => 15,  // Fecha
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']],
            2 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center']],
            3 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DBEAFE']]],
            4 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DCFCE7']]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FEE2E2']]],
            6 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F3E8FF']]],
        ];
    }
}
