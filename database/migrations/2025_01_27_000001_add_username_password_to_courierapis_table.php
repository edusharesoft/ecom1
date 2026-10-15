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
            $table->string('username')->nullable()->after('client_secret');
            $table->string('password')->nullable()->after('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courierapis', function (Blueprint $table) {
            $table->dropColumn(['username', 'password']);
        });
    }
};
