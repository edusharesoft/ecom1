<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Fraud
            $table->integer('fraud_success')->default(0)->after('order_status');
            $table->integer('fraud_cancel')->default(0)->after('fraud_success');
            $table->decimal('fraud_rate', 5, 2)->default(0)->after('fraud_cancel');

            // Pathao
            $table->integer('pathao_success')->default(0)->after('fraud_rate');
            $table->integer('pathao_cancel')->default(0)->after('pathao_success');
            $table->decimal('pathao_rate', 5, 2)->default(0)->after('pathao_cancel');

            // RedX
            $table->integer('redx_success')->default(0)->after('pathao_rate');
            $table->integer('redx_cancel')->default(0)->after('redx_success');
            $table->decimal('redx_rate', 5, 2)->default(0)->after('redx_cancel');

            // Steadfast
            $table->integer('steadfast_success')->default(0)->after('redx_rate');
            $table->integer('steadfast_cancel')->default(0)->after('steadfast_success');
            $table->decimal('steadfast_rate', 5, 2)->default(0)->after('steadfast_cancel');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'fraud_success','fraud_cancel','fraud_rate',
                'pathao_success','pathao_cancel','pathao_rate',
                'redx_success','redx_cancel','redx_rate',
                'steadfast_success','steadfast_cancel','steadfast_rate'
            ]);
        });
    }
};
