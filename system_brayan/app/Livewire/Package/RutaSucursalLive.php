<?php

namespace App\Livewire\Package;

use App\Models\Package\RutaSucursal;
use App\Services\Package\RutaSucursalService;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\ToastTrait;

class RutaSucursalLive extends Component
{
    use WithPagination, ToastTrait;

    protected RutaSucursalService $rutaSucursalService;

    // Estados del componente
    public $showModal = false;
    public $showDeleteModal = false;
    public $editingRuta = null;
    public $rutaToDelete = null;
    public $search = '';
    public $filterEstado = 'ACTIVA';


    public $filterSucursalDestino = '';
    public $filterVehiculo = '';
    public $fechaInicio = '';
    public $fechaFin = '';
    public $perPage = 10;

    // Campos del formulario
    public $sucursal_origen_id = '';
    public $sucursal_destino_id = '';
    public $transportista_id = '';
    public $vehiculo_id = '';
    public $fecha_salida = '';
    public $hora_salida = '';
    public $dia_semana = '';

    public $estado_ruta = 'ACTIVA';
    public $observaciones = '';
    public $isActive = true;

    // Datos para las listas
    public $sucursales = [];
    public $transportistas = [];
    public $vehiculos = [];

    // Opciones para los selects
    public $diasSemana = [
        'LUNES' => 'Lunes',
        'MARTES' => 'Martes',
        'MIERCOLES' => 'Miércoles',
        'JUEVES' => 'Jueves',
        'VIERNES' => 'Viernes',
        'SABADO' => 'Sábado',
        'DOMINGO' => 'Domingo'
    ];



    public $estados = [
        'ACTIVA' => 'Activa',
        'INACTIVA' => 'Inactiva',
        'SUSPENDIDA' => 'Suspendida',
        'COMPLETADO' => 'Completado'
    ];

    public function boot(RutaSucursalService $rutaSucursalService): void
    {
        $this->rutaSucursalService = $rutaSucursalService;
        $this->fechaInicio = Carbon::now()->format('Y-m-d');
        $this->fechaFin = Carbon::now()->addDays(30)->format('Y-m-d');
    }

        public function mount(): void
    {
        $this->loadData();
        $this->fecha_salida = Carbon::now()->format('Y-m-d');
        $this->hora_salida = '10:00';

        // Inicializar la sucursal origen con la del usuario logueado
        $this->sucursal_origen_id = Auth::user()->sucursal_id;

        // Calcular el día de la semana automáticamente
        $this->calcularDiaSemana();
    }

    public function loadData(): void
    {
        $this->sucursales = $this->rutaSucursalService->getSucursales();
        $this->transportistas = $this->rutaSucursalService->getTransportistas();
        $this->vehiculos = $this->rutaSucursalService->getVehiculos();
    }

    public function calcularDiaSemana(): void
    {
        if (!empty($this->fecha_salida)) {
            $fecha = Carbon::parse($this->fecha_salida);
            $this->dia_semana = $this->obtenerDiaSemana($fecha->dayOfWeek);
        }
    }

    private function obtenerDiaSemana($dayOfWeek): string
    {
        return match($dayOfWeek) {
            0 => 'DOMINGO',
            1 => 'LUNES',
            2 => 'MARTES',
            3 => 'MIERCOLES',
            4 => 'JUEVES',
            5 => 'VIERNES',
            6 => 'SABADO',
            default => 'LUNES'
        };
    }

