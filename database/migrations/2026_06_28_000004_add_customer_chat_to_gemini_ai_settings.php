<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gemini_ai_settings', function (Blueprint $table) {
            $table->boolean('customer_chat_enabled')->default(true)->after('status');
            $table->text('customer_chat_welcome')->nullable()->after('customer_chat_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('gemini_ai_settings', function (Blueprint $table) {
            $table->dropColumn(['customer_chat_enabled', 'customer_chat_welcome']);
        });
    }
};
