<?php

namespace App\Models\Package;

use App\Models\Configuration\Sucursal;
use App\Models\Configuration\Transportista;
use App\Models\Configuration\Vehiculo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutaSucursal extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'sucursal_origen_id',      // Sucursal de origen
        'sucursal_destino_id',     // Sucursal de destino
        'transportista_id',        // Transportista asignado
        'vehiculo_id',             // Vehículo asignado
        'fecha_salida',            // Fecha de salida programada
        'hora_salida',             // Hora de salida programada
        'dia_semana',              // Día de la semana (Lunes, Martes, etc.)
        'estado_ruta',             // ACTIVA, INACTIVA, SUSPENDIDA, COMPLETADO
        'observaciones',           // Observaciones de la ruta
        'isActive',                // Si la ruta está activa
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'hora_salida' => 'datetime',
        'isActive' => 'boolean',
    ];

    // Relaciones
    public function sucursalOrigen()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_origen_id');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }

    public function transportista()
    {
        return $this->belongsTo(Transportista::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function encomiendas()
    {
        return $this->hasMany(Encomienda::class, 'ruta_id', 'id');
    }

    // Scopes para consultas comunes
    public function scopeActivas($query)
    {
        return $query->where('isActive', true)->where('estado_ruta', 'ACTIVA');
    }

    public function scopePorSucursalOrigen($query, $sucursalId)
    {
        return $query->where('sucursal_origen_id', $sucursalId);
    }

    public function scopePorSucursalDestino($query, $sucursalId)
    {
        return $query->where('sucursal_destino_id', $sucursalId);
    }

    public function scopePorDiaSemana($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    // Métodos de utilidad
    public function getNombreRutaAttribute()
    {
        return "{$this->sucursalOrigen->name} → {$this->sucursalDestino->name}";
    }

    public function getHorarioCompletoAttribute()
    {
        return $this->hora_salida ? $this->hora_salida->format('H:i') : 'No definido';
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado_ruta) {
            'ACTIVA' => 'green',
            'INACTIVA' => 'red',
            'SUSPENDIDA' => 'yellow',
            'COMPLETADO' => 'blue',
            default => 'gray'
        };
    }

    // Validaciones
    public static function rules($excludeId = null)
    {
        $rules = [
            'sucursal_origen_id' => 'required|exists:sucursals,id',
            'sucursal_destino_id' => 'required|exists:sucursals,id|different:sucursal_origen_id',
            'transportista_id' => 'required|exists:transportistas,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha_salida' => 'required|date|after_or_equal:today',
            'hora_salida' => 'required|date_format:H:i',
            'dia_semana' => 'required|in:LUNES,MARTES,MIERCOLES,JUEVES,VIERNES,SABADO,DOMINGO',
            'estado_ruta' => 'required|in:ACTIVA,INACTIVA,SUSPENDIDA,COMPLETADO',
            'observaciones' => 'nullable|string|max:500',
            'isActive' => 'boolean',
        ];

        // Validación de code solo si se proporciona (opcional, se genera automáticamente)
        // Si se está editando, excluir el ID actual de la validación unique
        if ($excludeId) {
            $rules['code'] = 'nullable|string|max:255|unique:rutas_sucursals,code,' . $excludeId;
        } else {
            $rules['code'] = 'nullable|string|max:255|unique:rutas_sucursals,code';
        }

        return $rules;
    }

    public static function messages()
    {
        return [
            'sucursal_origen_id.required' => 'La sucursal de origen es obligatoria.',
            'sucursal_destino_id.required' => 'La sucursal de destino es obligatoria.',
            'sucursal_destino_id.different' => 'La sucursal de destino debe ser diferente a la sucursal de origen.',
            'transportista_id.required' => 'El transportista es obligatorio.',
            'vehiculo_id.required' => 'El vehículo es obligatorio.',
            'fecha_salida.required' => 'La fecha de salida es obligatoria.',
            'fecha_salida.after_or_equal' => 'La fecha de salida debe ser hoy o una fecha futura.',
            'hora_salida.required' => 'La hora de salida es obligatoria.',
            'hora_salida.date_format' => 'El formato de hora debe ser HH:MM.',
            'dia_semana.required' => 'El día de la semana es obligatorio.',
            'estado_ruta.required' => 'El estado de la ruta es obligatorio.',
        ];
    }

    // Validación personalizada para transportista y vehículo únicos por ruta
    public static function validateUniqueTransportistaVehiculo($sucursalOrigenId, $sucursalDestinoId, $transportistaId, $vehiculoId, $excludeId = null)
    {
        $query = self::where('sucursal_origen_id', $sucursalOrigenId)
                    ->where('sucursal_destino_id', $sucursalDestinoId)
                    ->where('transportista_id', $transportistaId)
                    ->where('vehiculo_id', $vehiculoId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
