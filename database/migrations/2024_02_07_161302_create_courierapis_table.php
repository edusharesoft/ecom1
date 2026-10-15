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
        Schema::create('courierapis', function (Blueprint $table) {
            $table->id();
            $table->string('type')->length(55)->nullable();
            $table->string('api_key')->length(155)->nullable();
            $table->string('secret_key')->length(155)->nullable();
            $table->string('url')->length(99)->nullable();
            $table->string('token')->length(350)->nullable();
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
        Schema::dropIfExists('courierapis');
    }
};
