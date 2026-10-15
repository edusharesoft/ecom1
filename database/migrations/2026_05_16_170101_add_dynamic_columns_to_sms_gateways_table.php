<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_gateways', function (Blueprint $table) {
            if (!Schema::hasColumn('sms_gateways', 'gateway_name')) {
                $table->string('gateway_name', 100)->default('BulkSMSBD')->after('id');
            }
            if (!Schema::hasColumn('sms_gateways', 'method')) {
                $table->enum('method', ['GET', 'POST'])->default('GET')->after('url');
            }
            if (!Schema::hasColumn('sms_gateways', 'param_api_key')) {
                $table->string('param_api_key', 50)->default('api_key')->after('api_key');
            }
            if (!Schema::hasColumn('sms_gateways', 'param_phone')) {
                $table->string('param_phone', 50)->default('number')->after('param_api_key');
            }
            if (!Schema::hasColumn('sms_gateways', 'param_message')) {
                $table->string('param_message', 50)->default('message')->after('param_phone');
            }
            if (!Schema::hasColumn('sms_gateways', 'param_senderid')) {
                $table->string('param_senderid', 50)->default('senderid')->after('param_message');
            }
            if (!Schema::hasColumn('sms_gateways', 'extra_params')) {
                // JSON: static extra parameters, e.g. {"type":"text"}
                $table->text('extra_params')->nullable()->after('param_senderid');
            }
            if (!Schema::hasColumn('sms_gateways', 'success_check')) {
                // Response body-তে এই string থাকলে success ধরা হবে
                $table->string('success_check', 100)->default('202')->after('extra_params');
            }
            if (!Schema::hasColumn('sms_gateways', 'auth_type')) {
                $table->enum('auth_type', ['none', 'bearer', 'basic'])->default('none')->after('success_check');
            }
            if (!Schema::hasColumn('sms_gateways', 'auth_value')) {
                $table->text('auth_value')->nullable()->after('auth_type');
            }
        });

        // Default BulkSMSBD config আপডেট
        \DB::table('sms_gateways')->where('id', 1)->update([
            'gateway_name'  => 'BulkSMSBD',
            'method'        => 'GET',
            'param_api_key' => 'api_key',
            'param_phone'   => 'number',
            'param_message' => 'message',
            'param_senderid'=> 'senderid',
            'extra_params'  => json_encode(['type' => 'text']),
            'success_check' => '202',
            'auth_type'     => 'none',
        ]);
    }

    public function down(): void
    {
        Schema::table('sms_gateways', function (Blueprint $table) {
            $cols = ['gateway_name','method','param_api_key','param_phone','param_message',
                     'param_senderid','extra_params','success_check','auth_type','auth_value'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('sms_gateways', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
