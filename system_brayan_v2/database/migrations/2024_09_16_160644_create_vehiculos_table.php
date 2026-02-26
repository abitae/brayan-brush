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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('marca');
            $table->string('modelo');
            $table->string('tipo');//configuracion de vehiculo
            $table->string('color')->nullable();
            $table->string('largo')->nullable();
            $table->string('ancho')->nullable();
            $table->string('alto')->nullable();
            $table->string('pesoBruto')->nullable();
            $table->string('pesoNeto')->nullable();
            $table->string('mtc')->nullable();
            $table->string('placa')->nullable();
            $table->string('nroCirculacion')->nullable();
            $table->string('codEmisor')->nullable();
            $table->string('nroAutorizacion')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
