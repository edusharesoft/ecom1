<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->unsignedInteger('vendor_id')->nullable()->after('product_id');
            $table->decimal('commission_rate', 5, 2)->nullable()->after('sale_price');
            $table->decimal('admin_commission', 12, 2)->default(0)->after('commission_rate');
            $table->decimal('vendor_earning', 12, 2)->default(0)->after('admin_commission');
            $table->timestamp('vendor_paid_at')->nullable()->after('vendor_earning');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id', 'commission_rate', 'admin_commission', 'vendor_earning', 'vendor_paid_at']);
        });
    }
};
