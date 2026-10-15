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
        Schema::create('wholesale_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('subcategory_id')->nullable();
            $table->unsignedInteger('childcategory_id')->nullable();
            $table->unsignedInteger('brand_id')->nullable();
            $table->string('product_code')->unique();
            
            // Pricing
            $table->decimal('purchase_price', 14, 2);
            $table->decimal('wholesale_price', 14, 2); // Wholesale price per unit
            $table->decimal('retail_price', 14, 2)->nullable(); // Retail price (optional)
            $table->integer('min_quantity')->default(1); // Minimum quantity for wholesale
            
            // Stock & Status
            $table->integer('stock')->default(0);
            $table->tinyInteger('status')->default(1); // 1=active, 0=inactive
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Vendor & Creator
            $table->unsignedInteger('vendor_id')->nullable(); // null = admin uploaded
            $table->unsignedInteger('created_by')->nullable(); // user id (admin or vendor)
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('meta_image')->nullable();
            
            // Additional
            $table->tinyInteger('feature_product')->default(0);
            $table->string('unit')->default('piece'); // piece, kg, liter, etc.
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            
            // Indexes
            $table->index('category_id');
            $table->index('vendor_id');
            $table->index('status');
            $table->index('approval_status');
        });
        
        // Wholesale product images table
        Schema::create('wholesale_product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wholesale_product_id');
            $table->string('image');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('wholesale_product_id')->references('id')->on('wholesale_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesale_product_images');
        Schema::dropIfExists('wholesale_products');
    }
};
