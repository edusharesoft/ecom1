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
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('is_wholesale')->default(0)->after('reseller_price'); // 0=no, 1=yes
            $table->decimal('wholesale_price', 14, 2)->nullable()->after('is_wholesale'); // Wholesale price per unit
            $table->integer('min_wholesale_quantity')->default(1)->after('wholesale_price'); // Minimum quantity for wholesale
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_wholesale', 'wholesale_price', 'min_wholesale_quantity']);
        });
    }
};
