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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('customer_id');
            $table->string('refund_id')->unique(); // Unique refund ID like REF-12345
            $table->decimal('amount', 14, 2); // Refund amount
            $table->decimal('shipping_charge', 14, 2)->default(0); // Shipping charge to refund
            $table->text('reason')->nullable(); // Customer's reason for refund
            $table->text('admin_note')->nullable(); // Admin's note
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed'])->default('pending');
            $table->enum('refund_method', ['original_payment', 'bkash', 'nagad', 'bank', 'manual'])->default('original_payment');
            $table->string('refund_account')->nullable(); // Account number/phone for refund
            $table->string('refund_account_name')->nullable(); // Account holder name
            $table->string('transaction_id')->nullable(); // Transaction ID after processing
            $table->unsignedInteger('processed_by')->nullable(); // Admin user ID who processed
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            
            $table->index('order_id');
            $table->index('customer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
