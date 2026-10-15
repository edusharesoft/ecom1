<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191);
            $table->text('instructions')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1=enabled,0=disabled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_payment_gateways');
    }
};
