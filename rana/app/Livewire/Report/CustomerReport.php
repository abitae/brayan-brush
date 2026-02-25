<?php

namespace App\Livewire\Report;

use App\Exports\ReportCustomerExport;
use App\Models\Package\Customer;
use App\Traits\ToastTrait;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class CustomerReport extends Component
{
    use ToastTrait, WithPagination, WithoutUrlPagination;

    public string $title = 'REPORTE DE CLIENTES';
    public string $sub_title = 'Módulo de reporte de clientes';
    public int $perPage = 20;

    public $search;
    public $tipoDocumento = 'Todos';
    public $tiposDocumento = [
        ['id' => 'Todos', 'name' => 'Todos'],
        ['id' => '1', 'name' => 'DNI'],
        ['id' => '6', 'name' => 'RUC'],
    ];

    public function render()
    {
        $customers = Customer::query()
            ->withCount(['encomiendas_remitente', 'encomiendas_destinatario'])
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->tipoDocumento !== 'Todos', function ($query) {
                return $query->where('type_code', $this->tipoDocumento);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.report.customer-report', compact('customers'));
    }

    public function exportExcel()
    {
        try {
            $customers = Customer::query()
                ->withCount(['encomiendas_remitente', 'encomiendas_destinatario'])
                ->when($this->search, function ($query) {
                    return $query->where(function ($q) {
                        $q->where('code', 'like', '%' . $this->search . '%')
                            ->orWhere('name', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->tipoDocumento !== 'Todos', function ($query) {
                    return $query->where('type_code', $this->tipoDocumento);
                })
                ->latest()
                ->get();

            return Excel::download(new ReportCustomerExport($customers), 'reporte-clientes-' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            $this->error('Error al exportar: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->tipoDocumento = 'Todos';
    }
}

