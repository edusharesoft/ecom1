<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Traffic source: facebook | google | tiktok | whatsapp | direct | other
            $table->string('traffic_source', 50)->nullable()->default('direct')->after('note');
            // Optional: store the full referrer URL for deeper analysis
            $table->string('traffic_referrer', 500)->nullable()->after('traffic_source');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['traffic_source', 'traffic_referrer']);
        });
    }
};
