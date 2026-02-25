<?php

namespace Database\Seeders;

use App\Models\Configuration\Vehiculo;
use Illuminate\Database\Seeder;

class VehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $vehiculos = [
            [
                'name' => 'Camión 01',
                'marca' => 'Mercedes Benz',
                'modelo' => 'Actros 2644',
                'tipo' => 'Camión',
                'color' => 'Blanco',
                'largo' => 12.0,
                'ancho' => 2.5,
                'alto' => 3.5,
                'pesoBruto' => 26000,
                'pesoNeto' => 18000,
                'mtc' => 'MTC-001',
                'placa' => 'ABC-123',
                'nroCirculacion' => 'CIR-001',
                'codEmisor' => '01',
                'nroAutorizacion' => 'AUT-001',
                'isActive' => true,
            ],
            [
                'name' => 'Camión 02',
                'marca' => 'Volvo',
                'modelo' => 'FH 460',
                'tipo' => 'Camión',
                'color' => 'Azul',
                'largo' => 11.5,
                'ancho' => 2.5,
                'alto' => 3.4,
                'pesoBruto' => 25000,
                'pesoNeto' => 17000,
                'mtc' => 'MTC-002',
                'placa' => 'DEF-456',
                'nroCirculacion' => 'CIR-002',
                'codEmisor' => '01',
                'nroAutorizacion' => 'AUT-002',
                'isActive' => true,
            ],
            [
                'name' => 'Furgón 01',
                'marca' => 'Nissan',
                'modelo' => 'Urvan',
                'tipo' => 'Furgón',
                'color' => 'Rojo',
                'largo' => 4.5,
                'ancho' => 1.8,
                'alto' => 2.0,
                'pesoBruto' => 3500,
                'pesoNeto' => 2500,
                'mtc' => 'MTC-003',
                'placa' => 'GHI-789',
                'nroCirculacion' => 'CIR-003',
                'codEmisor' => '01',
                'nroAutorizacion' => 'AUT-003',
                'isActive' => true,
            ],
            [
                'name' => 'Furgón 02',
                'marca' => 'Toyota',
                'modelo' => 'Hiace',
                'tipo' => 'Furgón',
                'color' => 'Gris',
                'largo' => 4.6,
                'ancho' => 1.9,
                'alto' => 2.1,
                'pesoBruto' => 3600,
                'pesoNeto' => 2600,
                'mtc' => 'MTC-004',
                'placa' => 'JKL-012',
                'nroCirculacion' => 'CIR-004',
                'codEmisor' => '01',
                'nroAutorizacion' => 'AUT-004',
                'isActive' => true,
            ],
            [
                'name' => 'Camión 03',
                'marca' => 'Scania',
                'modelo' => 'R 450',
                'tipo' => 'Camión',
                'color' => 'Negro',
                'largo' => 12.5,
                'ancho' => 2.6,
                'alto' => 3.6,
                'pesoBruto' => 28000,
                'pesoNeto' => 20000,
                'mtc' => 'MTC-005',
                'placa' => 'MNO-345',
                'nroCirculacion' => 'CIR-005',
                'codEmisor' => '01',
                'nroAutorizacion' => 'AUT-005',
                'isActive' => true,
            ],
        ];

        foreach ($vehiculos as $vehiculo) {
            Vehiculo::create($vehiculo);
        }
    }
}
