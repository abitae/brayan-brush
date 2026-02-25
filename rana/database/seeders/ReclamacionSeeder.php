<?php

namespace Database\Seeders;

use App\Models\Frontend\Reclamacion;
use Illuminate\Database\Seeder;

class ReclamacionSeeder extends Seeder
{
    public function run(): void
    {
        if (Reclamacion::query()->exists()) {
            return;
        }

        $reclamos = [
            [
                'reclamo_nombre' => 'Juan Torres',
                'reclamo_documento' => '45678912',
                'reclamo_telefono' => '987650001',
                'reclamo_email' => 'juan.torres@correo.com',
                'reclamo_direccion' => 'Av. Los Pinos 123',
                'reclamo_tipo' => 'Queja',
                'reclamo_producto' => 'Servicio de envio',
                'reclamo_monto' => '120.00',
                'reclamo_descripcion' => 'Retraso en la entrega de encomienda.',
                'reclamo_politicas' => 'Acepto politicas',
                'isActive' => true,
            ],
            [
                'reclamo_nombre' => 'Maria Silva',
                'reclamo_documento' => '78912345',
                'reclamo_telefono' => '987650002',
                'reclamo_email' => 'maria.silva@correo.com',
                'reclamo_direccion' => 'Jr. Los Olivos 456',
                'reclamo_tipo' => 'Reclamo',
                'reclamo_producto' => 'Encomienda',
                'reclamo_monto' => '80.50',
                'reclamo_descripcion' => 'Paquete llego con danos visibles.',
                'reclamo_politicas' => 'Acepto politicas',
                'isActive' => true,
            ],
        ];

        foreach ($reclamos as $reclamo) {
            Reclamacion::create($reclamo);
        }
    }
}
