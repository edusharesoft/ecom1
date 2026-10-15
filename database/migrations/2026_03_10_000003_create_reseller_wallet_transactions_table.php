<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type', 30); // deposit, order_profit, delivery_charge_deduct, withdrawal
            $table->decimal('amount', 14, 2); // positive = credit, negative = debit
            $table->decimal('balance_after', 14, 2)->nullable();
            $table->string('reference_type', 50)->nullable(); // Order, ResellerDeposit, ResellerWithdrawal
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_wallet_transactions');
    }
};
