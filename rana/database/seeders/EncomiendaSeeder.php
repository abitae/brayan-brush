<?php

namespace Database\Seeders;

use App\Models\Configuration\Sucursal;
use App\Models\Configuration\Transportista;
use App\Models\Configuration\Vehiculo;
use App\Models\Package\Customer;
use App\Models\Package\Encomienda;
use App\Models\Package\Paquete;
use App\Models\Package\RutaSucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EncomiendaSeeder extends Seeder
{
    public function run(): void
    {
        $sucursales = Sucursal::orderBy('id')->take(3)->get(); // 3 sucursales
        $customers = Customer::all();
        $transportistas = Transportista::all();
        $vehiculos = Vehiculo::all();
        $users = User::all();
        $rutas = RutaSucursal::all();

        if ($sucursales->count() < 3) {
            $this->command->error('Se necesitan al menos 3 sucursales para crear encomiendas.');
            return;
        }

        if ($customers->count() < 2) {
            $this->command->error('Se necesitan al menos 2 clientes para crear encomiendas.');
            return;
        }

        if ($transportistas->isEmpty()) {
            $this->command->error('Se necesita al menos 1 transportista para crear encomiendas.');
            return;
        }

        if ($vehiculos->isEmpty()) {
            $this->command->error('Se necesita al menos 1 vehículo para crear encomiendas.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('Se necesita al menos 1 usuario para crear encomiendas.');
            return;
        }

        $estadosPago = ['ENVIO PAGADO', 'CONTRA ENTREGA'];
        $tiposPago = ['Contado', 'Credito'];
        $tiposComprobante = ['TICKET', 'BOLETA', 'FACTURA'];
        $metodosPago = ['EFECTIVO', 'YAPE', 'TARJETA', 'TRANSFERENCIA', 'CHEQUE', 'OTRO'];
        $unidadesMedida = ['NIU', 'ZZ', 'KG', 'M2', 'M3'];

        // Obtener las 3 sucursales
        $sucursal1 = $sucursales->get(0);
        $sucursal2 = $sucursales->get(1);
        $sucursal3 = $sucursales->get(2);

        // Fecha actual
        $fechaActual = Carbon::now();

        $this->command->info("Sucursal 1: {$sucursal1->name} (ID: {$sucursal1->id})");
        $this->command->info("Sucursal 2: {$sucursal2->name} (ID: {$sucursal2->id})");
        $this->command->info("Sucursal 3: {$sucursal3->name} (ID: {$sucursal3->id})");
        $this->command->info("Clientes disponibles: {$customers->count()}");
        $this->command->info("Transportistas disponibles: {$transportistas->count()}");
        $this->command->info("Vehículos disponibles: {$vehiculos->count()}");
        $this->command->info("Usuarios disponibles: {$users->count()}");
        $this->command->info("Rutas disponibles: {$rutas->count()}");
        $this->command->info('');

        // Crear 100 encomiendas por cada estado
        $estados = ['REGISTRADO', 'ENVIADO', 'RECIBIDO', 'ENTREGADO'];
        $inicioCodigo = 1;

        foreach ($estados as $estado) {
            $this->command->info("Creando 100 encomiendas {$estado}...");
            $this->crearEncomiendasPorEstado(
                $estado,
                100,
                $sucursales,
                $customers,
                $transportistas,
                $vehiculos,
                $users,
                $rutas,
                $fechaActual,
                $estadosPago,
                $tiposPago,
                $tiposComprobante,
                $metodosPago,
                $unidadesMedida,
                $inicioCodigo
            );
            $inicioCodigo += 100;
        }

        $totalCreadas = Encomienda::whereIn('estado_encomienda', ['REGISTRADO', 'ENVIADO', 'RECIBIDO', 'ENTREGADO'])->count();
        $this->command->info("¡Proceso completado! Total de encomiendas creadas: {$totalCreadas}");
    }

    private function crearEncomiendasPorEstado(
        string $estado,
        int $cantidad,
        $sucursales,
        $customers,
        $transportistas,
        $vehiculos,
        $users,
        $rutas,
        Carbon $fechaActual,
        array $estadosPago,
        array $tiposPago,
        array $tiposComprobante,
        array $metodosPago,
        array $unidadesMedida,
        int $inicioCodigo
    ): void {
        $creadas = 0;
        $errores = 0;
        
        $this->command->info("Iniciando creación de {$cantidad} encomiendas con estado: {$estado}");
        
        for ($i = 0; $i < $cantidad; $i++) {
            try {
                $codigoNumero = $inicioCodigo + $i;
                
                // Verificar que tengamos los datos necesarios
                if ($customers->isEmpty() || $transportistas->isEmpty() || $vehiculos->isEmpty() || $users->isEmpty()) {
                    $this->command->error("Faltan datos necesarios en la iteración {$i}. Deteniendo...");
                    break;
                }

                // Seleccionar sucursales aleatorias (origen y destino diferentes)
                $sucursalOrigen = $sucursales->random();
                $sucursalDestino = $sucursales->where('id', '!=', $sucursalOrigen->id)->random();
                
                $remitente = $customers->random();
                $destinatario = $customers->where('id', '!=', $remitente->id)->random();
                $facturacion = $customers->random();
                $user = $users->random();
                $transportista = $transportistas->random();
                $vehiculo = $vehiculos->random();
            
                $estadoPago = $estadosPago[array_rand($estadosPago)];
                $tipoPago = $estadoPago == 'CONTRA ENTREGA' ? 'Credito' : 'Contado';
                $tipoComprobante = $estadoPago == 'CONTRA ENTREGA' ? 'TICKET' : $tiposComprobante[array_rand($tiposComprobante)];
                $metodoPago = $estadoPago == 'CONTRA ENTREGA' ? null : $metodosPago[array_rand($metodosPago)];
                
                // Lógica correcta: Si es retorno, siempre es domicilio
                // Si es domicilio, no siempre es retorno
                $tipoDistribucion = rand(1, 3);
                
                if ($tipoDistribucion == 3) {
                    // 33% Retorno (siempre es domicilio)
                    $isReturn = true;
                    $isHome = true; // Retorno siempre es entrega a domicilio
                } else {
                    // 67% No retorno: puede ser domicilio o agencia
                    $isReturn = false;
                    $isHome = ($tipoDistribucion == 1); // 50% domicilio, 50% agencia
                }

                // Usar fecha actual para todas las encomiendas
                $fechaCreacion = $fechaActual->copy();

                // Buscar una ruta que coincida con las sucursales
                $ruta = $rutas->where('sucursal_origen_id', $sucursalOrigen->id)
                    ->where('sucursal_destino_id', $sucursalDestino->id)
                    ->where('isActive', true)
                    ->first();
                
                // Si no encuentra una ruta exacta, usar cualquier ruta activa
                if (!$ruta) {
                    $ruta = $rutas->where('isActive', true)->first();
                }
                
                // Si aún no hay rutas, crear una nueva para esta combinación
                if (!$ruta) {
                    $ruta = RutaSucursal::firstOrCreate(
                        [
                            'sucursal_origen_id' => $sucursalOrigen->id,
                            'sucursal_destino_id' => $sucursalDestino->id,
                        ],
                        [
                            'code' => 'RUTA-AUTO-' . str_pad($sucursalOrigen->id . $sucursalDestino->id, 4, '0', STR_PAD_LEFT),
                            'transportista_id' => $transportista->id,
                            'vehiculo_id' => $vehiculo->id,
                            'fecha_salida' => $fechaActual->copy()->addDay(),
                            'hora_salida' => '08:00:00',
                            'dia_semana' => 'LUNES',
                            'estado_ruta' => 'ACTIVA',
                            'observaciones' => 'Ruta creada automáticamente para encomienda',
                            'isActive' => true,
                        ]
                    );
                    // Actualizar la colección de rutas para incluir la nueva
                    if (!$rutas->contains('id', $ruta->id)) {
                        $rutas->push($ruta);
                    }
                }
                
                $rutaId = $ruta->id;

                // Calcular cantidad de paquetes primero
                $numPaquetes = rand(1, 3);
                $montoTotal = 0;
                
                // Calcular monto total antes de crear la encomienda
                for ($k = 1; $k <= $numPaquetes; $k++) {
                    $cantidadPaquete = rand(1, 5);
                    $amount = rand(20, 200) + (rand(0, 99) / 100);
                    $montoTotal += $amount * $cantidadPaquete;
                }

                // Determinar fechas según el estado
                $fechaEnvio = null;
                $fechaRecepcion = null;
                $fechaEntrega = null;
                
                if (in_array($estado, ['ENVIADO', 'RECIBIDO', 'ENTREGADO'])) {
                    $fechaEnvio = $fechaCreacion->copy()->addHours(rand(1, 6));
                }
                
                if (in_array($estado, ['RECIBIDO', 'ENTREGADO'])) {
                    $fechaRecepcion = $fechaEnvio->copy()->addDays(rand(1, 2));
                }
                
                if ($estado === 'ENTREGADO') {
                    $fechaEntrega = $fechaRecepcion->copy()->addHours(rand(1, 24));
                }

                // Generar código único - usar timestamp para asegurar unicidad
                $timestamp = time();
                $codigo = 'ENC-' . str_pad($codigoNumero, 6, '0', STR_PAD_LEFT) . '-' . $timestamp . '-' . $i . '-' . rand(1000, 9999);
                
                // Verificar que el código no exista, si existe generar uno nuevo
                while (Encomienda::where('code', $codigo)->exists()) {
                    $codigo = 'ENC-' . str_pad($codigoNumero, 6, '0', STR_PAD_LEFT) . '-' . microtime(true) . '-' . $i . '-' . rand(10000, 99999);
                }

                // Determinar tipo de entrega para la observación
                $tipoEntrega = $isHome ? 'Entrega a domicilio' : ($isReturn ? 'Retorno' : 'Recojo en agencia');

                $encomienda = Encomienda::create([
                    'code' => $codigo,
                    'sucursal_id' => $sucursalOrigen->id, // Sucursal remitente
                    'sucursal_dest_id' => $sucursalDestino->id, // Sucursal destinatario
                    'user_id' => $user->id,
                    'transportista_id' => $transportista->id,
                    'vehiculo_id' => $vehiculo->id,
                    'customer_id' => $remitente->id,
                    'customer_dest_id' => $destinatario->id,
                    'customer_fact_id' => $facturacion->id,
                    'cantidad' => $numPaquetes,
                    'monto' => $montoTotal,
                    'monto_descuento' => rand(0, 10) < 2 ? round(rand(5, 50), 2) : 0, // 20% tienen descuento
                    'motivo_descuento' => rand(0, 10) < 2 ? 'Descuento promocional' : null,
                    'estado_encomienda' => $estado,
                    'estado_credito' => $tipoPago == 'Contado' ? 'Cancelado' : 'Pendiente',
                    'estado_pago' => $estadoPago,
                    'tipo_pago' => $tipoPago,
                    'tipo_comprobante' => $tipoComprobante,
                    'metodo_pago' => $metodoPago,
                    'pin' => 123, // PIN fijo para todas las encomiendas
                    'glosa' => 'Encomienda de prueba generada automáticamente',
                    'observation' => "Encomienda de prueba #{$codigoNumero} - {$tipoEntrega}",
                    'fecha_creacion' => $fechaCreacion,
                    'fecha_envio' => $fechaEnvio,
                    'fecha_recepcion' => $fechaRecepcion,
                    'fecha_entrega' => $fechaEntrega,
                    'fecha_retorno' => null,
                    'docsTraslado' => json_encode([]),
                    'isTransbordo' => false,
                    'isHome' => $isHome,
                    'direccion_envio' => $isHome ? 'Dirección de entrega ' . rand(100, 999) . ', ' . $sucursalDestino->name : null,
                    'isReturn' => $isReturn,
                    'isActive' => true,
                    'ruta_id' => $rutaId,
                    'created_at' => $fechaCreacion,
                    'updated_at' => $fechaCreacion,
                ]);

                // Crear paquetes para la encomienda
                $descripciones = [
                    'Documentos importantes',
                    'Ropa y accesorios',
                    'Electrodomésticos',
                    'Productos frágiles',
                    'Mercancía general',
                    'Equipos electrónicos',
                    'Alimentos perecederos',
                    'Material de oficina',
                    'Herramientas',
                    'Productos farmacéuticos',
                ];

                for ($j = 1; $j <= $numPaquetes; $j++) {
                    $cantidadPaquete = rand(1, 5);
                    $peso = round(rand(1, 50) + (rand(0, 99) / 100), 2);
                    $amount = round(rand(20, 200) + (rand(0, 99) / 100), 2);
                    $subTotal = round($amount * $cantidadPaquete, 2);
                    $unidadMedida = $unidadesMedida[array_rand($unidadesMedida)];
                    $descripcion = $descripciones[array_rand($descripciones)] . ' - Paquete ' . $j;

                    Paquete::create([
                        'encomienda_id' => $encomienda->id,
                        'cantidad' => $cantidadPaquete,
                        'und_medida' => $unidadMedida,
                        'description' => $descripcion,
                        'peso' => $peso,
                        'amount' => $amount,
                        'sub_total' => $subTotal,
                    ]);
                }
                
                $creadas++;
                
                // Mostrar progreso cada 10 encomiendas
                if ($creadas % 10 == 0) {
                    $this->command->info("Creadas {$creadas}/{$cantidad} encomiendas {$estado}...");
                }
            } catch (\Illuminate\Database\QueryException $e) {
                $errores++;
                $this->command->error("Error de base de datos al crear encomienda #{$codigoNumero} (Estado: {$estado}): " . $e->getMessage());
                if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE constraint')) {
                    $this->command->warn("Código duplicado detectado, intentando con código alternativo...");
                    // Intentar una vez más con un código diferente
                    try {
                        $codigoAlternativo = 'ENC-' . microtime(true) . '-' . $codigoNumero . '-' . rand(100000, 999999);
                        
                        // Seleccionar sucursales aleatorias nuevamente
                        $sucursalOrigen = $sucursales->random();
                        $sucursalDestino = $sucursales->where('id', '!=', $sucursalOrigen->id)->random();
                        
                        // Lógica correcta: Si es retorno, siempre es domicilio
                        $tipoDistribucion = rand(1, 3);
                        
                        if ($tipoDistribucion == 3) {
                            // Retorno (siempre es domicilio)
                            $isReturn = true;
                            $isHome = true;
                        } else {
                            // No retorno: puede ser domicilio o agencia
                            $isReturn = false;
                            $isHome = ($tipoDistribucion == 1);
                        }
                        
                        $tipoEntrega = $isHome ? ($isReturn ? 'Retorno' : 'Entrega a domicilio') : 'Recojo en agencia';
                        
                        $encomienda = Encomienda::create([
                            'code' => $codigoAlternativo,
                            'sucursal_id' => $sucursalOrigen->id,
                            'sucursal_dest_id' => $sucursalDestino->id,
                            'user_id' => $user->id,
                            'transportista_id' => $transportista->id,
                            'vehiculo_id' => $vehiculo->id,
                            'customer_id' => $remitente->id,
                            'customer_dest_id' => $destinatario->id,
                            'customer_fact_id' => $facturacion->id,
                            'cantidad' => $numPaquetes,
                            'monto' => $montoTotal,
                            'monto_descuento' => rand(0, 10) < 2 ? round(rand(5, 50), 2) : 0,
                            'motivo_descuento' => rand(0, 10) < 2 ? 'Descuento promocional' : null,
                            'estado_encomienda' => $estado,
                            'estado_credito' => $tipoPago == 'Contado' ? 'Cancelado' : 'Pendiente',
                            'estado_pago' => $estadoPago,
                            'tipo_pago' => $tipoPago,
                            'tipo_comprobante' => $tipoComprobante,
                            'metodo_pago' => $metodoPago,
                            'pin' => 123,
                            'glosa' => 'Encomienda de prueba generada automáticamente',
                            'observation' => "Encomienda de prueba #{$codigoNumero} - {$tipoEntrega}",
                            'fecha_creacion' => $fechaCreacion,
                            'fecha_envio' => $fechaEnvio,
                            'fecha_recepcion' => $fechaRecepcion,
                            'fecha_entrega' => $fechaEntrega,
                            'fecha_retorno' => null,
                            'docsTraslado' => json_encode([]),
                            'isTransbordo' => false,
                            'isHome' => $isHome,
                            'direccion_envio' => $isHome ? 'Dirección de entrega ' . rand(100, 999) . ', ' . $sucursalDestino->name : null,
                            'isReturn' => $isReturn,
                            'isActive' => true,
                            'ruta_id' => $rutaId,
                            'created_at' => $fechaCreacion,
                            'updated_at' => $fechaCreacion,
                        ]);
                        
                        // Crear paquetes
                        $descripcionesPaquetes = [
                            'Documentos importantes',
                            'Ropa y accesorios',
                            'Electrodomésticos',
                            'Productos frágiles',
                            'Mercancía general',
                            'Equipos electrónicos',
                            'Alimentos perecederos',
                            'Material de oficina',
                            'Herramientas',
                            'Productos farmacéuticos',
                        ];
                        
                        for ($j = 1; $j <= $numPaquetes; $j++) {
                            $cantidadPaquete = rand(1, 5);
                            $peso = round(rand(1, 50) + (rand(0, 99) / 100), 2);
                            $amount = round(rand(20, 200) + (rand(0, 99) / 100), 2);
                            $subTotal = round($amount * $cantidadPaquete, 2);
                            $unidadMedida = $unidadesMedida[array_rand($unidadesMedida)];
                            $descripcion = $descripcionesPaquetes[array_rand($descripcionesPaquetes)] . ' - Paquete ' . $j;
                            
                            Paquete::create([
                                'encomienda_id' => $encomienda->id,
                                'cantidad' => $cantidadPaquete,
                                'und_medida' => $unidadMedida,
                                'description' => $descripcion,
                                'peso' => $peso,
                                'amount' => $amount,
                                'sub_total' => $subTotal,
                            ]);
                        }
                        
                        $creadas++;
                        if ($creadas % 10 == 0) {
                            $this->command->info("Creadas {$creadas}/{$cantidad} encomiendas {$estado}...");
                        }
                    } catch (\Exception $e2) {
                        $this->command->error("Error al reintentar: " . $e2->getMessage());
                    }
                }
                continue;
            } catch (\Exception $e) {
                $errores++;
                $this->command->error("Error al crear encomienda #{$codigoNumero} (Estado: {$estado}): " . $e->getMessage());
                $this->command->error("Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
                // Continuar con la siguiente encomienda
                continue;
            }
        }
        
        $this->command->info("Estado {$estado}: {$creadas} creadas, {$errores} errores");
    }
}
