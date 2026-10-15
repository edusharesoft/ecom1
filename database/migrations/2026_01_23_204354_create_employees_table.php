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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable(); // Link to users table (optional)
            $table->string('employee_id')->unique(); // Employee ID like EMP-001
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('designation')->nullable(); // Job title
            $table->string('department')->nullable();
            $table->date('joining_date');
            $table->decimal('basic_salary', 14, 2)->default(0);
            $table->text('address')->nullable();
            $table->string('nid')->nullable(); // National ID
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign keys will be added after table creation to avoid constraint issues
            $table->index('employee_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
