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
        if (!Schema::hasColumn('campaigns', 'review')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->string('review')->nullable();
            });
        }
        if (!Schema::hasColumn('campaigns', 'short_description')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->text('short_description')->nullable();
            });
        }
        if (!Schema::hasColumn('campaigns', 'description')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->text('description')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = array_filter([
            Schema::hasColumn('campaigns', 'review') ? 'review' : null,
            Schema::hasColumn('campaigns', 'short_description') ? 'short_description' : null,
            Schema::hasColumn('campaigns', 'description') ? 'description' : null,
        ]);
        if (!empty($columns)) {
            Schema::table('campaigns', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
