<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->decimal('reseller_wallet_min_balance', 14, 2)->default(0)->nullable()->after('reseller_deposit_max');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn('reseller_wallet_min_balance');
        });
    }
};
