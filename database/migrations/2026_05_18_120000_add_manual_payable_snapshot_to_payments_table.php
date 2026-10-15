<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'manual_payable_snapshot')) {
                $table->unsignedBigInteger('manual_payable_snapshot')->nullable()->after('amount')->comment('ম্যানুয়াল গেটওয়ে: চেকআউটে প্রত্যাশিত টাকা; Paid হলে amount এ বসানো হবে');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'manual_payable_snapshot')) {
                $table->dropColumn('manual_payable_snapshot');
            }
        });
    }
};
