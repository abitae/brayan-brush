<?php

namespace Database\Factories\Configuration;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Configuration\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ruc' => $this->faker->unique()->numerify('20##########'),
            'razonSocial' => $this->faker->company,
            'nombreComercial' => $this->faker->company,
            'address' => $this->faker->address,
            'email' => $this->faker->unique()->safeEmail,
            'telephone' => $this->faker->phoneNumber,
            'ubigeo' => $this->faker->postcode,
            'ctaBanco' => $this->faker->bankAccountNumber,
            'pin' => $this->faker->numerify('##########'),
            'nroMtc' => $this->faker->numerify('##########'),
            'logo_path' => $this->faker->imageUrl,
            'sol_user' => $this->faker->userName,
            'sol_pass' => $this->faker->password,
            'cert_path' => $this->faker->imageUrl,
            'client_id' => $this->faker->numerify('##########'),
            'client_secret' => $this->faker->password,
            'production' => $this->faker->boolean,
        ];
    }
}
