<?php

namespace App\Livewire\Report;

use App\Exports\ReportContableExport;
use App\Models\Configuration\Sucursal;
use App\Models\Facturacion\Invoice;
use App\Models\Facturacion\Note;
use App\Traits\UtilsTrait;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

class ContableReport extends Component
{
    use Toast, UtilsTrait, WithPagination, WithoutUrlPagination;

    private const DEFAULT_PER_PAGE = 15;

    private const TIPOS_DOCUMENTO = [
        ['id' => '', 'name' => 'Todos'],
        ['id' => '01', 'name' => 'Factura'],
        ['id' => '03', 'name' => 'Boleta'],
        ['id' => '07', 'name' => 'Nota de crédito'],
        ['id' => '08', 'name' => 'Nota de débito'],
    ];

    private const ESTADOS_SUNAT = [
        ['id' => '', 'name' => 'Todos'],
        ['id' => 'aceptado', 'name' => 'Aceptado SUNAT'],
        ['id' => 'pendiente', 'name' => 'Pendiente envío'],
        ['id' => 'error', 'name' => 'Con error'],
    ];

    public string $title = 'REPORTE CONTABLE';

    public string $sub_title = 'Registro de comprobantes electrónicos para contabilidad';

    public ?int $filtroSucursal = null;

    public ?string $filtroFechaInicio = null;

    public ?string $filtroFechaFin = null;

    public ?string $search = null;

    public ?string $filtroTipoDoc = null;

    public ?string $filtroEstadoSunat = null;

    public int $perPage = self::DEFAULT_PER_PAGE;

    public bool $soloSucursalUsuario = false;

    public float $totalBase = 0;

    public float $totalIgv = 0;

    public float $totalVentas = 0;

    public int $totalDocumentos = 0;

    /** @var array<int, array<string, mixed>> */
    public array $exportRows = [];

    public function mount(): void
    {
        $this->filtroFechaInicio = $this->filterDateStartOfMonth();
        $this->filtroFechaFin = $this->filterDateEnd();

        $user = Auth::user();

        if ($user && ! $this->canViewAllSucursales()) {
            $this->filtroSucursal = $user->sucursal_id;
            $this->soloSucursalUsuario = true;
        }
    }

