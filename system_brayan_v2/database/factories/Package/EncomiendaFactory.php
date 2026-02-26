<?php

namespace Database\Factories\Package;

use App\Models\Configuration\Sucursal;
use App\Models\Configuration\Transportista;
use App\Models\Configuration\Vehiculo;
use App\Models\Package\Customer;
use App\Models\Package\RutaSucursal;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package\Encomienda>
 */
class EncomiendaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'ENC-' . $this->faker->unique()->numerify('#####'),
            'user_id' => User::inRandomOrder()->first()->id,
            'transportista_id' => Transportista::inRandomOrder()->first()->id,
            'vehiculo_id' => Vehiculo::inRandomOrder()->first()->id,
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'sucursal_id' => Sucursal::inRandomOrder()->first()->id,
            'customer_dest_id' => Customer::inRandomOrder()->first()->id,
            'sucursal_dest_id' => Sucursal::inRandomOrder()->first()->id,
            'customer_fact_id' => Customer::inRandomOrder()->first()->id,
            'cantidad' => 0, // Se actualizará con el número real de paquetes
            'monto' => 0, // Se calculará basado en la suma de los paquetes
            'monto_descuento' => $this->faker->optional(0.3)->randomFloat(2, 5, 500),
            'motivo_descuento' => $this->faker->optional(0.3)->sentence(),
            'doc_ticket' => null,
            'doc_guia' => null,
            'doc_factura' => null,
            'fecha_creacion' => now(),
            'fecha_envio' => null,
            'fecha_recepcion' => null,
            'fecha_entrega' => null,
            'fecha_retorno' => null,
            'estado_pago' => $this->faker->randomElement(['PENDIENTE', 'PAGADO', 'CONTRA_ENTREGA']),
            'tipo_pago' => $this->faker->randomElement(['CONTADO', 'CREDITO']),
            'metodo_pago' => $this->faker->randomElement(['EFECTIVO', 'YAPE', 'TARJETA', 'CHEQUE', 'TRANSFERENCIA', 'OTRO']),
            'tipo_comprobante' => $this->faker->randomElement(['TICKET', 'FACTURA', 'BOLETA']),
            'estado_credito' => $this->faker->randomElement(['PENDIENTE', 'CANCELADO']),
            'docsTraslado' => json_encode([]),
            'glosa' => $this->faker->sentence(),
            'observation' => $this->faker->optional(0.5)->sentence(),
            'estado_encomienda' => $this->faker->randomElement(['REGISTRADO']),
            'pin' => $this->faker->numberBetween(100, 999),
            'isTransbordo' => $this->faker->boolean(20), // 20% chance of being true
            'isHome' => $this->faker->boolean(30), // 30% chance of being true
            'direccion_envio' => $this->faker->optional(0.4)->address(),
            'isReturn' => $this->faker->boolean(10), // 10% chance of being true
            'isActive' => true, // Siempre activo por defecto
            'ruta_id' => RutaSucursal::inRandomOrder()->first()?->id,
        ];
    }
}
