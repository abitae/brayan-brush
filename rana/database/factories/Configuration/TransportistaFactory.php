<?php

namespace Database\Factories\Configuration;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Configuration\Transportista>
 */
class TransportistaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_code' => $this->faker->unique()->randomNumber(4),
            'licencia' => $this->faker->unique()->randomNumber(8, false),
            'dni' => $this->faker->unique()->randomNumber(8, false),
            'name' => $this->faker->name,
            'tipo' => 'INTERNO',
            'isActive' => true,
        ];
    }
}
