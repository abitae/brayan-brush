<?php

namespace Database\Factories\Configuration;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Configuration\Vehiculo>
 */
class VehiculoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomNumber(8, false),
            'marca' => $this->faker->company,
            'modelo' => $this->faker->word,
            'tipo' => $this->faker->randomElement(['INTERNO', 'EXTERNO']),
            'color' => $this->faker->colorName,
            'largo' => $this->faker->randomNumber(2, false),
            'ancho' => $this->faker->randomNumber(2, false),
            'alto' => $this->faker->randomNumber(2, false),
            'pesoBruto' => $this->faker->randomNumber(2, false),
            'pesoNeto' => $this->faker->randomNumber(2, false),
            'mtc' => $this->faker->randomNumber(2, false),
            'placa' => $this->faker->randomNumber(2, false),
            'nroCirculacion' => $this->faker->randomNumber(2, false),
            'codEmisor' => $this->faker->randomNumber(2, false),//codigo de emisor
            'nroAutorizacion' => $this->faker->randomNumber(2, false),//numero de autorizacion
            'isActive' => true,
        ];
    }
}
