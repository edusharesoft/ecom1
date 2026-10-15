<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseller_landing_pages', function (Blueprint $table) {
            $table->string('facebook_url', 255)->nullable()->after('address');
            $table->string('twitter_url', 255)->nullable()->after('facebook_url');
            $table->string('whatsapp_url', 255)->nullable()->after('twitter_url');
            $table->string('youtube_url', 255)->nullable()->after('whatsapp_url');
            $table->string('instagram_url', 255)->nullable()->after('youtube_url');
            $table->boolean('show_newsletter_footer')->default(1)->after('instagram_url');
            $table->boolean('show_social_footer')->default(1)->after('show_newsletter_footer');
        });
    }

    public function down(): void
    {
        Schema::table('reseller_landing_pages', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url', 'twitter_url', 'whatsapp_url', 'youtube_url', 'instagram_url',
                'show_newsletter_footer', 'show_social_footer'
            ]);
        });
    }
};
