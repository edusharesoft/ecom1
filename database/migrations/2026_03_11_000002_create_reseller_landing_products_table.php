<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reseller_landing_products')) {
            return;
        }
        Schema::create('reseller_landing_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reseller_landing_page_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('custom_price', 12, 2);
            $table->timestamps();

            $table->foreign('reseller_landing_page_id')->references('id')->on('reseller_landing_pages')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['reseller_landing_page_id', 'product_id'], 'rlp_product_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_landing_products');
    }
};
