<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manual_payment_gateways', function (Blueprint $table) {
            if (! Schema::hasColumn('manual_payment_gateways', 'logo')) {
                $table->string('logo', 255)->nullable()->after('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('manual_payment_gateways', function (Blueprint $table) {
            if (Schema::hasColumn('manual_payment_gateways', 'logo')) {
                $table->dropColumn('logo');
            }
        });
    }
};
