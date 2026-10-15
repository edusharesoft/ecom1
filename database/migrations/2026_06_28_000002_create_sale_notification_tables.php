<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('show_real_orders')->default(true);
            $table->boolean('show_fake_orders')->default(true);
            $table->unsignedTinyInteger('display_duration')->default(5);
            $table->unsignedTinyInteger('interval_min')->default(8);
            $table->unsignedTinyInteger('interval_max')->default(15);
            $table->timestamps();
        });

        Schema::create('sale_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable()->unique();
            $table->string('customer_name', 100);
            $table->string('product_name', 255);
            $table->string('product_image')->nullable();
            $table->string('product_url')->nullable();
            $table->boolean('is_real')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_notifications');
        Schema::dropIfExists('notification_settings');
    }
};
