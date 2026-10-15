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
        Schema::create('versions', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20)->unique();
            $table->date('release_date');
            $table->text('changelog')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_migration')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('versions');
    }
};
