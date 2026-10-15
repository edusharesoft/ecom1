<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vendor_id');
            $table->decimal('amount', 14, 2);
            $table->decimal('charge', 14, 2)->default(0);
            $table->string('payout_method')->default('manual'); // manual|bkash|nagad|bank
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->default('pending'); // pending|approved|rejected
            $table->text('admin_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_withdrawals');
    }
};
