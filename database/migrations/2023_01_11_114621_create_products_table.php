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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->integer('category_id'); 
            $table->integer('brand_id')->nullable();
            $table->string('product_code')->unique();
            $table->integer('purchase_price');
            $table->integer('old_price')->nullable();
            $table->integer('new_price');
            $table->integer('stock');
            $table->text('meta_description')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('topsale')->nullable();
            $table->tinyInteger('feature_product')->nullable();
            $table->tinyInteger('campaign_id')->nullable();
            $table->tinyInteger('status');
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
        Schema::dropIfExists('products');
    }
};
