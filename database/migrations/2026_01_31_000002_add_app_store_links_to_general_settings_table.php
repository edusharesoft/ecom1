<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'google_play_link')) {
                $table->string('google_play_link')->nullable()->after('footer_about_text');
            }
            if (!Schema::hasColumn('general_settings', 'app_store_link')) {
                $table->string('app_store_link')->nullable()->after('google_play_link');
            }
        });
    }

    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'google_play_link')) {
                $table->dropColumn('google_play_link');
            }
            if (Schema::hasColumn('general_settings', 'app_store_link')) {
                $table->dropColumn('app_store_link');
            }
        });
    }
};
