<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'delivery_otp_hash')) {
                $table->string('delivery_otp_hash')->nullable()->after('rider_delivered_at');
            }
            if (! Schema::hasColumn('orders', 'delivery_otp_sent_at')) {
                $table->timestamp('delivery_otp_sent_at')->nullable()->after('delivery_otp_hash');
            }
            if (! Schema::hasColumn('orders', 'delivery_otp_expires_at')) {
                $table->timestamp('delivery_otp_expires_at')->nullable()->after('delivery_otp_sent_at');
            }
            if (! Schema::hasColumn('orders', 'delivery_otp_verified_at')) {
                $table->timestamp('delivery_otp_verified_at')->nullable()->after('delivery_otp_expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'delivery_otp_hash',
                'delivery_otp_sent_at',
                'delivery_otp_expires_at',
                'delivery_otp_verified_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
