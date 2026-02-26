<?php

namespace App\Livewire\Home;

use App\Models\Configuration\Sucursal;
use App\Models\Package\Encomienda;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardLive extends Component
{
    public string $title = 'DASHBOARD';
    public string $sub_title = 'Estadistica';

    // Configuración de gráficos unificada con opciones comunes
    private array $chartDefaultOptions = [
        'responsive' => true,
        'plugins' => [
            'legend' => [
                'position' => 'bottom',
            ],
            'title' => [
                'display' => true,
                'text' => 'Chart.js Bar Chart'
            ]
        ]
    ];

    public array $myChart = ['type' => 'bar', 'data' => []];

    public array $myLine = [
        'type' => 'line',
        'data' => [],
        'option' => []
    ];

    public array $myPie = [
        'type' => 'bar',
        'data' => [],
        'option' => []
    ];

    public array $myBar = [
        'type' => 'bar',
        'data' => [],
        'option' => []
    ];

    public array $myBarTipoCobro = [
        'type' => 'bar',
        'data' => [],
        'option' => []
    ];

    public $selectedTipe = 'Y';
    public $date_ini;
    public $date_end;

    // Mapeo de estados y métodos de pago con colores consistentes
    private array $estadoColors = [
        'REGISTRADO' => 'rgba(54, 162, 235, 0.8)',
        'ENVIADO' => 'rgba(255, 99, 132, 0.8)',
        'RECIBIDO' => 'rgba(75, 192, 192, 0.8)',
        'RETORNADO' => 'rgba(255, 206, 86, 0.8)',
        'ENTREGADO' => 'rgba(153, 102, 255, 0.8)'
    ];

    private array $paymentTypeColors = [
        'Contado' => 'rgba(54, 162, 235, 0.8)',
        'Credito' => 'rgba(255, 99, 132, 0.8)'
    ];

    private array $metodoPagoColors = [
        'Efectivo' => 'rgba(54, 162, 235, 0.8)',
        'Yape' => 'rgba(75, 192, 192, 0.8)',
        'Transferencia' => 'rgba(255, 206, 86, 0.8)',
        'Deposito' => 'rgba(153, 102, 255, 0.8)'
    ];

    public function mount()
    {
        $this->date_ini = Carbon::now()->startOfDay()->format('Y-m-d H:i');
        $this->date_end = Carbon::now()->endOfDay()->format('Y-m-d H:i');
        // Inicializar opciones de gráficos
        $this->myLine['option'] = $this->chartDefaultOptions;
        $this->myPie['option'] = ['responsive' => true];
        $this->myBar['option'] = $this->chartDefaultOptions;
        $this->myBarTipoCobro['option'] = $this->chartDefaultOptions;
    }

    public function render()
    {
        $dateObj = new DateTime($this->date_ini);

        // Obtener todos los datos en una sola llamada según el período seleccionado
        $chartData = $this->getDataForPeriod($dateObj, 'chart');
        $pieData = $this->getDataForPeriod($dateObj, 'pie');
        $barData = $this->getDataForPeriod($dateObj, 'bar');
        $dataTipoCobro = $this->dataTipoCobro($dateObj);

        // Actualizar gráficos con los datos obtenidos
        Arr::set($this->myLine['data'], 'labels', $chartData['labels']);
        Arr::set($this->myLine['data'], 'datasets', $chartData['datasets']);

        Arr::set($this->myPie['data'], 'labels', $pieData['labels']);
        Arr::set($this->myPie['data'], 'datasets', $pieData['datasets']);

        Arr::set($this->myBar['data'], 'labels', $barData['labels']);
        Arr::set($this->myBar['data'], 'datasets', $barData['datasets']);

        Arr::set($this->myBarTipoCobro['data'], 'labels', $dataTipoCobro['labels']);
        Arr::set($this->myBarTipoCobro['data'], 'datasets', $dataTipoCobro['datasets']);

        return view('livewire.home.dashboard-live');
    }

    // Método unificado para obtener datos según el período seleccionado
    private function getDataForPeriod(DateTime $date, string $chartType)
    {
        $dateEnd = new DateTime($this->date_end);

        switch ($this->selectedTipe) {
            case 'Y':
                $methodName = "data{$chartType}Year";
                break;
            case 'm':
                $methodName = "data{$chartType}Month";
                break;
            case 'd':
                $methodName = "data{$chartType}Day";
                break;
            default:
                $methodName = "data{$chartType}Month";
                break;
        }

        return $this->$methodName($date, $dateEnd);
    }

    private function dataChartYear(DateTime $date, DateTime $dateEnd)
    {
        return $this->getChartData($date, $dateEnd, 'year');
    }

    private function dataChartMonth(DateTime $date, DateTime $dateEnd)
    {
        return $this->getChartData($date, $dateEnd, 'month');
    }

    private function dataChartDay(DateTime $date, DateTime $dateEnd)
    {
        return $this->getChartData($date, $dateEnd, 'day');
    }

    private function getChartData(DateTime $date, DateTime $dateEnd, string $timeUnit = 'month')
    {
        $timeConfigs = [
            'year' => [
                'size' => 12,
                'start' => 1,
                'format' => 'MONTH',
                'labels' => [
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ]
            ],
            'month' => [
                'size' => $date->format('t'),
                'start' => 1,
                'format' => 'DAY',
                'where' => ['whereMonth' => $date->format('m')]
            ],
            'day' => [
                'size' => 24,
                'start' => 0,
                'format' => 'HOUR',
                'where' => ['whereMonth' => $date->format('m'), 'whereDay' => $date->format('d')]
            ]
        ];

        $config = $timeConfigs[$timeUnit];
        $sucursals = Sucursal::all();

        // Optimización: Obtener todos los datos en una sola consulta con rango de fechas
        $query = Encomienda::whereBetween('created_at', [$date, $dateEnd]);

        // Aplicar cláusulas where adicionales si existen
        if (isset($config['where'])) {
            foreach ($config['where'] as $method => $value) {
                $query->$method('created_at', $value);
            }
        }

        $allData = $query->selectRaw(
            $config['format'] . "(created_at) as period,
            SUM(monto) as total_amount,
            sucursal_id"
        )
            ->groupBy('period', 'sucursal_id')
            ->orderBy('period')
            ->get()
            ->groupBy('sucursal_id');

        // Preparar datasets
        $datasets = [];
        foreach ($sucursals as $sucursal) {
            $periodData = array_fill($config['start'], $config['size'], 0);

            if (isset($allData[$sucursal->id])) {
                foreach ($allData[$sucursal->id] as $record) {
                    $periodData[$record->period] = $record->total_amount;
                }
            }

            // Usar colores consistentes para cada sucursal
            $color = sprintf('#%06X', crc32($sucursal->code) & 0xFFFFFF);
            $datasets[] = [
                'label' => $sucursal->code,
                'data' => array_values($periodData),
                'borderColor' => $color,
                'backgroundColor' => $color,
            ];
        }

        return [
            'labels' => $timeUnit === 'year' ? $config['labels'] : range($config['start'], $config['size'] - 1 + $config['start']),
            'datasets' => $datasets
        ];
    }

    private function dataPieYear(DateTime $date, DateTime $dateEnd)
    {
        return $this->getPaymentTypeData($date, $dateEnd, 'year');
    }

    private function dataPieMonth(DateTime $date, DateTime $dateEnd)
    {
        return $this->getPaymentTypeData($date, $dateEnd, 'month');
    }

    private function dataPieDay(DateTime $date, DateTime $dateEnd)
    {
        return $this->getPaymentTypeData($date, $dateEnd, 'day');
    }

    private function getPaymentTypeData(DateTime $date, DateTime $dateEnd, string $timeUnit = 'month')
    {
        // Configurar períodos de tiempo y etiquetas
        $timeConfigs = [
            'year' => [
                'labels' => ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                'format' => 'MONTH'
            ],
            'month' => [
                'format' => 'DAY',
                'where' => ['whereMonth' => $date->format('m')]
            ],
            'day' => [
                'format' => 'HOUR',
                'where' => ['whereMonth' => $date->format('m'), 'whereDay' => $date->format('d')]
            ]
        ];

        $config = $timeConfigs[$timeUnit];
        $sucursals = Sucursal::all();
        $paymentTypes = ['Contado', 'Credito'];

        // Obtener datos para todas las sucursales y tipos de pago en una sola consulta con rango de fechas
        $query = Encomienda::whereBetween('created_at', [$date, $dateEnd])
            ->whereIn('tipo_pago', $paymentTypes);

        if (isset($config['where'])) {
            foreach ($config['where'] as $method => $value) {
                $query->$method('created_at', $value);
            }
        }

        $data = $query->selectRaw(
            "tipo_pago,
            SUM(monto) as total_amount,
            sucursal_id"
        )
            ->groupBy('tipo_pago', 'sucursal_id')
            ->get()
            ->groupBy('tipo_pago');

        // Preparar datasets
        $datasets = [];
        foreach ($paymentTypes as $paymentType) {
            $branchData = array_fill_keys($sucursals->pluck('id')->toArray(), 0);

            if (isset($data[$paymentType])) {
                foreach ($data[$paymentType] as $record) {
                    $branchData[$record->sucursal_id] = $record->total_amount;
                }
            }

            $datasets[] = [
                'label' => $paymentType,
                'data' => array_values($branchData),
                'backgroundColor' => $this->paymentTypeColors[$paymentType],
                'borderColor' => $this->paymentTypeColors[$paymentType],
                'borderWidth' => 1,
                'borderRadius' => 5
            ];
        }

        return [
            'labels' => $sucursals->pluck('code')->toArray(),
            'datasets' => $datasets
        ];
    }

    private function dataBarYear(DateTime $date, DateTime $dateEnd)
    {
        $year = $date->format('Y');
        return $this->getBarData($date, $dateEnd, 'year');
    }

    private function dataBarMonth(DateTime $date, DateTime $dateEnd)
    {
        $year = $date->format('Y');
        $month = $date->format('m');
        return $this->getBarData($date, $dateEnd, 'month', $month);
    }

    private function dataBarDay(DateTime $date, DateTime $dateEnd)
    {
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');
        return $this->getBarData($date, $dateEnd, 'day', $month, $day);
    }

    private function getBarData(DateTime $date, DateTime $dateEnd, string $timeUnit = 'month', $month = null, $day = null)
    {
        $sucursals = Sucursal::all();
        $estados = array_keys($this->estadoColors);

        // Inicializar array de datos para todos los estados
        $labelsData = [];
        foreach ($sucursals as $sucursal) {
            $labelsData[$sucursal->code] = array_fill_keys($estados, 0);
        }

        // Obtener recuentos de envíos por estado para cada sucursal en una sola consulta con rango de fechas
        $query = Encomienda::whereBetween('created_at', [$date, $dateEnd])
            ->selectRaw('sucursal_id, estado_encomienda, COUNT(*) as total')
            ->groupBy('sucursal_id', 'estado_encomienda');

        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        if ($day) {
            $query->whereDay('created_at', $day);
        }

        $data = $query->get();

        // Organizar datos
        foreach ($data as $record) {
            $sucursal = $sucursals->firstWhere('id', $record->sucursal_id);
            if ($sucursal) {
                $labelsData[$sucursal->code][$record->estado_encomienda] = $record->total;
            }
        }

        // Preparar datasets para cada estado
        $datasets = [];
        foreach ($estados as $estado) {
            $dataset = [
                'label' => $estado,
                'data' => array_map(function ($branchData) use ($estado) {
                    return $branchData[$estado];
                }, $labelsData),
                'backgroundColor' => $this->estadoColors[$estado],
                'borderColor' => $this->estadoColors[$estado],
                'borderRadius' => 5,
                'borderWidth' => 1
            ];

            $datasets[] = $dataset;
        }

        return [
            'labels' => array_keys($labelsData),
            'datasets' => $datasets
        ];
    }

    private function dataTipoCobro(DateTime $date)
    {
        // Extraer componentes de fecha
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');

        // Definir métodos de pago y obtener sucursales una vez
        $metodoPagos = array_keys($this->metodoPagoColors);
        $sucursals = Sucursal::all();

        // Construir consulta base
        $query = Encomienda::whereYear('created_at', $year)
            ->whereIn('metodo_pago', $metodoPagos);

        // Aplicar filtros de tiempo según la unidad de tiempo seleccionada
        switch ($this->selectedTipe) {
            case 'm':
                $query->whereMonth('created_at', $month);
                break;
            case 'd':
                $query->whereMonth('created_at', $month)
                    ->whereDay('created_at', $day);
                break;
        }

        // Obtener todos los datos de pago en una sola consulta
        $data = $query->select('sucursal_id', 'metodo_pago', DB::raw('SUM(monto) as total'))
            ->groupBy('sucursal_id', 'metodo_pago')
            ->get();

        // Organizar datos por método de pago y sucursal
        $paymentData = [];
        foreach ($data as $record) {
            $paymentData[$record->metodo_pago][$record->sucursal_id] = $record->total;
        }

        // Generar datasets con colores consistentes
        $datasets = [];
        foreach ($metodoPagos as $metodoPago) {
            $dataArray = [];

            foreach ($sucursals as $sucursal) {
                $dataArray[] = $paymentData[$metodoPago][$sucursal->id] ?? 0;
            }

            $datasets[] = [
                'label' => $metodoPago,
                'data' => $dataArray,
                'backgroundColor' => $this->metodoPagoColors[$metodoPago],
                'borderColor' => $this->metodoPagoColors[$metodoPago],
                'borderWidth' => 1,
                'borderRadius' => 5
            ];
        }

        return [
            'labels' => $sucursals->pluck('code')->toArray(),
            'datasets' => $datasets
        ];
    }

    public function switch()
    {
        $type = $this->myChart['type'] == 'bar' ? 'pie' : 'bar';
        Arr::set($this->myChart, 'type', $type);
    }
}
