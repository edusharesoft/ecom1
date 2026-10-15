<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 14, 2);
            $table->string('payment_gateway', 50)->default('uddoktapay');
            $table->string('transaction_id')->nullable();
            $table->string('status', 20)->default('pending'); // pending, completed, failed
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('delivery_charge_deducted')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_deposits');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_charge_deducted');
        });
    }
};
