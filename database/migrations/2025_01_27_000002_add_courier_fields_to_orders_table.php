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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier_type')->nullable()->after('order_status')->comment('pathao, steadfast, redx, etc');
            $table->string('courier_tracking_id')->nullable()->after('courier_type')->comment('Consignment ID or Tracking ID from courier');
            $table->timestamp('courier_sent_at')->nullable()->after('courier_tracking_id')->comment('When order was sent to courier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier_type', 'courier_tracking_id', 'courier_sent_at']);
        });
    }
};
