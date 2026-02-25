<?php

namespace Database\Seeders;

use App\Models\Configuration\Sucursal;
use App\Models\Configuration\Transportista;
use App\Models\Configuration\Vehiculo;
use App\Models\Package\RutaSucursal;
use Illuminate\Database\Seeder;

class RutaSucursalSeeder extends Seeder
{
    public function run(): void
    {
        $sucursales = Sucursal::all();
        $transportistas = Transportista::all();
        $vehiculos = Vehiculo::all();

        if ($sucursales->count() < 2) {
            return;
        }

        // Crear rutas entre sucursales principales
        $rutas = [
            [
                'code' => 'RUTA-' . str_pad(1, 4, '0', STR_PAD_LEFT),
                'sucursal_origen_id' => $sucursales[0]->id, // Lima Centro
                'sucursal_destino_id' => $sucursales[1]->id, // Lima Norte
                'transportista_id' => $transportistas->first()->id,
                'vehiculo_id' => $vehiculos->first()->id,
                'fecha_salida' => now()->addDay()->format('Y-m-d'),
                'hora_salida' => '08:00:00',
                'dia_semana' => 'LUNES',
                'estado_ruta' => 'ACTIVA',
                'observaciones' => 'Ruta diaria Lima Centro - Lima Norte',
                'isActive' => true,
            ],
            [
                'code' => 'RUTA-' . str_pad(2, 4, '0', STR_PAD_LEFT),
                'sucursal_origen_id' => $sucursales[0]->id, // Lima Centro
                'sucursal_destino_id' => $sucursales[2]->id, // Lima Sur
                'transportista_id' => $transportistas->skip(1)->first()->id,
                'vehiculo_id' => $vehiculos->skip(1)->first()->id,
                'fecha_salida' => now()->addDay()->format('Y-m-d'),
                'hora_salida' => '09:00:00',
                'dia_semana' => 'MARTES',
                'estado_ruta' => 'ACTIVA',
                'observaciones' => 'Ruta semanal Lima Centro - Lima Sur',
                'isActive' => true,
            ],
            [
                'code' => 'RUTA-' . str_pad(3, 4, '0', STR_PAD_LEFT),
                'sucursal_origen_id' => $sucursales[0]->id, // Lima Centro
                'sucursal_destino_id' => $sucursales[3]->id, // Arequipa
                'transportista_id' => $transportistas->skip(2)->first()->id,
                'vehiculo_id' => $vehiculos->skip(2)->first()->id,
                'fecha_salida' => now()->addDays(2)->format('Y-m-d'),
                'hora_salida' => '06:00:00',
                'dia_semana' => 'MIERCOLES',
                'estado_ruta' => 'ACTIVA',
                'observaciones' => 'Ruta interprovincial Lima - Arequipa',
                'isActive' => true,
            ],
            [
                'code' => 'RUTA-' . str_pad(4, 4, '0', STR_PAD_LEFT),
                'sucursal_origen_id' => $sucursales[0]->id, // Lima Centro
                'sucursal_destino_id' => $sucursales[4]->id, // Trujillo
                'transportista_id' => $transportistas->skip(3)->first()->id,
                'vehiculo_id' => $vehiculos->skip(3)->first()->id,
                'fecha_salida' => now()->addDays(3)->format('Y-m-d'),
                'hora_salida' => '07:00:00',
                'dia_semana' => 'JUEVES',
                'estado_ruta' => 'ACTIVA',
                'observaciones' => 'Ruta interprovincial Lima - Trujillo',
                'isActive' => true,
            ],
            [
                'code' => 'RUTA-' . str_pad(5, 4, '0', STR_PAD_LEFT),
                'sucursal_origen_id' => $sucursales[1]->id, // Lima Norte
                'sucursal_destino_id' => $sucursales[2]->id, // Lima Sur
                'transportista_id' => $transportistas->first()->id,
                'vehiculo_id' => $vehiculos->first()->id,
                'fecha_salida' => now()->addDay()->format('Y-m-d'),
                'hora_salida' => '10:00:00',
                'dia_semana' => 'VIERNES',
                'estado_ruta' => 'ACTIVA',
                'observaciones' => 'Ruta Lima Norte - Lima Sur',
                'isActive' => true,
            ],
        ];

        foreach ($rutas as $ruta) {
            RutaSucursal::create($ruta);
        }
    }
}
