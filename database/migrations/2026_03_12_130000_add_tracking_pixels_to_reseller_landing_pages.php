<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseller_landing_pages', function (Blueprint $table) {
            $table->string('facebook_pixel_id', 50)->nullable()->after('instagram_url');
            $table->string('gtm_id', 50)->nullable()->after('facebook_pixel_id');
            $table->string('tiktok_pixel_id', 50)->nullable()->after('gtm_id');
            $table->text('facebook_capi_access_token')->nullable()->after('tiktok_pixel_id');
        });
    }

    public function down(): void
    {
        Schema::table('reseller_landing_pages', function (Blueprint $table) {
            $table->dropColumn(['facebook_pixel_id', 'gtm_id', 'tiktok_pixel_id', 'facebook_capi_access_token']);
        });
    }
};
