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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encomienda_id')->constrained('encomiendas');
            $table->string('tipoDoc');
            $table->string('tipoOperacion');
            $table->string('serie');
            $table->string('correlativo');
            $table->string('fechaEmision');
            $table->string('formaPago_moneda');
            $table->string('formaPago_tipo');
            $table->string('tipoMoneda');
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('client_id')->constrained('customers');
            $table->decimal('mtoOperGravadas',8,2);
            $table->decimal('mtoIGV',8,2);
            $table->decimal('totalImpuestos',8,2);
            $table->decimal('valorVenta',8,2);
            $table->decimal('subTotal',8,2);
            $table->decimal('mtoImpVenta',8,2);
            $table->decimal('monto_descuento',8,2)->nullable();
            $table->string('motivo_descuento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
