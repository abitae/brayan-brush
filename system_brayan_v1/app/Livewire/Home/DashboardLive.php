<?php

namespace App\Livewire\Home;

use App\Models\Package\Encomienda;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardLive extends Component
{
    public string $title = 'DASHBOARD';

    public string $sub_title = 'Estadística';

    public array $myLine = [
        'type' => 'line',
        'data' => [],
        'options' => [],
    ];

    public array $myPie = [
        'type' => 'doughnut',
        'data' => [],
        'options' => [],
    ];

    public array $myBar = [
        'type' => 'bar',
        'data' => [],
        'options' => [],
    ];

    public array $myBarTipoCobro = [
        'type' => 'bar',
        'data' => [],
        'options' => [],
    ];

    public string $selectedTipe = 'Y';

    public $date_ini;

    public $date_end;

    public ?string $sucursalNombre = null;

    public int $statTotal = 0;

    public float $statMonto = 0;

    public int $statEntregadas = 0;

    public int $statEnProceso = 0;

    private array $estadoColors = [
        'REGISTRADO' => 'rgba(54, 162, 235, 0.85)',
        'ENVIADO' => 'rgba(255, 99, 132, 0.85)',
        'RECIBIDO' => 'rgba(75, 192, 192, 0.85)',
        'RETORNADO' => 'rgba(255, 206, 86, 0.85)',
        'ENTREGADO' => 'rgba(153, 102, 255, 0.85)',
    ];

    private array $estadoLabels = [
        'REGISTRADO' => 'Registrado',
        'ENVIADO' => 'Enviado',
        'RECIBIDO' => 'Recibido',
        'RETORNADO' => 'Retornado',
        'ENTREGADO' => 'Entregado',
    ];

    private array $paymentTypeColors = [
        'Contado' => 'rgba(54, 162, 235, 0.85)',
        'Credito' => 'rgba(255, 99, 132, 0.85)',
    ];

    private array $metodoPagoColors = [
        'Efectivo' => 'rgba(54, 162, 235, 0.85)',
        'Yape' => 'rgba(75, 192, 192, 0.85)',
        'Transferencia' => 'rgba(255, 206, 86, 0.85)',
        'Deposito' => 'rgba(153, 102, 255, 0.85)',
    ];

    public function mount(): void
    {
        $this->date_ini = Carbon::now()->startOfDay()->format('Y-m-d H:i');
        $this->date_end = Carbon::now()->endOfDay()->format('Y-m-d H:i');
        $this->sucursalNombre = Auth::user()?->sucursal?->code;
        $this->sub_title = $this->sucursalNombre
            ? "Estadísticas — {$this->sucursalNombre}"
            : 'Estadísticas — sin sucursal asignada';
    }

    private function sucursalId(): ?int
    {
        return Auth::user()?->sucursal_id;
    }

    private function encomiendaQuery(): Builder
    {
        $query = Encomienda::query();

        if ($sucursalId = $this->sucursalId()) {
            $query->where('sucursal_id', $sucursalId);
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query;
    }

    private function emptyChart(array $labels = []): array
    {
        return [
            'labels' => $labels,
            'datasets' => [],
        ];
    }

    private function baseOptions(array $overrides = []): array
    {
        return array_replace_recursive([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 14,
                        'usePointStyle' => true,
                        'boxWidth' => 10,
                    ],
                ],
            ],
        ], $overrides);
    }

    private function currencyScaleOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0,0,0,0.06)'],
                    'ticks' => ['padding' => 8],
                ],
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['maxRotation' => 45, 'minRotation' => 0],
                ],
            ],
        ];
    }

    public function render()
    {
        $dateObj = new DateTime($this->date_ini);
        $dateEnd = new DateTime($this->date_end);

        $this->loadSummaryStats($dateObj, $dateEnd);

        $chartData = $this->getDataForPeriod($dateObj, 'chart');
        $pieData = $this->getDataForPeriod($dateObj, 'pie');
        $barData = $this->getDataForPeriod($dateObj, 'bar');
        $dataTipoCobro = $this->dataTipoCobro($dateObj);

        Arr::set($this->myLine, 'data', $chartData);
        Arr::set($this->myLine, 'options', $this->baseOptions(array_replace_recursive(
            $this->currencyScaleOptions(),
            [
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => [
                        'display' => true,
                        'text' => 'Evolución del monto recaudado (S/.)',
                        'padding' => ['bottom' => 12],
                    ],
                ],
            ]
        )));

        Arr::set($this->myPie, 'data', $pieData);
        Arr::set($this->myPie, 'options', $this->baseOptions([
            'cutout' => '55%',
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Distribución Contado vs Crédito',
                    'padding' => ['bottom' => 12],
                ],
            ],
        ]));

        Arr::set($this->myBar, 'data', $barData);
        Arr::set($this->myBar, 'options', $this->baseOptions([
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => ['display' => false],
                'title' => [
                    'display' => true,
                    'text' => 'Cantidad por estado operativo',
                    'padding' => ['bottom' => 12],
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0,0,0,0.06)'],
                    'ticks' => ['stepSize' => 1, 'precision' => 0],
                ],
                'y' => ['grid' => ['display' => false]],
            ],
        ]));

        Arr::set($this->myBarTipoCobro, 'data', $dataTipoCobro);
        Arr::set($this->myBarTipoCobro, 'options', $this->baseOptions(array_replace_recursive(
            $this->currencyScaleOptions(),
            [
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => [
                        'display' => true,
                        'text' => 'Recaudación por método de pago (S/.)',
                        'padding' => ['bottom' => 12],
                    ],
                ],
            ]
        )));

        return view('livewire.home.dashboard-live');
    }

    private function loadSummaryStats(DateTime $date, DateTime $dateEnd): void
    {
        if (! $this->sucursalId()) {
            $this->statTotal = 0;
            $this->statMonto = 0;
            $this->statEntregadas = 0;
            $this->statEnProceso = 0;

            return;
        }

        $stats = $this->encomiendaQuery()
            ->whereBetween('created_at', [$date, $dateEnd])
            ->selectRaw('
                COUNT(*) as total,
                COALESCE(SUM(monto), 0) as monto,
                SUM(CASE WHEN estado_encomienda = ? THEN 1 ELSE 0 END) as entregadas,
                SUM(CASE WHEN estado_encomienda NOT IN (?, ?) THEN 1 ELSE 0 END) as en_proceso
            ', ['ENTREGADO', 'ENTREGADO', 'RETORNADO'])
            ->first();

        $this->statTotal = (int) ($stats->total ?? 0);
        $this->statMonto = (float) ($stats->monto ?? 0);
        $this->statEntregadas = (int) ($stats->entregadas ?? 0);
        $this->statEnProceso = (int) ($stats->en_proceso ?? 0);
    }

    private function getDataForPeriod(DateTime $date, string $chartType): array
    {
        $dateEnd = new DateTime($this->date_end);

        $methodName = match ($this->selectedTipe) {
            'Y' => "data{$chartType}Year",
            'd' => "data{$chartType}Day",
            default => "data{$chartType}Month",
        };

        return $this->$methodName($date, $dateEnd);
    }

    private function dataChartYear(DateTime $date, DateTime $dateEnd): array
    {
        return $this->getChartData($date, $dateEnd, 'year');
    }

    private function dataChartMonth(DateTime $date, DateTime $dateEnd): array
    {
        return $this->getChartData($date, $dateEnd, 'month');
    }

    private function dataChartDay(DateTime $date, DateTime $dateEnd): array
    {
        return $this->getChartData($date, $dateEnd, 'day');
    }

    private function getChartData(DateTime $date, DateTime $dateEnd, string $timeUnit = 'month'): array
    {
        $timeConfigs = [
            'year' => [
                'size' => 12,
                'start' => 1,
                'format' => 'MONTH',
                'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            ],
            'month' => [
                'size' => (int) $date->format('t'),
                'start' => 1,
                'format' => 'DAY',
                'where' => ['whereMonth' => $date->format('m'), 'whereYear' => $date->format('Y')],
            ],
            'day' => [
                'size' => 24,
                'start' => 0,
                'format' => 'HOUR',
                'where' => [
                    'whereMonth' => $date->format('m'),
                    'whereDay' => $date->format('d'),
                    'whereYear' => $date->format('Y'),
                ],
            ],
        ];

        $config = $timeConfigs[$timeUnit];

        if (! $this->sucursalId()) {
            return $this->emptyChart($this->periodLabels($timeUnit, $config));
        }

        $query = $this->encomiendaQuery()->whereBetween('created_at', [$date, $dateEnd]);

        if (isset($config['where'])) {
            foreach ($config['where'] as $method => $value) {
                $query->$method('created_at', $value);
            }
        }

        $records = $query->selectRaw(
            $config['format'] . '(created_at) as period, SUM(monto) as total_amount'
        )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $periodData = array_fill($config['start'], $config['size'], 0);

        foreach ($records as $record) {
            $periodData[(int) $record->period] = (float) $record->total_amount;
        }

        return [
            'labels' => $this->periodLabels($timeUnit, $config),
            'datasets' => [[
                'label' => 'Recaudado (S/.)',
                'data' => array_values($periodData),
                'borderColor' => 'rgb(59, 130, 246)',
                'backgroundColor' => 'rgba(59, 130, 246, 0.12)',
                'borderWidth' => 2,
                'fill' => true,
                'tension' => 0.35,
                'pointRadius' => 3,
                'pointHoverRadius' => 6,
                'pointBackgroundColor' => 'rgb(59, 130, 246)',
            ]],
        ];
    }

    private function periodLabels(string $timeUnit, array $config): array
    {
        if ($timeUnit === 'year') {
            return $config['labels'];
        }

        if ($timeUnit === 'day') {
            return array_map(fn (int $hour) => sprintf('%02d:00', $hour), range(0, 23));
        }

        return array_map(fn (int $day) => (string) $day, range(1, $config['size']));
    }

    private function dataPieYear(DateTime $date, DateTime $dateEnd): array
    {
        return $this->getPaymentTypeData($date, $dateEnd);
    }

    private function dataPieMonth(DateTime $date, DateTime $dateEnd): array
    {
        return $this->getPaymentTypeData($date, $dateEnd);
    }

    private function dataPieDay(DateTime $date, DateTime $dateEnd): array
    {
        return $this->getPaymentTypeData($date, $dateEnd);
    }

    private function getPaymentTypeData(DateTime $date, DateTime $dateEnd): array
    {
        $paymentTypes = ['Contado', 'Credito'];
        $labels = ['Contado', 'Crédito'];

        if (! $this->sucursalId()) {
            return $this->emptyChart($labels);
        }

        $data = $this->encomiendaQuery()
            ->whereBetween('created_at', [$date, $dateEnd])
            ->whereIn('tipo_pago', $paymentTypes)
            ->selectRaw('tipo_pago, SUM(monto) as total_amount')
            ->groupBy('tipo_pago')
            ->get()
            ->keyBy('tipo_pago');

        $values = array_map(
            fn (string $paymentType) => (float) ($data[$paymentType]->total_amount ?? 0),
            $paymentTypes
        );

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Monto (S/.)',
                'data' => $values,
                'backgroundColor' => array_values($this->paymentTypeColors),
                'borderColor' => '#ffffff',
                'borderWidth' => 2,
                'hoverOffset' => 8,
            ]],
        ];
    }

    private function dataBarYear(DateTime $date, DateTime $dateEnd): array
    {
        return $this->getBarData($date, $dateEnd);
    }

    private function dataBarMonth(DateTime $date, DateTime $dateEnd): array
    {
        return $this->getBarData($date, $dateEnd);
    }

    private function dataBarDay(DateTime $date, DateTime $dateEnd): array
    {
        return $this->getBarData($date, $dateEnd);
    }

    private function getBarData(DateTime $date, DateTime $dateEnd): array
    {
        $estados = array_keys($this->estadoColors);
        $labels = array_values($this->estadoLabels);

        if (! $this->sucursalId()) {
            return $this->emptyChart($labels);
        }

        $data = $this->encomiendaQuery()
            ->whereBetween('created_at', [$date, $dateEnd])
            ->selectRaw('estado_encomienda, COUNT(*) as total')
            ->groupBy('estado_encomienda')
            ->get()
            ->keyBy('estado_encomienda');

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Encomiendas',
                'data' => array_map(
                    fn (string $estado) => (int) ($data[$estado]->total ?? 0),
                    $estados
                ),
                'backgroundColor' => array_values($this->estadoColors),
                'borderColor' => array_values($this->estadoColors),
                'borderRadius' => 6,
                'borderWidth' => 0,
                'barThickness' => 18,
            ]],
        ];
    }

    private function dataTipoCobro(DateTime $date): array
    {
        $dateEnd = new DateTime($this->date_end);
        $metodoPagos = array_keys($this->metodoPagoColors);

        if (! $this->sucursalId()) {
            return $this->emptyChart($metodoPagos);
        }

        $data = $this->encomiendaQuery()
            ->whereBetween('created_at', [$date, $dateEnd])
            ->whereIn('metodo_pago', $metodoPagos)
            ->select('metodo_pago', DB::raw('SUM(monto) as total'))
            ->groupBy('metodo_pago')
            ->get()
            ->keyBy('metodo_pago');

        return [
            'labels' => $metodoPagos,
            'datasets' => [[
                'label' => 'Monto (S/.)',
                'data' => array_map(
                    fn (string $metodoPago) => (float) ($data[$metodoPago]->total ?? 0),
                    $metodoPagos
                ),
                'backgroundColor' => array_values($this->metodoPagoColors),
                'borderColor' => array_values($this->metodoPagoColors),
                'borderRadius' => 6,
                'borderWidth' => 0,
            ]],
        ];
    }
}
