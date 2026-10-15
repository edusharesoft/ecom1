<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'delivery_boy_id')) {
                $table->unsignedBigInteger('delivery_boy_id')->nullable()->after('customer_id');
                $table->timestamp('delivery_assigned_at')->nullable()->after('delivery_boy_id');
                $table->timestamp('rider_delivered_at')->nullable()->after('delivery_assigned_at');
                $table->index('delivery_boy_id');
                $table->foreign('delivery_boy_id')->references('id')->on('delivery_boys')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_boy_id')) {
                $table->dropForeign(['delivery_boy_id']);
                $table->dropColumn(['delivery_boy_id', 'delivery_assigned_at', 'rider_delivered_at']);
            }
        });
    }
};
