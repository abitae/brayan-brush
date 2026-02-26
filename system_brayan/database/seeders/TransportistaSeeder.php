<?php

namespace Database\Seeders;

use App\Models\Configuration\Transportista;
use Illuminate\Database\Seeder;

class TransportistaSeeder extends Seeder
{
    public function run(): void
    {
        $transportistas = [
            [
                'type_code' => '1', // DNI
                'licencia' => 'A1-123456',
                'dni' => '12345678',
                'name' => 'Juan Pérez García',
                'tipo' => 'Chofer',
                'isActive' => true,
            ],
            [
                'type_code' => '1',
                'licencia' => 'A2-234567',
                'dni' => '23456789',
                'name' => 'Carlos Rodríguez López',
                'tipo' => 'Chofer',
                'isActive' => true,
            ],
            [
                'type_code' => '1',
                'licencia' => 'A3-345678',
                'dni' => '34567890',
                'name' => 'Miguel Torres Sánchez',
                'tipo' => 'Chofer',
                'isActive' => true,
            ],
            [
                'type_code' => '1',
                'licencia' => 'A4-456789',
                'dni' => '45678901',
                'name' => 'Luis Fernández Martínez',
                'tipo' => 'Chofer',
                'isActive' => true,
            ],
            [
                'type_code' => '1',
                'licencia' => 'A5-567890',
                'dni' => '56789012',
                'name' => 'Roberto Silva Vargas',
                'tipo' => 'Chofer',
                'isActive' => true,
            ],
        ];

        foreach ($transportistas as $transportista) {
            Transportista::create($transportista);
        }
    }
}
