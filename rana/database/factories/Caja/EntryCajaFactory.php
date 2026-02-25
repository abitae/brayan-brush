<?php

namespace Database\Factories\Caja;

use App\Models\Configuration\TipoEntryCaja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Caja\EntryCaja>
 */
class EntryCajaFactory extends Factory
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
            'monto_entry' => $this->faker->randomFloat(2, 10, 500),
            'description' => $this->faker->sentence(),
            'metodo_pago' => $this->faker->randomElement(['efectivo', 'tarjeta', 'transferencia']),
            'tipo_entry_id' => TipoEntryCaja::inRandomOrder()->first()?->id ?? TipoEntryCaja::factory(),
        ];
    }
}
