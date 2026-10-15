<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_boy_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_boy_id')->constrained('delivery_boys')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('status', 16)->default('pending')->comment('pending, approved, rejected');
            $table->string('payout_method', 64)->nullable()->comment('bkash, nagad, bank');
            $table->string('payout_number', 64)->nullable();
            $table->text('note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_boy_withdrawals');
    }
};
