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
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('customer_id');
            $table->integer('amount');
            $table->string('trx_id')->length('55')->nullable();
            $table->string('sender_number')->length('55')->nullable();
            $table->string('payment_method')->length('55')->nullable();
            $table->string('payment_status')->length('55');
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
        Schema::dropIfExists('payments');
    }
};
