<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_id')->nullable();
            $table->text('page_access_token')->nullable();
            $table->string('page_name')->nullable();
            $table->boolean('auto_post_new_products')->default(false);
            $table->text('post_template')->nullable(); // e.g. "New: {name} - ৳{price}. {link}"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_page_settings');
    }
};
