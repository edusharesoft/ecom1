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
        // Add foreign keys for employees table
        Schema::table('employees', function (Blueprint $table) {
            // Note: Foreign keys are commented out to avoid constraint issues
            // They can be added manually if needed
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // $table->dropForeign(['user_id']);
            // $table->dropForeign(['created_by']);
        });
    }
};
