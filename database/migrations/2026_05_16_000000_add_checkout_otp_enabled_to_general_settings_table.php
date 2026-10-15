<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'checkout_otp_enabled')) {
                $table->boolean('checkout_otp_enabled')->default(false)->after('reseller_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'checkout_otp_enabled')) {
                $table->dropColumn('checkout_otp_enabled');
            }
        });
    }
};
