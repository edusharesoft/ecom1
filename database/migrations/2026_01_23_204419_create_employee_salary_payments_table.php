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
        Schema::create('employee_salary_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('salary_id')->nullable(); // Link to employee_salaries
            $table->string('payment_id')->unique(); // Payment ID like PAY-001
            $table->string('payment_month'); // Format: 2026-01
            $table->decimal('amount', 14, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'bkash', 'nagad', 'rocket', 'check'])->default('bank_transfer');
            $table->string('transaction_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->unsignedInteger('paid_by')->nullable(); // Admin who processed payment
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('salary_id')->references('id')->on('employee_salaries')->onDelete('set null');
            // paid_by foreign key will be added separately
            $table->index('employee_id');
            $table->index('payment_month');
            $table->index('status');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salary_payments');
    }
};
