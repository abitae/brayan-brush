<?php

namespace Database\Factories\Caja;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Caja\Caja>
 */
class CajaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'monto_apertura' => $this->faker->randomFloat(2, 100, 1000),
            'monto_cierre' => $this->faker->randomFloat(2, 100, 1000),
            'isActive' => $this->faker->boolean(),
        ];
    }
}
