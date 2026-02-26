<?php

namespace Database\Factories\Package;

use App\Models\Package\Encomienda;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package\Paquete>
 */
class PaqueteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cantidad = $this->faker->numberBetween(1, 10);
        $amount = $this->faker->randomFloat(2, 5, 200);

        return [
            'encomienda_id' => null, // Se establecerá al crear el paquete
            'cantidad' => $cantidad,
            'und_medida' => $this->faker->randomElement(['UND', 'M3', 'KG', 'LT', 'CAJA', 'BOLSA']),
            'description' => $this->faker->randomElement([
                'Documentos importantes',
                'Ropa y textiles',
                'Electrónicos',
                'Alimentos perecibles',
                'Herramientas',
                'Libros y papelería',
                'Juguetes',
                'Cosméticos',
                'Medicamentos',
                'Repuestos automotrices',
                'Productos de limpieza',
                'Artículos deportivos'
            ]),
            'peso' => $this->faker->randomFloat(2, 0.1, 50),
            'amount' => $amount,
            'sub_total' => $cantidad * $amount,
        ];
    }
}
