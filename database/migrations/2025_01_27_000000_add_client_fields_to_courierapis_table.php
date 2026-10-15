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
        Schema::table('courierapis', function (Blueprint $table) {
            $table->string('client_id')->nullable()->after('secret_key');
            $table->string('client_secret')->nullable()->after('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courierapis', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'client_secret']);
        });
    }
};
