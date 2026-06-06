<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_config', function (Blueprint $table) {
            $table->decimal('calculator_base_fee', 10, 2)->nullable()->after('calculator_default_height');
            $table->unsignedInteger('calculator_included_kg')->nullable()->after('calculator_base_fee');
            $table->decimal('calculator_excess_price_per_kg', 10, 4)->nullable()->after('calculator_included_kg');
            $table->decimal('calculator_express_multiplier', 5, 2)->nullable()->after('calculator_excess_price_per_kg');
        });

        Schema::table('pricing_routes', function (Blueprint $table) {
            $table->unsignedInteger('included_kg')->default(5)->after('base_fee');
        });
    }

    public function down(): void
    {
        Schema::table('site_config', function (Blueprint $table) {
            $table->dropColumn([
                'calculator_base_fee',
                'calculator_included_kg',
                'calculator_excess_price_per_kg',
                'calculator_express_multiplier',
            ]);
        });

        Schema::table('pricing_routes', function (Blueprint $table) {
            $table->dropColumn('included_kg');
        });
    }
};