    public function render()
    {
        $registros = $this->getRegistrosCollection();
        $this->exportRows = $registros->values()->all();
        $this->calculateTotals($registros);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = $registros->slice(($currentPage - 1) * $this->perPage, $this->perPage)->values();

        $comprobantes = new LengthAwarePaginator(
            $items,
            $registros->count(),
            $this->perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return view('livewire.report.contable-report', [
            'comprobantes' => $comprobantes,
            'sucursals' => $this->getSucursales(),
            'tiposDocumento' => self::TIPOS_DOCUMENTO,
            'estadosSunat' => self::ESTADOS_SUNAT,
        ]);
    }

    public function excelGenerate()
    {
        if (empty($this->exportRows)) {
            $this->warning('No hay datos para exportar');

            return null;
        }

        $filename = 'reporte_contable_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $this->success('Reporte contable generado con éxito');

        return Excel::download(
            new ReportContableExport($this->exportRows, $this->totalBase, $this->totalIgv, $this->totalVentas),
            $filename
        );
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroFechaInicio(): void
    {
        $this->ensureDateRangeOrder($this->filtroFechaInicio, $this->filtroFechaFin);
    }

    public function updatedFiltroFechaFin(): void
    {
        $this->ensureDateRangeOrder($this->filtroFechaInicio, $this->filtroFechaFin);
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'filtroSucursal',
            'filtroFechaInicio',
            'filtroFechaFin',
            'filtroTipoDoc',
            'filtroEstadoSunat',
            'search',
        ], true)) {
            $this->resetPage();
        }
    }

    private function canViewAllSucursales(): bool
    {
        $user = Auth::user();

        return $user?->hasRole(['SuperAdmin', 'Administrador']) ?? false;
    }

    private function getSucursales(): Collection
    {
        return Sucursal::where('isActive', true)->get();
    }

    private function getRegistrosCollection(): Collection
    {
        $registros = collect();

        if (! $this->filtroTipoDoc || in_array($this->filtroTipoDoc, ['01', '03'], true)) {
            $registros = $registros->concat(
                $this->mapInvoices($this->getInvoicesQuery()->with(['client', 'sucursal', 'encomienda'])->get())
            );
        }

        if (! $this->filtroTipoDoc || in_array($this->filtroTipoDoc, ['07', '08'], true)) {
            $registros = $registros->concat(
                $this->mapNotes($this->getNotesQuery()->with(['client', 'sucursal'])->get())
            );
        }

        return $registros
            ->sortByDesc(fn (array $row) => $row['fecha_emision_raw'])
            ->values();
    }

    private function getInvoicesQuery()
    {
        $query = Invoice::query();

        $this->applyCommonFilters($query, 'fechaEmision');

        if ($this->filtroTipoDoc && in_array($this->filtroTipoDoc, ['01', '03'], true)) {
            $query->where('tipoDoc', $this->filtroTipoDoc);
        } elseif ($this->filtroTipoDoc && in_array($this->filtroTipoDoc, ['07', '08'], true)) {
            $query->whereRaw('1 = 0');
        }

        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('serie', 'like', $term)
                    ->orWhere('correlativo', 'like', $term)
                    ->orWhereHas('client', fn ($client) => $client
                        ->where('name', 'like', $term)
                        ->orWhere('code', 'like', $term));
            });
        }

        return $query;
    }

    private function getNotesQuery()
    {
        $query = Note::query();

        $this->applyCommonFilters($query, 'fechaEmision');

        if ($this->filtroTipoDoc && in_array($this->filtroTipoDoc, ['07', '08'], true)) {
            $query->where('tipoDoc', $this->filtroTipoDoc);
        } elseif ($this->filtroTipoDoc && in_array($this->filtroTipoDoc, ['01', '03'], true)) {
            $query->whereRaw('1 = 0');
        }

        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('serie', 'like', $term)
                    ->orWhere('correlativo', 'like', $term)
                    ->orWhere('numDocfectado', 'like', $term)
                    ->orWhereHas('client', fn ($client) => $client
                        ->where('name', 'like', $term)
                        ->orWhere('code', 'like', $term));
            });
        }

        return $query;
    }

    private function applyCommonFilters($query, string $dateColumn): void
    {
        if ($this->filtroSucursal) {
            $query->where('sucursal_id', $this->filtroSucursal);
        }

        if ($this->filtroFechaInicio && $this->filtroFechaFin) {
            $query->whereBetween($dateColumn, [
                $this->parseFilterDateStart($this->filtroFechaInicio),
                $this->parseFilterDateEnd($this->filtroFechaFin),
            ]);
        }

        match ($this->filtroEstadoSunat) {
            'aceptado' => $query->where('cdr_code', '0'),
            'pendiente' => $query->whereNull('cdr_code')->whereNull('errorCode'),
            'error' => $query->whereNotNull('errorCode'),
            default => null,
        };
    }

    private function mapInvoices(Collection $invoices): Collection
    {
        return $invoices->map(fn (Invoice $invoice) => [
            'id' => $invoice->id,
            'origen' => 'invoice',
            'tipo_doc' => $invoice->tipoDoc,
            'tipo_label' => $this->tipoDocumentoLabel($invoice->tipoDoc),
            'numero' => $invoice->serie . '-' . str_pad((string) $invoice->correlativo, 8, '0', STR_PAD_LEFT),
            'fecha_emision' => Carbon::parse($invoice->fechaEmision)->format('d/m/Y'),
            'fecha_emision_raw' => Carbon::parse($invoice->fechaEmision)->format('Y-m-d H:i:s'),
            'cliente_doc' => $invoice->client?->code,
            'cliente_nombre' => $invoice->client?->name,
            'encomienda_code' => $invoice->encomienda?->code,
            'sucursal' => $invoice->sucursal?->code,
            'forma_pago' => $invoice->formaPago_tipo,
            'base' => (float) $invoice->mtoOperGravadas,
            'igv' => (float) $invoice->mtoIGV,
            'total' => (float) $invoice->mtoImpVenta,
            'factor' => 1,
            'estado_sunat' => $this->estadoSunatLabel($invoice->cdr_code, $invoice->errorCode),
            'cdr_code' => $invoice->cdr_code,
            'pdf_url' => '/invoice/a4/' . $invoice->id,
        ]);
    }

    private function mapNotes(Collection $notes): Collection
    {
        return $notes->map(fn (Note $note) => [
            'id' => $note->id,
            'origen' => 'note',
            'tipo_doc' => $note->tipoDoc,
            'tipo_label' => $this->tipoDocumentoLabel($note->tipoDoc),
            'numero' => $note->serie . '-' . str_pad((string) $note->correlativo, 8, '0', STR_PAD_LEFT),
            'fecha_emision' => Carbon::parse($note->fechaEmision)->format('d/m/Y'),
            'fecha_emision_raw' => Carbon::parse($note->fechaEmision)->format('Y-m-d H:i:s'),
            'cliente_doc' => $note->client?->code,
            'cliente_nombre' => $note->client?->name,
            'encomienda_code' => null,
            'sucursal' => $note->sucursal?->code,
            'forma_pago' => $note->tipoDocAfectado ? 'Doc. ' . $note->numDocfectado : '-',
            'base' => (float) $note->mtoOperGravadas,
            'igv' => (float) $note->mtoIGV,
            'total' => (float) $note->mtoImpVenta,
            'factor' => $note->tipoDoc === '07' ? -1 : 1,
            'estado_sunat' => $this->estadoSunatLabel($note->cdr_code, $note->errorCode),
            'cdr_code' => $note->cdr_code,
            'pdf_url' => '/note/a4/' . $note->id,
        ]);
    }

    private function calculateTotals(Collection $registros): void
    {
        $this->totalDocumentos = $registros->count();
        $this->totalBase = round($registros->sum(fn (array $row) => $row['base'] * $row['factor']), 2);
        $this->totalIgv = round($registros->sum(fn (array $row) => $row['igv'] * $row['factor']), 2);
        $this->totalVentas = round($registros->sum(fn (array $row) => $row['total'] * $row['factor']), 2);
    }

    private function tipoDocumentoLabel(string $tipoDoc): string
    {
        return match ($tipoDoc) {
            '01' => 'Factura',
            '03' => 'Boleta',
            '07' => 'Nota crédito',
            '08' => 'Nota débito',
            default => $tipoDoc,
        };
    }

    private function estadoSunatLabel(?string $cdrCode, ?string $errorCode): string
    {
        if ($errorCode) {
            return 'Error';
        }

        if ($cdrCode === '0') {
            return 'Aceptado';
        }

        if ($cdrCode) {
            return 'Observado';
        }

        return 'Pendiente';
    }
}
