<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ফ্রড রেট সিঙ্ক না হওয়া পর্যন্ত NULL — ইন্ডেক্সে "চেকিং" দেখানোর জন্য।
     */
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'fraud_rate')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE `orders` MODIFY `fraud_rate` DECIMAL(5,2) NULL DEFAULT NULL');
        }

        DB::table('orders')
            ->where('fraud_rate', 0)
            ->where('fraud_success', 0)
            ->where('fraud_cancel', 0)
            ->where('pathao_success', 0)
            ->where('pathao_cancel', 0)
            ->where('redx_success', 0)
            ->where('redx_cancel', 0)
            ->where('steadfast_success', 0)
            ->where('steadfast_cancel', 0)
            ->update(['fraud_rate' => null]);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('orders', 'fraud_rate')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        DB::table('orders')->whereNull('fraud_rate')->update(['fraud_rate' => 0]);

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE `orders` MODIFY `fraud_rate` DECIMAL(5,2) NOT NULL DEFAULT 0');
        }
    }
};
