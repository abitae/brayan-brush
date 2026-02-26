<?php

namespace Database\Factories\Configuration;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Configuration\Sucursal>
 */
class SucursalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->randomNumber(4),
            'codeSunat' => $this->faker->unique()->randomNumber(4),
            'igv' => $this->faker->randomFloat(2, 0, 100),
            'serieFactura' => $this->faker->unique()->randomNumber(4),
            'serieBoleta' => $this->faker->unique()->randomNumber(4),
            'serieGuiaRemision' => $this->faker->unique()->randomNumber(4),
            'serieNotaCreditoFactura' => $this->faker->unique()->randomNumber(4),
            'serieNotaCreditoBoleta' => $this->faker->unique()->randomNumber(4),
            'serieNotaDebitoFactura' => $this->faker->unique()->randomNumber(4),
            'serieNotaDebitoBoleta' => $this->faker->unique()->randomNumber(4),
            'color' => $this->faker->hexColor(),
            'name' => $this->faker->company(),
            'departamento' => $this->faker->state(),
            'provincia' => $this->faker->city(),
            'distrito' => $this->faker->city(),
            'urbanizacion' => $this->faker->city(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'ubigeo' => $this->faker->postcode(),
            'isActive' => true,

        ];
    }
}
