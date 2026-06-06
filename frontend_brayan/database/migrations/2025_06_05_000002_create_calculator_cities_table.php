<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculator_cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->boolean('can_origin')->default(true);
            $table->boolean('can_destination')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('site_config', function (Blueprint $table) {
            $table->string('calculator_default_origin', 100)->nullable()->after('calculator_express_multiplier');
            $table->string('calculator_default_destination', 100)->nullable()->after('calculator_default_origin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculator_cities');

        Schema::table('site_config', function (Blueprint $table) {
            $table->dropColumn(['calculator_default_origin', 'calculator_default_destination']);
        });
    }
};
