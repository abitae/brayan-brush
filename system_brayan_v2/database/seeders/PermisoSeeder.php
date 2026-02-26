<?php

namespace Database\Seeders;

use App\Models\Configuration\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'caja.index',
            'config.ruta',
            'menu.encomienda',
            'menu.entrega',
            'menu.facturacion',
            'menu.reporte',
            'menu.configuracion',
            'config.sucursal',
            'config.vehiculo',
            'config.transportista',
            'config.user',
            'config.role',
            'config.company',
            'report.encomienda',
            'package.customer',
            'package.register',
            'package.send',
            'package.receive',
            'package.deliver',
            'package.record',
            'package.home',
            'package.return',
            'package.manifiesto',
            'message.frontend',
            'reclamaciones.frontend',
            'facturacion.ticket',
            'facturacion.invoice',
            'facturacion.despache',
            'facturacion.note',
            'facturacion.create-invoice',
            'facturacion.create-note',
            'tutorial.video'
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }
        
        // Crear roles
        $roleSuperAdmin = Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $roleSuperAdmin->syncPermissions(Permission::all());
        
        $roleAdministrador = Role::create(['name' => 'Administrador', 'guard_name' => 'web']);
        $roleAdministrador->syncPermissions(Permission::all());
        
        $roleAdminSucursal = Role::create(['name' => 'Admin sucursal', 'guard_name' => 'web']);
        $roleAdminSucursal->syncPermissions([
            'caja.index',
            'config.ruta',
            'menu.encomienda',
            'menu.entrega',
            'menu.facturacion',
            'menu.reporte',
            'report.encomienda',
            'package.customer',
            'package.register',
            'package.send',
            'package.receive',
            'package.deliver',
            'package.record',
            'package.home',
            'package.return',
            'package.manifiesto',
            'message.frontend',
            'reclamaciones.frontend',
            'facturacion.ticket',
            'facturacion.invoice',
            'facturacion.despache',
            'facturacion.note',
            'facturacion.create-invoice',
            'facturacion.create-note',
            'tutorial.video'
        ]);

        // Crear rol Operador
        $roleOperador = Role::create(['name' => 'Operador', 'guard_name' => 'web']);
        $roleOperador->syncPermissions([
            'caja.index',
            'menu.encomienda',
            'menu.entrega',
            'package.register',
            'package.send',
            'package.receive',
            'package.deliver',
            'package.record',
            'package.home',
            'facturacion.ticket',
        ]);
    }
}

