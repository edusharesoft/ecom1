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
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('salary_month'); // Format: 2026-01
            $table->integer('total_days'); // Total days in month
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('working_days')->default(0); // Calculated working days
            $table->decimal('basic_salary', 14, 2)->default(0);
            $table->decimal('allowance', 14, 2)->default(0);
            $table->decimal('deduction', 14, 2)->default(0);
            $table->decimal('bonus', 14, 2)->default(0);
            $table->decimal('overtime', 14, 2)->default(0);
            $table->decimal('gross_salary', 14, 2)->default(0); // Before deduction
            $table->decimal('net_salary', 14, 2)->default(0); // After deduction
            $table->enum('status', ['pending', 'calculated', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedInteger('calculated_by')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            // calculated_by foreign key will be added separately
            $table->unique(['employee_id', 'salary_month']); // One salary per employee per month
            $table->index('salary_month');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};
