<?php

namespace Database\Factories\Package;

use App\Models\Configuration\Sucursal;
use App\Models\Configuration\Transportista;
use App\Models\Configuration\Vehiculo;
use App\Models\Package\RutaSucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package\RutaSucursal>
 */
class RutaSucursalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
        public function definition(): array
    {
        $sucursales = Sucursal::all();
        $transportistas = Transportista::all();
        $vehiculos = Vehiculo::all();

        if ($sucursales->count() < 2) {
            throw new \Exception('Se necesitan al menos 2 sucursales para crear rutas');
        }

        if ($transportistas->isEmpty()) {
            throw new \Exception('Se necesitan transportistas para crear rutas');
        }

        if ($vehiculos->isEmpty()) {
            throw new \Exception('Se necesitan vehículos para crear rutas');
        }

        $sucursalOrigen = $sucursales->random();
        // Permitir que la sucursal destino sea la misma que la origen (30% de probabilidad)
        $sucursalDestino = $this->faker->boolean(30)
            ? $sucursalOrigen
            : $sucursales->where('id', '!=', $sucursalOrigen->id)->random();

        $diasSemana = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO'];
        $estados = ['ACTIVA', 'INACTIVA', 'SUSPENDIDA', 'COMPLETADO'];

        return [
            'code' => $this->faker->unique()->numerify('RS-####'),
            'sucursal_origen_id' => $sucursalOrigen->id,
            'sucursal_destino_id' => $sucursalDestino->id,
            'transportista_id' => $transportistas->random()->id,
            'vehiculo_id' => $vehiculos->random()->id,
            'fecha_salida' => $this->faker->dateTimeBetween('now', '+30 days'),
            'hora_salida' => $this->faker->time('H:i:s'),
            'dia_semana' => $this->faker->randomElement($diasSemana),
            'estado_ruta' => $this->faker->randomElement($estados),
            'observaciones' => $this->faker->optional(0.7)->sentence(),
            'isActive' => $this->faker->boolean(80), // 80% de probabilidad de estar activo
        ];
    }

    /**
     * Indica que la ruta está activa
     */
    public function activa(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_ruta' => 'ACTIVA',
            'isActive' => true,
        ]);
    }

    /**
     * Indica que la ruta está inactiva
     */
    public function inactiva(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_ruta' => 'INACTIVA',
            'isActive' => false,
        ]);
    }

    /**
     * Indica que la ruta está suspendida
     */
    public function suspendida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_ruta' => 'SUSPENDIDA',
            'isActive' => false,
        ]);
    }

    /**
     * Indica que la ruta está completada
     */
    public function completada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_ruta' => 'COMPLETADO',
            'isActive' => false,
        ]);
    }

    /**
     * Ruta para un día específico de la semana
     */
    public function paraDia($dia): static
    {
        return $this->state(fn (array $attributes) => [
            'dia_semana' => $dia,
        ]);
    }

    /**
     * Ruta con hora específica
     */
    public function conHora($hora): static
    {
        return $this->state(fn (array $attributes) => [
            'hora_salida' => $hora,
        ]);
    }

    /**
     * Ruta con la misma sucursal origen y destino
     */
    public function mismaSucursal(): static
    {
        return $this->state(function (array $attributes) {
            $sucursales = Sucursal::all();
            if ($sucursales->isEmpty()) {
                throw new \Exception('Se necesitan sucursales para crear rutas');
            }

            $sucursal = $sucursales->random();
            return [
                'sucursal_origen_id' => $sucursal->id,
                'sucursal_destino_id' => $sucursal->id,
            ];
        });
    }

    /**
     * Ruta con sucursales diferentes
     */
    public function sucursalesDiferentes(): static
    {
        return $this->state(function (array $attributes) {
            $sucursales = Sucursal::all();
            if ($sucursales->count() < 2) {
                throw new \Exception('Se necesitan al menos 2 sucursales para crear rutas con sucursales diferentes');
            }

            $sucursalOrigen = $sucursales->random();
            $sucursalDestino = $sucursales->where('id', '!=', $sucursalOrigen->id)->random();

            return [
                'sucursal_origen_id' => $sucursalOrigen->id,
                'sucursal_destino_id' => $sucursalDestino->id,
            ];
        });
    }

    /**
     * Ruta con transportista específico
     */
    public function conTransportista($transportistaId): static
    {
        return $this->state(fn (array $attributes) => [
            'transportista_id' => $transportistaId,
        ]);
    }

    /**
     * Ruta con vehículo específico
     */
    public function conVehiculo($vehiculoId): static
    {
        return $this->state(fn (array $attributes) => [
            'vehiculo_id' => $vehiculoId,
        ]);
    }
}
