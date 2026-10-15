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
        Schema::create('product_wholesale_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->integer('min_quantity'); // Minimum quantity for this wholesale tier
            $table->decimal('wholesale_price', 14, 2); // Wholesale price for this tier
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_wholesale_prices');
    }
};
