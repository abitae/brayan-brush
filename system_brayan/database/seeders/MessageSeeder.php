<?php

namespace Database\Seeders;

use App\Models\Frontend\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        if (Message::query()->exists()) {
            return;
        }

        $mensajes = [
            [
                'name' => 'Ana Perez',
                'email' => 'ana.perez@correo.com',
                'phone' => '987654001',
                'select' => 'Consulta',
                'message' => 'Quisiera saber los horarios de envio a provincias.',
                'isActive' => true,
            ],
            [
                'name' => 'Carlos Medina',
                'email' => 'carlos.medina@correo.com',
                'phone' => '987654002',
                'select' => 'Cotizacion',
                'message' => 'Necesito cotizar el envio de 3 cajas a Arequipa.',
                'isActive' => true,
            ],
            [
                'name' => 'Lucia Ramirez',
                'email' => 'lucia.ramirez@correo.com',
                'phone' => '987654003',
                'select' => 'Reclamo',
                'message' => 'Mi encomienda llego con retraso, solicito informacion.',
                'isActive' => true,
            ],
            [
                'name' => 'Mario Salas',
                'email' => 'mario.salas@correo.com',
                'phone' => '987654004',
                'select' => 'Sugerencia',
                'message' => 'Seria ideal habilitar seguimiento en tiempo real.',
                'isActive' => true,
            ],
        ];

        foreach ($mensajes as $mensaje) {
            Message::create($mensaje);
        }
    }
}