    public function render()
    {
        $filters = [];

        // Filtrar por la sucursal del usuario logueado
        $filters['sucursal_origen_id'] = Auth::user()->sucursal_id;

        if (!empty($this->filterEstado)) {
            $filters['estado_ruta'] = $this->filterEstado;
        }

        if (!empty($this->filterVehiculo)) {
            $filters['vehiculo_id'] = $this->filterVehiculo;
        }

        if (!empty($this->fechaInicio)) {
            $filters['fecha_inicio'] = $this->fechaInicio;
        }

        if (!empty($this->fechaFin)) {
            $filters['fecha_fin'] = $this->fechaFin;
        }

        if (!empty($this->filterSucursalDestino)) {
            $filters['sucursal_destino_id'] = $this->filterSucursalDestino;
        }

        $rutas = $this->rutaSucursalService->getRutas($this->search, $filters, $this->perPage);
        $estadisticas = $this->rutaSucursalService->getEstadisticasPorSucursal(Auth::user()->sucursal_id);

        return view('livewire.package.ruta-sucursal-live', [
            'rutas' => $rutas,
            'estadisticas' => $estadisticas,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterEstado(): void
    {
        $this->resetPage();
    }

    public function updatedFilterVehiculo(): void
    {
        $this->resetPage();
    }

    public function updatedFechaInicio(): void
    {
        $this->resetPage();
    }

    public function updatedFechaFin(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSucursalDestino(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFechaSalida(): void
    {
        $this->calcularDiaSemana();
    }

    public function updatedSucursalDestinoId(): void
    {
        // Validar en tiempo real que no sea igual al origen
        if (!empty($this->sucursal_origen_id) && !empty($this->sucursal_destino_id)) {
            if ((int)$this->sucursal_origen_id === (int)$this->sucursal_destino_id) {
                $this->addError('sucursal_destino_id', 'La sucursal de destino debe ser diferente a la sucursal de origen.');
            } else {
                $this->resetErrorBag('sucursal_destino_id');
            }
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['search', 'filterEstado', 'filterSucursalDestino', 'filterVehiculo', 'fechaInicio', 'fechaFin']);
        $this->resetPage();
    }

    public function openModal(): void
    {
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

        public function resetForm(): void
    {
        $this->reset([
            'editingRuta', 'sucursal_destino_id',
            'transportista_id', 'vehiculo_id', 'fecha_salida', 'hora_salida',
            'dia_semana', 'estado_ruta', 'observaciones', 'isActive'
        ]);

        // Mantener la sucursal origen del usuario logueado
        $this->sucursal_origen_id = Auth::user()->sucursal_id;

        $this->fecha_salida = Carbon::now()->format('Y-m-d');
        $this->hora_salida = '08:00';

        $this->estado_ruta = 'ACTIVA';
        $this->isActive = true;

        // Calcular el día de la semana automáticamente
        $this->calcularDiaSemana();
    }

    public function editRuta($rutaId): void
    {
        $ruta = $this->rutaSucursalService->getRutaById($rutaId);

        // Verificar que la ruta pertenece a la sucursal del usuario logueado
        if ($ruta->sucursal_origen_id !== Auth::user()->sucursal_id) {
            $this->error('No tienes permisos para editar esta ruta.');
            return;
        }

        $this->editingRuta = $ruta;
        $this->sucursal_origen_id = $ruta->sucursal_origen_id;
        $this->sucursal_destino_id = $ruta->sucursal_destino_id;
        $this->transportista_id = $ruta->transportista_id;
        $this->vehiculo_id = $ruta->vehiculo_id;
        $this->fecha_salida = $ruta->fecha_salida ? Carbon::parse($ruta->fecha_salida)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $this->hora_salida = $ruta->hora_salida ? Carbon::parse($ruta->hora_salida)->format('H:i') : '08:00';
        $this->dia_semana = $ruta->dia_semana;

        $this->estado_ruta = $ruta->estado_ruta;
        $this->observaciones = $ruta->observaciones;
        $this->isActive = $ruta->isActive;

        $this->showModal = true;
    }

    public function saveRuta(): void
    {
        // Pasar el ID a excluir si se está editando
        $excludeId = $this->editingRuta ? $this->editingRuta->id : null;
        $rules = RutaSucursal::rules($excludeId);
        // Excluir 'code' de la validación ya que se genera automáticamente
        unset($rules['code']);
        
        // Validar que la sucursal de origen no sea igual a la de destino
        if (!empty($this->sucursal_origen_id) && !empty($this->sucursal_destino_id)) {
            if ((int)$this->sucursal_origen_id === (int)$this->sucursal_destino_id) {
                $this->addError('sucursal_destino_id', 'La sucursal de destino debe ser diferente a la sucursal de origen.');
                $this->addError('sucursal_origen_id', 'La sucursal de origen no puede ser igual a la de destino.');
                return;
            }
        }
        
        try {
            $this->validate($rules, RutaSucursal::messages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si la validación falla, verificar específicamente el error de different
            if ($e->validator->errors()->has('sucursal_destino_id')) {
                $this->addError('sucursal_destino_id', 'La sucursal de destino debe ser diferente a la sucursal de origen.');
            }
            throw $e;
        }

        // Validación personalizada para transportista y vehículo únicos
        if (RutaSucursal::validateUniqueTransportistaVehiculo(
            $this->sucursal_origen_id,
            $this->sucursal_destino_id,
            $this->transportista_id,
            $this->vehiculo_id,
            $this->editingRuta ? $this->editingRuta->id : null
        )) {
            $this->addError('transportista_id', 'Ya existe una ruta con estos datos.');
            $this->addError('vehiculo_id', 'Ya existe una ruta con estos datos.');
            return;
        }

        $data = [
            'sucursal_origen_id' => $this->sucursal_origen_id,
            'sucursal_destino_id' => $this->sucursal_destino_id,
            'transportista_id' => $this->transportista_id,
            'vehiculo_id' => $this->vehiculo_id,
            'fecha_salida' => $this->fecha_salida,
            'hora_salida' => $this->hora_salida,
            'dia_semana' => $this->dia_semana,

            'estado_ruta' => $this->estado_ruta,
            'observaciones' => $this->observaciones,
            'isActive' => $this->isActive,
        ];

        try {
            if ($this->editingRuta) {
                $this->rutaSucursalService->actualizarRuta($this->editingRuta->id, $data);
                $this->success('Ruta actualizada exitosamente.');
            } else {
                $this->rutaSucursalService->crearRuta($data);
                $this->success('Ruta creada exitosamente.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            $this->error('Error al guardar la ruta: ' . $e->getMessage());
        }
    }

    public function openDeleteModal($rutaId): void
    {
        $ruta = $this->rutaSucursalService->getRutaById($rutaId);

        // Verificar que la ruta pertenece a la sucursal del usuario logueado
        if ($ruta->sucursal_origen_id !== Auth::user()->sucursal_id) {
            $this->error('No tienes permisos para eliminar esta ruta.');
            return;
        }

        $this->rutaToDelete = $ruta;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->rutaToDelete = null;
    }

    public function deleteRuta(): void
    {
        try {
            $this->rutaSucursalService->eliminarRuta($this->rutaToDelete->id);
            $this->success('Ruta eliminada exitosamente.');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            $this->error('Error al eliminar la ruta: ' . $e->getMessage());
        }
    }

    public function toggleEstado($rutaId): void
    {
        try {
            $ruta = $this->rutaSucursalService->getRutaById($rutaId);

            // Verificar que la ruta pertenece a la sucursal del usuario logueado
            if ($ruta->sucursal_origen_id !== Auth::user()->sucursal_id) {
                    $this->error('No tienes permisos para modificar esta ruta.');
                return;
            }

            $nuevoEstado = $ruta->estado_ruta === 'ACTIVA' ? 'INACTIVA' : 'ACTIVA';

            $this->rutaSucursalService->actualizarRuta($rutaId, [
                'estado_ruta' => $nuevoEstado,
                'isActive' => $nuevoEstado === 'ACTIVA'
            ]);

            $this->success('Estado de la ruta actualizado exitosamente.');
        } catch (\Exception $e) {
            $this->error('Error al actualizar el estado: ' . $e->getMessage());
        }
    }
}
