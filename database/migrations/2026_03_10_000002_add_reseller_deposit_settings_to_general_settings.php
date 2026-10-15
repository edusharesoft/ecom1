<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->decimal('reseller_deposit_min', 14, 2)->default(100)->nullable()->after('reseller_enabled');
            $table->decimal('reseller_deposit_max', 14, 2)->default(1000000)->nullable()->after('reseller_deposit_min');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['reseller_deposit_min', 'reseller_deposit_max']);
        });
    }
};
