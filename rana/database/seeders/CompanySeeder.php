<?php

namespace Database\Seeders;

use App\Models\Configuration\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'ruc' => '20612345678',
            'razonSocial' => 'CORPORACIÓN LOGÍSTICA BRAYAN BRUSH EIRL',
            'nombreComercial' => 'TRANSPORTES BRAYAN BRUSH',
            'address' => 'Av. Principal 123, Lima',
            'email' => 'contacto@transportes.com',
            'telephone' => '01-1234567',
            'ubigeo' => '150101',
            'ctaBanco' => '0011-1234-56789012-34',
            'pin' => '123456',
            'nroMtc' => 'MTC-123456',
            'logo_path' => null,
            'sol_user' => 'MODDATOS',
            'sol_pass' => 'moddatos',
            'cert_path' => '', // Ruta al certificado (vacío para pruebas)
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'production' => false, // Modo pruebas
        ]);
    }
}
