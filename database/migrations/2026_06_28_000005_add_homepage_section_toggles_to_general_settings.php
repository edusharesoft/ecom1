<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'homepage_brands_enabled')) {
                $table->boolean('homepage_brands_enabled')->default(true);
            }
            if (! Schema::hasColumn('general_settings', 'homepage_vendors_enabled')) {
                $table->boolean('homepage_vendors_enabled')->default(true);
            }
            if (! Schema::hasColumn('general_settings', 'homepage_blogs_enabled')) {
                $table->boolean('homepage_blogs_enabled')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $cols = ['homepage_brands_enabled', 'homepage_vendors_enabled', 'homepage_blogs_enabled'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
