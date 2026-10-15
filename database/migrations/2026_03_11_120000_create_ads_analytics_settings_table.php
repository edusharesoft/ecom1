<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads_analytics_settings', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // facebook, google, tiktok
            $table->boolean('is_active')->default(false);
            $table->text('access_token')->nullable();
            $table->string('ad_account_id')->nullable();      // act_123456 (Facebook), 123-456-7890 (Google), 123456789 (TikTok)
            $table->string('app_id')->nullable();
            $table->string('app_secret')->nullable();
            $table->string('refresh_token')->nullable();      // Google
            $table->string('client_id')->nullable();          // Google
            $table->string('client_secret')->nullable();      // Google
            $table->json('extra_config')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_analytics_settings');
    }
};
