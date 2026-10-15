<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('update_api_url', 255)->nullable();
            $table->string('update_script_name', 100)->nullable();
            $table->string('app_version', 50)->nullable();
        });
    }

    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['update_api_url', 'update_script_name', 'app_version']);
        });
    }
};
