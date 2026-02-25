<?php

namespace Database\Seeders;

use App\Models\Package\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Clientes con DNI (Personas naturales)
        $clientesDNI = [
            ['type_code' => '1', 'code' => '12345678', 'name' => 'Juan Carlos Pérez García', 'phone' => '987654321', 'email' => 'juan.perez@email.com', 'address' => 'Av. Principal 123, Lima', 'ubigeo' => '150101', 'texto_ubigeo' => 'Lima - Lima - Cercado de Lima', 'isActive' => true],
            ['type_code' => '1', 'code' => '23456789', 'name' => 'María Elena Rodríguez López', 'phone' => '987654322', 'email' => 'maria.rodriguez@email.com', 'address' => 'Jr. Los Olivos 456', 'ubigeo' => '150135', 'texto_ubigeo' => 'Lima - Lima - San Martín de Porres', 'isActive' => true],
            ['type_code' => '1', 'code' => '34567890', 'name' => 'Carlos Alberto Torres Sánchez', 'phone' => '987654323', 'email' => 'carlos.torres@email.com', 'address' => 'Av. Brasil 789', 'ubigeo' => '150101', 'texto_ubigeo' => 'Lima - Lima - Cercado de Lima', 'isActive' => true],
            ['type_code' => '1', 'code' => '45678901', 'name' => 'Ana Lucía Fernández Martínez', 'phone' => '987654324', 'email' => 'ana.fernandez@email.com', 'address' => 'Av. Javier Prado 321', 'ubigeo' => '150142', 'texto_ubigeo' => 'Lima - Lima - Villa El Salvador', 'isActive' => true],
            ['type_code' => '1', 'code' => '56789012', 'name' => 'Roberto Silva Vargas', 'phone' => '987654325', 'email' => 'roberto.silva@email.com', 'address' => 'Jr. Unión 654', 'ubigeo' => '150101', 'texto_ubigeo' => 'Lima - Lima - Cercado de Lima', 'isActive' => true],
        ];

        // Clientes con RUC (Empresas)
        $clientesRUC = [
            ['type_code' => '6', 'code' => '20123456789', 'name' => 'EMPRESA COMERCIAL SAC', 'phone' => '01-1234567', 'email' => 'ventas@empresa.com', 'address' => 'Av. Industrial 1234', 'ubigeo' => '150101', 'texto_ubigeo' => 'Lima - Lima - Cercado de Lima', 'isActive' => true],
            ['type_code' => '6', 'code' => '20234567890', 'name' => 'DISTRIBUIDORA PERÚ EIRL', 'phone' => '01-2345678', 'email' => 'contacto@distribuidora.com', 'address' => 'Av. Argentina 5678', 'ubigeo' => '150135', 'texto_ubigeo' => 'Lima - Lima - San Martín de Porres', 'isActive' => true],
            ['type_code' => '6', 'code' => '20345678901', 'name' => 'IMPORTADORA ANDINA S.A.', 'phone' => '01-3456789', 'email' => 'info@importadora.com', 'address' => 'Jr. Lampa 9012', 'ubigeo' => '150101', 'texto_ubigeo' => 'Lima - Lima - Cercado de Lima', 'isActive' => true],
            ['type_code' => '6', 'code' => '20456789012', 'name' => 'LOGÍSTICA INTEGRAL SAC', 'phone' => '01-4567890', 'email' => 'servicio@logistica.com', 'address' => 'Av. Ejército 3456', 'ubigeo' => '040101', 'texto_ubigeo' => 'Arequipa - Arequipa - Yanahuara', 'isActive' => true],
            ['type_code' => '6', 'code' => '20567890123', 'name' => 'TRANSPORTES DEL NORTE S.A.C.', 'phone' => '044-123456', 'email' => 'contacto@transnorte.com', 'address' => 'Jr. Pizarro 7890', 'ubigeo' => '130101', 'texto_ubigeo' => 'La Libertad - Trujillo - Trujillo', 'isActive' => true],
        ];

        // Crear clientes DNI
        foreach ($clientesDNI as $cliente) {
            Customer::create($cliente);
        }

        // Crear clientes RUC
        foreach ($clientesRUC as $cliente) {
            Customer::create($cliente);
        }

        // Crear clientes adicionales aleatorios para pruebas
        for ($i = 0; $i < 20; $i++) {
            $tipo = rand(0, 1) ? '1' : '6';
            $code = $tipo == '1' ? str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT) : '20' . str_pad(rand(10000000, 99999999), 9, '0', STR_PAD_LEFT);
            
            Customer::create([
                'type_code' => $tipo,
                'code' => $code,
                'name' => 'Cliente Prueba ' . ($i + 1),
                'phone' => '9' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'email' => 'cliente' . ($i + 1) . '@test.com',
                'address' => 'Dirección ' . ($i + 1),
                'ubigeo' => '150101',
                'texto_ubigeo' => 'Lima - Lima - Cercado de Lima',
                'isActive' => true,
            ]);
        }
    }
}
