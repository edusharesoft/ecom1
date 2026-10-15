<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('carts')) {
            return;
        }
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('size_id')->nullable();
            $table->unsignedBigInteger('color_id')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('set null');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('set null');

            $table->unique(['customer_id', 'product_id', 'size_id', 'color_id'], 'unique_cart_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
