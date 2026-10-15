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
        Schema::create('purchase_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id')->nullable(); // Original purchase ID (nullable to preserve logs after deletion)
            $table->enum('action', ['edit', 'delete']); // Action performed
            $table->string('old_invoice_no')->nullable(); // Original invoice no
            $table->string('new_invoice_no')->nullable(); // New invoice no (only for edit)
            $table->date('old_purchase_date')->nullable(); // Original date
            $table->date('new_purchase_date')->nullable(); // New date (only for edit)
            $table->decimal('old_paid_amount', 15, 2)->nullable(); // Original paid amount
            $table->decimal('new_paid_amount', 15, 2)->nullable(); // New paid amount (only for edit)
            $table->decimal('old_grand_total', 15, 2)->nullable(); // Original grand total
            $table->decimal('new_grand_total', 15, 2)->nullable(); // New grand total (only for edit)
            $table->text('old_note')->nullable(); // Original note
            $table->text('new_note')->nullable(); // New note (only for edit)
            $table->decimal('fund_balance_before', 15, 2); // Fund balance before action
            $table->decimal('fund_balance_after', 15, 2); // Fund balance after action
            $table->text('description')->nullable(); // Description of the change
            $table->unsignedBigInteger('performed_by'); // User who performed the action
            $table->timestamps();

            // Use 'set null' so that logs persist even after purchase is deleted (important for audit trail)
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('set null');
            $table->index('purchase_id');
            $table->index('action');
            $table->index('performed_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_logs');
    }
};
