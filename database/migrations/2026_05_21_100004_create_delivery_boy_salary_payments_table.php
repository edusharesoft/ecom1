<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_boy_salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_boy_id')->constrained('delivery_boys')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('salary_month', 7)->comment('YYYY-MM');
            $table->string('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['delivery_boy_id', 'salary_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_boy_salary_payments');
    }
};
