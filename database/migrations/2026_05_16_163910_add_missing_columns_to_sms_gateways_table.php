<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_gateways', function (Blueprint $table) {
            if (!Schema::hasColumn('sms_gateways', 'senderid')) {
                $table->string('senderid', 50)->nullable()->after('api_key');
            }
            if (!Schema::hasColumn('sms_gateways', 'order')) {
                $table->tinyInteger('order')->default(0)->after('status');
            }
            if (!Schema::hasColumn('sms_gateways', 'forget_pass')) {
                $table->tinyInteger('forget_pass')->default(0)->after('order');
            }
            if (!Schema::hasColumn('sms_gateways', 'password_g')) {
                $table->tinyInteger('password_g')->default(0)->after('forget_pass');
            }
            if (!Schema::hasColumn('sms_gateways', 'admin_phone')) {
                $table->string('admin_phone', 255)->nullable()->after('password_g');
            }
        });

        // Default row set করা (bulksmsbd URL)
        if (\DB::table('sms_gateways')->count() === 0) {
            \DB::table('sms_gateways')->insert([
                'url'        => 'http://bulksmsbd.net/api/smsapi',
                'api_key'    => '',
                'senderid'   => '',
                'serderid'   => '',
                'status'     => 0,
                'order'      => 0,
                'forget_pass'=> 0,
                'password_g' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // আগে থেকে থাকলে URL আপডেট করো
            \DB::table('sms_gateways')->whereNull('url')
                ->orWhere('url', '')
                ->update(['url' => 'http://bulksmsbd.net/api/smsapi']);
        }
    }

    public function down(): void
    {
        Schema::table('sms_gateways', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('sms_gateways', 'senderid')    ? 'senderid'    : null,
                Schema::hasColumn('sms_gateways', 'order')       ? 'order'       : null,
                Schema::hasColumn('sms_gateways', 'forget_pass') ? 'forget_pass' : null,
                Schema::hasColumn('sms_gateways', 'password_g')  ? 'password_g'  : null,
                Schema::hasColumn('sms_gateways', 'admin_phone') ? 'admin_phone' : null,
            ]));
        });
    }
};
