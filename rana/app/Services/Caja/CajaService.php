<?php

namespace App\Services\Caja;

use App\Models\Caja\Caja;
use App\Models\Caja\EntryCaja;
use App\Models\Caja\ExitCaja;
use App\Models\Configuration\TipoEntryCaja;
use Illuminate\Support\Facades\DB;

class CajaService
{
    /**
     * Obtener todas las cajas del usuario autenticado
     */
    public function getCajasByUser($userId, $search = '', $filterStatus = '')
    {
        return Caja::with(['user', 'entries', 'exits'])
            ->where('user_id', $userId)
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when($filterStatus !== '', function ($query) use ($filterStatus) {
                $query->where('isActive', $filterStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    /**
     * Obtener la caja activa del usuario
     */
    public function getCajaActiva($userId)
    {
        return Caja::where('user_id', $userId)
            ->where('isActive', true)
            ->first();
    }

    /**
     * Crear una nueva caja
     */
    public function crearCaja($userId, $montoApertura)
    {
        // Verificar si ya existe una caja activa
        $cajaActiva = $this->getCajaActiva($userId);
        if ($cajaActiva) {
            throw new \Exception('Ya tienes una caja activa. Debes cerrarla antes de abrir una nueva.');
        }

        return Caja::create([
            'user_id' => $userId,
            'monto_apertura' => $montoApertura,
            'monto_cierre' => 0,
            'isActive' => true,
        ]);
    }

    /**
     * Cerrar una caja
     */
    public function cerrarCaja($cajaId, $montoCierre = null)
    {
        $caja = Caja::findOrFail($cajaId);

        if (!$caja->isActive) {
            throw new \Exception('La caja ya está cerrada.');
        }

        // El monto de cierre siempre será calculado automáticamente
        $montoCierre = $this->calcularBalance($caja);

        $caja->update([
            'monto_cierre' => $montoCierre,
            'isActive' => false,
        ]);

        return $caja;
    }

    /**
     * Crear una entrada de caja
     */
    public function crearEntrada($cajaId, $monto, $descripcion, $tipoEntryId, $metodoPago)
    {
        $caja = Caja::findOrFail($cajaId);
        
        if (!$caja->isActive) {
            throw new \Exception('No se pueden agregar entradas a una caja cerrada.');
        }

        return EntryCaja::create([
            'caja_id' => $cajaId,
            'monto_entry' => $monto,
            'description' => $descripcion,
            'tipo_entry_id' => $tipoEntryId,
            'metodo_pago' => $metodoPago,
        ]);
    }

    /**
     * Actualizar una entrada de caja
     */
    public function actualizarEntrada($entryId, $monto, $descripcion, $tipoEntryId, $metodoPago)
    {
        $entrada = EntryCaja::findOrFail($entryId);

        $entrada->update([
            'monto_entry' => $monto,
            'description' => $descripcion,
            'tipo_entry_id' => $tipoEntryId,
            'metodo_pago' => $metodoPago,
        ]);

        return $entrada;
    }

    /**
     * Eliminar una entrada de caja
     */
    public function eliminarEntrada($entryId)
    {
        $entrada = EntryCaja::findOrFail($entryId);
        $entrada->delete();
        return true;
    }

    /**
     * Crear una salida de caja
     */
    public function crearSalida($cajaId, $monto, $descripcion, $tipoExitId, $metodoPago)
    {
        $caja = Caja::findOrFail($cajaId);

        if (!$caja->isActive) {
            throw new \Exception('No se pueden agregar salidas a una caja cerrada.');
        }

        return ExitCaja::create([
            'caja_id' => $cajaId,
            'monto_exit' => $monto,
            'description' => $descripcion,
            'tipo_exit_id' => $tipoExitId,
            'metodo_pago' => $metodoPago,
        ]);
    }

    /**
     * Actualizar una salida de caja
     */
    public function actualizarSalida($exitId, $monto, $descripcion, $tipoExitId, $metodoPago)
    {
        $salida = ExitCaja::findOrFail($exitId);

        $salida->update([
            'monto_exit' => $monto,
            'description' => $descripcion,
            'tipo_exit_id' => $tipoExitId,
            'metodo_pago' => $metodoPago,
        ]);

        return $salida;
    }

    /**
     * Eliminar una salida de caja
     */
    public function eliminarSalida($exitId)
    {
        $salida = ExitCaja::findOrFail($exitId);
        $salida->delete();
        return true;
    }

    /**
     * Calcular el total de entradas de una caja
     */
    public function getTotalEntradas($caja)
    {
        return $caja->entries->sum('monto_entry');
    }

    /**
     * Calcular el total de salidas de una caja
     */
    public function getTotalSalidas($caja)
    {
        return $caja->exits->sum('monto_exit');
    }

    /**
     * Calcular el balance de una caja
     */
    public function calcularBalance($caja)
    {
        $totalEntradas = $this->getTotalEntradas($caja);
        $totalSalidas = $this->getTotalSalidas($caja);

        return $caja->monto_apertura + $totalEntradas - $totalSalidas;
    }

    /**
     * Obtener estadísticas de caja
     */
    public function getEstadisticasCaja($caja)
    {
        return [
            'monto_apertura' => $caja->monto_apertura,
            'total_entradas' => $this->getTotalEntradas($caja),
            'total_salidas' => $this->getTotalSalidas($caja),
            'balance' => $this->calcularBalance($caja),
            'num_entradas' => $caja->entries->count(),
            'num_salidas' => $caja->exits->count(),
        ];
    }

    /**
     * Eliminar una caja y todas sus entradas/salidas
     */
    public function eliminarCaja($cajaId)
    {
        return DB::transaction(function () use ($cajaId) {
            $caja = Caja::findOrFail($cajaId);

            // Eliminar entradas y salidas
            $caja->entries()->delete();
            $caja->exits()->delete();

            // Eliminar la caja
            $caja->delete();

            return true;
        });
    }

    /**
     * Validar si se puede crear una nueva caja
     */
    public function puedeCrearCaja($userId)
    {
        $cajaActiva = $this->getCajaActiva($userId);
        return $cajaActiva === null;
    }

    /**
     * Obtener resumen de todas las cajas del usuario
     */
    public function getResumenCajas($userId)
    {
        $cajas = Caja::where('user_id', $userId)->get();

        $resumen = [
            'total_cajas' => $cajas->count(),
            'cajas_activas' => $cajas->where('isActive', true)->count(),
            'cajas_cerradas' => $cajas->where('isActive', false)->count(),
            'total_entradas' => 0,
            'total_salidas' => 0,
        ];

        foreach ($cajas as $caja) {
            $resumen['total_entradas'] += $this->getTotalEntradas($caja);
            $resumen['total_salidas'] += $this->getTotalSalidas($caja);
        }

        return $resumen;
    }

    /**
     * Editar una caja (no permitido)
     */
    public function editarCaja($cajaId, $data)
    {
        throw new \Exception('No está permitido editar una caja.');
    }
}
