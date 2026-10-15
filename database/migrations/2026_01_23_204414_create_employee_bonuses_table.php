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
        Schema::create('employee_bonuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('bonus_type'); // e.g., 'performance', 'festival', 'annual', 'custom'
            $table->decimal('amount', 14, 2);
            $table->string('salary_month')->nullable(); // Link to specific month if applicable
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            // approved_by and created_by foreign keys will be added separately
            $table->index('employee_id');
            $table->index('status');
            $table->index('salary_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_bonuses');
    }
};
