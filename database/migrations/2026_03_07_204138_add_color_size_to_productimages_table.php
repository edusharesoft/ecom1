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
        Schema::table('productimages', function (Blueprint $table) {
            $table->unsignedInteger('color_id')->nullable()->after('product_id');
            $table->unsignedInteger('size_id')->nullable()->after('color_id');
        });
    }

    public function down(): void
    {
        Schema::table('productimages', function (Blueprint $table) {
            $table->dropColumn(['color_id', 'size_id']);
        });
    }
};
