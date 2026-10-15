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
            $table->index(['product_id', 'color_id', 'size_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productimages', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'color_id', 'size_id']);
        });
    }
};
