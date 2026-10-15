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
        if (!Schema::hasColumn('campaigns', 'date')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->string('date', 55)->nullable()->after('slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('campaigns', 'date')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropColumn('date');
            });
        }
    }
};
