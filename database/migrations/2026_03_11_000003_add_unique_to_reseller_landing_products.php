<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseller_landing_products', function (Blueprint $table) {
            $table->unique(['reseller_landing_page_id', 'product_id'], 'rlp_product_unique');
        });
    }

    public function down(): void
    {
        Schema::table('reseller_landing_products', function (Blueprint $table) {
            $table->dropUnique('rlp_product_unique');
        });
    }
};
