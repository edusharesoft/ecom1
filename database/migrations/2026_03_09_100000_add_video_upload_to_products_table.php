<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // 'youtube' = YouTube embed | 'upload' = local file
            $table->string('pro_video_type', 20)->nullable()->after('pro_video');
            // Path to locally uploaded video file
            $table->string('pro_video_path', 300)->nullable()->after('pro_video_type');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['pro_video_type', 'pro_video_path']);
        });
    }
};
