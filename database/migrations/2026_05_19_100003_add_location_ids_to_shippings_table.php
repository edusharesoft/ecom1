<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->nullable()->after('area');
            $table->unsignedBigInteger('district_id')->nullable()->after('division_id');
            $table->unsignedBigInteger('upazila_id')->nullable()->after('district_id');
        });
    }

    public function down(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn(['division_id', 'district_id', 'upazila_id']);
        });
    }
};
