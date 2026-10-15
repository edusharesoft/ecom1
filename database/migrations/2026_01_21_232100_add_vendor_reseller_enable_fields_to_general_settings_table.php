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
        Schema::table('general_settings', function (Blueprint $table) {
            $table->tinyInteger('vendor_enabled')->default(1)->after('status')->comment('1=Enabled, 0=Disabled');
            $table->tinyInteger('reseller_enabled')->default(1)->after('vendor_enabled')->comment('1=Enabled, 0=Disabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['vendor_enabled', 'reseller_enabled']);
        });
    }
};
