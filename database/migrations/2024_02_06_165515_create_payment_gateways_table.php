<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('type')->length(55)->nullable();
            $table->string('app_key')->length(155)->nullable();
            $table->string('app_secret')->length(155)->nullable();
            $table->string('username')->length(55)->nullable();
            $table->string('password')->length(55)->nullable();
            $table->string('base_url')->length(99)->nullable();
            $table->string('success_url')->length(155)->nullable();
            $table->string('return_url')->length(155)->nullable();
            $table->string('prefix')->length(25)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_gateways');
    }
};
