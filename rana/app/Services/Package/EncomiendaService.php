<?php

namespace App\Services\Package;

use App\Models\Package\Encomienda;
use App\Models\Package\Customer;
use App\Models\Package\Paquete;
use App\Models\Configuration\Sucursal;
use App\Models\Configuration\Transportista;
use App\Models\Configuration\Vehiculo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EncomiendaService
{
    public function getAll($search = '', $filters = [], $perPage = 10)
    {
        $query = Encomienda::with([
            'user',
            'transportista',
            'vehiculo',
            'remitente',
            'sucursal_remitente',
            'destinatario',
            'sucursal_destinatario',
            'facturacion',
            'invoice',
            'paquetes'
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('remitente', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('destinatario', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // Aplicar filtros
        if (isset($filters['estado_encomienda'])) {
            $query->where('estado_encomienda', $filters['estado_encomienda']);
        }

        if (isset($filters['estado_pago'])) {
            $query->where('estado_pago', $filters['estado_pago']);
        }

        if (isset($filters['sucursal_id'])) {
            $query->where('sucursal_id', $filters['sucursal_id']);
        }

        if (isset($filters['sucursal_dest_id'])) {
            $query->where('sucursal_dest_id', $filters['sucursal_dest_id']);
        }

        if (isset($filters['transportista_id'])) {
            $query->where('transportista_id', $filters['transportista_id']);
        }

        if (isset($filters['ruta_id'])) {
            $query->where('ruta_id', $filters['ruta_id']);
        }

        if (isset($filters['fecha_creacion'])) {
            $query->whereDate('fecha_creacion', $filters['fecha_creacion']);
        }

        // Filtro de rango de fechas (desde/hasta)
        if (isset($filters['fecha_desde']) && isset($filters['fecha_hasta'])) {
            $query->whereBetween('fecha_creacion', [
                \Carbon\Carbon::parse($filters['fecha_desde'])->startOfDay(),
                \Carbon\Carbon::parse($filters['fecha_hasta'])->endOfDay()
            ]);
        } elseif (isset($filters['fecha_desde'])) {
            $query->where('fecha_creacion', '>=', \Carbon\Carbon::parse($filters['fecha_desde'])->startOfDay());
        } else        if (isset($filters['fecha_hasta'])) {
            $query->where('fecha_creacion', '<=', \Carbon\Carbon::parse($filters['fecha_hasta'])->endOfDay());
        }

        if (isset($filters['isHome'])) {
            $query->where('isHome', $filters['isHome']);
        }

        if (isset($filters['isReturn'])) {
            $query->where('isReturn', $filters['isReturn']);
        }

        if ($perPage == 0) {
            return $query->orderBy('created_at', 'desc')->get();
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getById($id)
    {
        return Encomienda::with([
            'user',
            'transportista',
            'vehiculo',
            'remitente',
            'sucursal_remitente',
            'destinatario',
            'sucursal_destinatario',
            'facturacion',
            'paquetes'
        ])->findOrFail($id);
    }

    public function create($data)
    {
        DB::beginTransaction();
        try {
            // Validar que se proporcione sucursal_id o que el usuario tenga sucursal
            $sucursalId = $data['sucursal_id'] ?? null;
            
            // Generar código único basado en sucursal
            $data['code'] = $this->generateUniqueCode($sucursalId);

            // Usuario actual
            $data['user_id'] = Auth::id();

            // Fecha de creación
            $data['fecha_creacion'] = $data['fecha_creacion'] ?? now();

            // Estado inicial
            $data['estado_encomienda'] = 'REGISTRADO';

            // PIN de seguridad (solo si no se proporciona)
            if (!isset($data['pin'])) {
                $data['pin'] = rand(100, 999);
            }

            // Validar monto mínimo
            if (isset($data['monto']) && $data['monto'] < 0) {
                throw new \Exception('El monto no puede ser negativo');
            }

            // Crear encomienda
            $encomienda = Encomienda::create($data);

            // Crear paquetes si se proporcionan
            if (isset($data['paquetes']) && is_array($data['paquetes'])) {
                foreach ($data['paquetes'] as $paqueteData) {
                    $paqueteData['encomienda_id'] = $encomienda->id;
                    Paquete::create($paqueteData);
                }
            }

            DB::commit();
            return $encomienda;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $encomienda = Encomienda::findOrFail($id);

            // Validar que solo se puedan actualizar encomiendas en estado REGISTRADO
            if ($encomienda->estado_encomienda !== 'REGISTRADO') {
                throw new \Exception('No se puede actualizar una encomienda que ya ha sido procesada. Estado actual: ' . $encomienda->estado_encomienda);
            }

            $encomienda->update($data);

            // Actualizar paquetes si se proporcionan
            if (isset($data['paquetes']) && is_array($data['paquetes'])) {
                // Eliminar paquetes existentes
                $encomienda->paquetes()->delete();

                // Crear nuevos paquetes
                foreach ($data['paquetes'] as $paqueteData) {
                    $paqueteData['encomienda_id'] = $encomienda->id;
                    Paquete::create($paqueteData);
                }
            }

            DB::commit();
            return $encomienda;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function delete($id)
    {
        $encomienda = Encomienda::findOrFail($id);

        // Verificar si se puede eliminar
        if ($encomienda->estado_encomienda !== 'REGISTRADO') {
            throw new \Exception('No se puede eliminar una encomienda que ya ha sido procesada.');
        }

        return $encomienda->delete();
    }

    public function updateEstado($id, $estado, $fecha = null)
    {
        $encomienda = Encomienda::findOrFail($id);

        // Validar transiciones de estado válidas
        $transicionesValidas = [
            'REGISTRADO' => ['ENVIADO'],
            'ENVIADO' => ['RECIBIDO'],
            'RECIBIDO' => ['ENTREGADO', 'RETORNADO'],
        ];

        $estadoActual = $encomienda->estado_encomienda;
        
        if (!isset($transicionesValidas[$estadoActual]) || !in_array($estado, $transicionesValidas[$estadoActual])) {
            throw new \Exception("Transición de estado inválida: No se puede cambiar de {$estadoActual} a {$estado}");
        }

        $encomienda->estado_encomienda = $estado;

        // Actualizar fecha según el estado
        $fechaActualizacion = $fecha ?? now();
        switch ($estado) {
            case 'ENVIADO':
                $encomienda->fecha_envio = $fechaActualizacion;
                break;
            case 'RECIBIDO':
                $encomienda->fecha_recepcion = $fechaActualizacion;
                break;
            case 'ENTREGADO':
                $encomienda->fecha_entrega = $fechaActualizacion;
                break;
            case 'RETORNADO':
                $encomienda->fecha_retorno = $fechaActualizacion;
                break;
        }

        return $encomienda->save();
    }

    public function getCustomers($search = '')
    {
        $query = Customer::where('isActive', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->get();
    }

    public function getSucursales()
    {
        return Sucursal::where('isActive', true)->orderBy('name')->get();
    }

    public function getTransportistas()
    {
        return Transportista::where('isActive', true)->orderBy('name')->get();
    }

    public function getVehiculos()
    {
        return Vehiculo::where('isActive', true)->orderBy('name')->get();
    }

    public function getVehiculosByTransportista($transportistaId)
    {
        return Vehiculo::where('isActive', true)
                       ->where('transportista_id', $transportistaId)
                       ->orderBy('name')
                       ->get();
    }

    /**
     * Genera un código único para la encomienda
     * Formato: {codigo_sucursal}-{correlativo}
     * 
     * @param int|null $sucursalId ID de la sucursal. Si es null, usa la sucursal del usuario autenticado
     * @return string Código único generado
     */
    public function generateUniqueCode($sucursalId = null)
    {
        // Obtener sucursal
        if ($sucursalId === null) {
            $sucursal = Auth::user()->sucursal ?? null;
            if (!$sucursal) {
                throw new \Exception('No se puede generar código: Usuario no tiene sucursal asignada');
            }
            $sucursalCode = $sucursal->code;
            $sucursalId = $sucursal->id;
        } else {
            $sucursal = Sucursal::findOrFail($sucursalId);
            $sucursalCode = $sucursal->code;
        }

        // Obtener el siguiente correlativo para esta sucursal
        $ultimoCorrelativo = Encomienda::where('sucursal_id', $sucursalId)
            ->where('code', 'like', $sucursalCode . '-%')
            ->orderByRaw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED) DESC')
            ->value('code');

        if ($ultimoCorrelativo) {
            $ultimoNumero = (int) substr($ultimoCorrelativo, strrpos($ultimoCorrelativo, '-') + 1);
            $correlativo = $ultimoNumero + 1;
        } else {
            $correlativo = 1;
        }

        $code = $sucursalCode . '-' . str_pad($correlativo, 6, '0', STR_PAD_LEFT);

        // Verificar que no exista (por si acaso)
        if (Encomienda::where('code', $code)->exists()) {
            // Si existe, buscar el siguiente disponible
            do {
                $correlativo++;
                $code = $sucursalCode . '-' . str_pad($correlativo, 6, '0', STR_PAD_LEFT);
            } while (Encomienda::where('code', $code)->exists());
        }

        return $code;
    }

    public function calculateTotal($paquetes)
    {
        $total = 0;
        foreach ($paquetes as $paquete) {
            $total += $paquete['amount'] ?? 0;
        }
        return $total;
    }

    /**
     * Recibe una encomienda en la sucursal destino
     * 
     * @param int $id ID de la encomienda
     * @param \DateTime|null $fecha Fecha de recepción (opcional)
     * @return Encomienda
     * @throws \Exception
     */
    public function recibirEncomienda($id, $fecha = null)
    {
        $encomienda = Encomienda::findOrFail($id);

        // Validar que la encomienda esté en estado ENVIADO
        if ($encomienda->estado_encomienda !== 'ENVIADO') {
            throw new \Exception("Solo se pueden recibir encomiendas en estado ENVIADO. Estado actual: {$encomienda->estado_encomienda}");
        }

        // Validar que el usuario esté en la sucursal destino
        $sucursalUsuario = Auth::user()->sucursal->id ?? null;
        if ($sucursalUsuario !== $encomienda->sucursal_dest_id) {
            throw new \Exception("Solo se pueden recibir encomiendas en la sucursal destino asignada");
        }

        return $this->updateEstado($id, 'RECIBIDO', $fecha);
    }

    /**
     * Entrega una encomienda al destinatario
     * 
     * @param int $id ID de la encomienda
     * @param string|null $pin PIN de seguridad (requerido si no es entrega a domicilio)
     * @param \DateTime|null $fecha Fecha de entrega (opcional)
     * @return Encomienda
     * @throws \Exception
     */
    public function entregarEncomienda($id, $pin = null, $fecha = null)
    {
        $encomienda = Encomienda::findOrFail($id);

        // Validar que la encomienda esté en estado RECIBIDO
        if ($encomienda->estado_encomienda !== 'RECIBIDO') {
            throw new \Exception("Solo se pueden entregar encomiendas en estado RECIBIDO. Estado actual: {$encomienda->estado_encomienda}");
        }

        // Validar PIN si no es entrega a domicilio
        if (!$encomienda->isHome && $pin !== (string)$encomienda->pin) {
            throw new \Exception("PIN de seguridad incorrecto");
        }

        // Si es contra entrega, validar que el pago esté procesado
        if ($encomienda->estado_pago === 'CONTRA ENTREGA' && $encomienda->estado_credito !== 'CANCELADO') {
            throw new \Exception("No se puede entregar una encomienda contra entrega sin procesar el pago");
        }

        return $this->updateEstado($id, 'ENTREGADO', $fecha);
    }

    /**
     * Retorna una encomienda al remitente
     * 
     * @param int $id ID de la encomienda
     * @param string $motivo Motivo del retorno
     * @param \DateTime|null $fecha Fecha de retorno (opcional)
     * @return Encomienda
     * @throws \Exception
     */
    public function retornarEncomienda($id, $motivo = '', $fecha = null)
    {
        $encomienda = Encomienda::findOrFail($id);

        // Validar que la encomienda esté en estado RECIBIDO
        if ($encomienda->estado_encomienda !== 'RECIBIDO') {
            throw new \Exception("Solo se pueden retornar encomiendas en estado RECIBIDO. Estado actual: {$encomienda->estado_encomienda}");
        }

        // Actualizar observaciones con el motivo
        if ($motivo) {
            $observaciones = $encomienda->observation ?? '';
            $observaciones .= ($observaciones ? "\n" : '') . "RETORNO: " . $motivo;
            $encomienda->observation = $observaciones;
        }

        $encomienda->save();

        return $this->updateEstado($id, 'RETORNADO', $fecha);
    }

    /**
     * Busca encomiendas por PIN
     * 
     * @param string $pin PIN de seguridad
     * @param array $filters Filtros adicionales
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function buscarPorPin($pin, $filters = [])
    {
        $query = Encomienda::with([
            'user',
            'transportista',
            'vehiculo',
            'remitente',
            'sucursal_remitente',
            'destinatario',
            'sucursal_destinatario',
            'facturacion',
            'paquetes'
        ])->where('pin', $pin);

        // Aplicar filtros adicionales
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $query->where($key, $value);
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
