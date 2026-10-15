<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_boy_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_boy_id')->constrained('delivery_boys')->cascadeOnDelete();
            $table->string('type', 32)->comment('commission, salary, adjustment, withdrawal');
            $table->decimal('amount', 14, 2);
            $table->string('direction', 8)->comment('credit or debit');
            $table->decimal('balance_after', 14, 2);
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('withdrawal_id')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['delivery_boy_id', 'created_at'], 'db_wallet_tx_boy_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_boy_wallet_transactions');
    }
};
