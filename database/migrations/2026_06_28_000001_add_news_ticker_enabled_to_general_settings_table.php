<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'news_ticker_enabled')) {
                $table->boolean('news_ticker_enabled')->default(true)->after('top_headline');
            }
        });

        if (Schema::hasColumn('general_settings', 'news_ticker_enabled')) {
            DB::table('general_settings')
                ->whereNotNull('top_headline')
                ->where('top_headline', '!=', '')
                ->update(['news_ticker_enabled' => 1]);
        }
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'news_ticker_enabled')) {
                $table->dropColumn('news_ticker_enabled');
            }
        });
    }
};
