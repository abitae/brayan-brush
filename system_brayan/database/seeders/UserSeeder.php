<?php

namespace Database\Seeders;

use App\Models\Configuration\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $sucursales = Sucursal::all();

        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Administrador',
            'email' => 'abel.arana@hotmail.com',
            'sucursal_id' => $sucursales->first()->id,
            'isActive' => true,
            'password' => Hash::make('lobomalo123'),
        ]);
        $superAdmin->assignRole('SuperAdmin');

        // Administradores por sucursal
        foreach ($sucursales as $index => $sucursal) {
            $admin = User::create([
                'name' => 'Administrador ' . $sucursal->name,
                'email' => 'admin.' . strtolower($sucursal->code) . '@transportes.com',
                'sucursal_id' => $sucursal->id,
                'isActive' => true,
                'password' => Hash::make('password123'),
            ]);
            $admin->assignRole('Admin sucursal');

            // Usuarios operativos
            $operativo = User::create([
                'name' => 'Operador ' . $sucursal->name,
                'email' => 'operador.' . strtolower($sucursal->code) . '@transportes.com',
                'sucursal_id' => $sucursal->id,
                'isActive' => true,
                'password' => Hash::make('password123'),
            ]);
            $operativo->assignRole('Admin sucursal'); // Puedes crear un rol "Operador" si lo necesitas
        }

        // Usuario de prueba para desarrollo
        $testUser = User::create([
            'name' => 'Usuario Prueba',
            'email' => 'test@transportes.com',
            'sucursal_id' => $sucursales->first()->id,
            'isActive' => true,
            'password' => Hash::make('test123'),
        ]);
        $testUser->assignRole('Admin sucursal');
    }
}

