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
        Schema::table('product_wholesale_prices', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('wholesale_price'); // Stock quantity for this wholesale tier
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_wholesale_prices', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
