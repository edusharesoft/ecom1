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
        Schema::create('expense_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_id')->nullable(); // Original expense ID (nullable to preserve logs after deletion)
            $table->enum('action', ['edit', 'delete']); // Action performed
            $table->string('old_title')->nullable(); // Original title
            $table->string('new_title')->nullable(); // New title (only for edit)
            $table->decimal('old_amount', 15, 2)->nullable(); // Original amount
            $table->decimal('new_amount', 15, 2)->nullable(); // New amount (only for edit)
            $table->date('old_expense_date')->nullable(); // Original date
            $table->date('new_expense_date')->nullable(); // New date (only for edit)
            $table->string('old_category')->nullable(); // Original category
            $table->string('new_category')->nullable(); // New category (only for edit)
            $table->text('old_note')->nullable(); // Original note
            $table->text('new_note')->nullable(); // New note (only for edit)
            $table->decimal('fund_balance_before', 15, 2); // Fund balance before action
            $table->decimal('fund_balance_after', 15, 2); // Fund balance after action
            $table->text('description')->nullable(); // Description of the change
            $table->unsignedBigInteger('performed_by'); // User who performed the action
            $table->timestamps();

            // Use 'set null' so that logs persist even after expense is deleted (important for audit trail)
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('set null');
            $table->index('expense_id');
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
        Schema::dropIfExists('expense_logs');
    }
};
