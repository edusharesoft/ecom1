<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add role enum to users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'vendor', 'reseller', 'customer'])->default('customer')->after('status');
        });

        // Add reseller_price to products table
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('reseller_price', 14, 2)->nullable()->after('new_price');
        });

        // Add reseller_profit and customer_payable_amount to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('reseller_profit', 14, 2)->nullable()->after('amount');
            $table->decimal('customer_payable_amount', 14, 2)->nullable()->after('reseller_profit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('reseller_price');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['reseller_profit', 'customer_payable_amount']);
        });
    }
};
