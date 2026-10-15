<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Legacy app table (2023_02_22_create_districts) reused the same name;
        // keep it under a different name so delivery districts can use `districts`.
        if (Schema::hasTable('districts')) {
            Schema::rename('districts', 'legacy_district_areas');
        }

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('delivery_charge')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');

        if (Schema::hasTable('legacy_district_areas')) {
            Schema::rename('legacy_district_areas', 'districts');
        }
    }
};
