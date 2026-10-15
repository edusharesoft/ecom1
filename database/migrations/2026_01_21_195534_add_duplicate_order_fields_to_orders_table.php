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
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('is_duplicate_order')->default(0)->after('steadfast_rate');
            $table->integer('duplicate_order_count')->default(0)->after('is_duplicate_order');
            $table->decimal('duplicate_order_rate', 5, 2)->nullable()->after('duplicate_order_count');
            $table->dateTime('last_duplicate_order_date')->nullable()->after('duplicate_order_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_duplicate_order',
                'duplicate_order_count',
                'duplicate_order_rate',
                'last_duplicate_order_date'
            ]);
        });
    }
};
