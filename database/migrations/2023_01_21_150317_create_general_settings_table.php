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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->length('55');
            $table->string('white_logo')->length('255');
            $table->string('dark_logo')->length('255');
            $table->string('favicon')->length('255');
            $table->string('copyright')->length('155')->nullable();
            $table->tinyInteger('status')->length('2');
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
        Schema::dropIfExists('general_settings');
    }
};
