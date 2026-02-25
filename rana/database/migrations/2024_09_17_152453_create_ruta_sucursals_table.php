<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ruta_sucursals', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            // Sucursales
            $table->foreignId('sucursal_origen_id')->constrained('sucursals')->onDelete('cascade');
            $table->foreignId('sucursal_destino_id')->constrained('sucursals')->onDelete('cascade');

            // Transportista y vehículo
            $table->foreignId('transportista_id')->constrained('transportistas')->onDelete('cascade');
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');

            // Fechas y horarios
            $table->date('fecha_salida');
            $table->time('hora_salida');
                        $table->enum('dia_semana', ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO']);

            // Estado
            $table->enum('estado_ruta', ['ACTIVA', 'INACTIVA', 'SUSPENDIDA', 'COMPLETADO'])->default('ACTIVA');
            $table->boolean('isActive')->default(true);

            // Observaciones
            $table->text('observaciones')->nullable();

            // Timestamps
            $table->timestamps();

            // Índices
            $table->index(['sucursal_origen_id', 'sucursal_destino_id']);
            $table->index(['estado_ruta', 'isActive']);
            $table->index('dia_semana');
            $table->index('fecha_salida');

            // Restricción única: transportista y vehículo únicos por ruta (permite múltiples rutas con misma sucursal origen-destino)
            $table->unique(['sucursal_origen_id', 'sucursal_destino_id', 'transportista_id', 'vehiculo_id'], 'unique_ruta_transportista_vehiculo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruta_sucursals');
    }
};
