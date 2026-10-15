<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gemini_ai_settings', function (Blueprint $table) {
            $table->id();
            $table->text('api_key')->nullable();
            $table->string('model')->default('gemini-2.0-flash');
            $table->unsignedSmallInteger('timeout')->default(60);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gemini_ai_settings');
    }
};
