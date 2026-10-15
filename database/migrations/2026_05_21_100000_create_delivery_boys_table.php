<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_boys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('1 active, 0 inactive');
            $table->decimal('commission_per_delivery', 10, 2)->default(0);
            $table->decimal('monthly_salary_amount', 12, 2)->default(0)->comment('Reference / display; actual pay via salary payments');
            $table->decimal('wallet_balance', 14, 2)->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_boys');
    }
};
