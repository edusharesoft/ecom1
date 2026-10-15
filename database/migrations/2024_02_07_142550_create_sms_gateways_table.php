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
        Schema::create('sms_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('url')->length(99)->nullable();
            $table->string('api_key')->length(155)->nullable();
            $table->string('serderid')->length(155)->nullable();
            $table->string('status')->length(25)->nullable();
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
        Schema::dropIfExists('sms_gateways');
    }
};
