<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_landing_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('slug', 100)->unique();
            $table->string('custom_domain', 255)->nullable();
            $table->string('logo')->nullable();
            $table->string('title')->nullable();
            $table->string('tagline')->nullable();
            $table->json('slider_images')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('slug');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_landing_pages');
    }
};
