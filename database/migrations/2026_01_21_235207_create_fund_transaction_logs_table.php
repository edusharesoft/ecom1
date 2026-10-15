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
        Schema::create('fund_transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fund_transaction_id')->nullable(); // Original transaction ID (nullable to preserve logs after deletion)
            $table->enum('action', ['edit', 'delete']); // Action performed
            $table->enum('old_direction', ['in', 'out'])->nullable(); // Original direction (for edit/delete)
            $table->enum('new_direction', ['in', 'out'])->nullable(); // New direction (only for edit)
            $table->decimal('old_amount', 15, 2)->nullable(); // Original amount
            $table->decimal('new_amount', 15, 2)->nullable(); // New amount (only for edit)
            $table->decimal('balance_before', 15, 2); // Balance before action
            $table->decimal('balance_after', 15, 2); // Balance after action
            $table->string('old_note')->nullable(); // Original note
            $table->string('new_note')->nullable(); // New note (only for edit)
            $table->text('description')->nullable(); // Description of the change
            $table->unsignedBigInteger('performed_by'); // User who performed the action
            $table->timestamps();

            // Use 'set null' so that logs persist even after transaction is deleted (important for audit trail)
            // The transaction ID will be null but all other data (amount, direction, balance changes) will remain
            $table->foreign('fund_transaction_id')->references('id')->on('fund_transactions')->onDelete('set null');
            $table->index('fund_transaction_id');
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
        Schema::dropIfExists('fund_transaction_logs');
    }
};
