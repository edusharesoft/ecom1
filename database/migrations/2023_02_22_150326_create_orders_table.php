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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_id')->length('55');
            $table->integer('amount');
            $table->integer('discount');
            $table->integer('shipping_charge');
            $table->integer('customer_id');
            $table->string('order_status')->length('55');
			$table->decimal('fraud_success_rate', 5, 2)->nullable()->after('status_id');
			$table->decimal('pathao_rate',5,2)->nullable();
            $table->decimal('redx_rate',5,2)->nullable();
            $table->decimal('steadfast_rate',5,2)->nullable();
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
        Schema::dropIfExists('orders');
    }
};
