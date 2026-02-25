<?php

namespace Database\Factories\Caja;

use App\Models\Configuration\TipoExitCaja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Caja\ExitCaja>
 */
class ExitCajaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'caja_id' => \App\Models\Caja\Caja::factory(),
            'monto_exit' => $this->faker->randomFloat(2, 10, 500),
            'description' => $this->faker->sentence(),
            'metodo_pago' => $this->faker->randomElement(['efectivo', 'tarjeta', 'transferencia']),
            'tipo_exit_id' => TipoExitCaja::inRandomOrder()->first()?->id ?? TipoExitCaja::factory(),
        ];
    }
}
