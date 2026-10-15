<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cron_job_settings', function (Blueprint $table) {
            $table->id();
            $table->string('job_key', 80)->unique();
            $table->string('job_title', 150);
            $table->text('job_description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('frequency_minutes')->default(10);
            $table->unsignedSmallInteger('order_limit')->default(50);
            $table->timestamp('last_run_at')->nullable();
            $table->string('last_run_status', 20)->nullable(); // success | failed | running
            $table->text('last_run_result')->nullable();
            $table->unsignedInteger('last_updated_count')->default(0);
            $table->unsignedInteger('last_failed_count')->default(0);
            $table->timestamps();
        });

        // Default courier sync job
        DB::table('cron_job_settings')->insert([
            'job_key'            => 'courier_status_sync',
            'job_title'          => 'কুরিয়ার স্ট্যাটাস সিঙ্ক',
            'job_description'    => 'Pathao, Steadfast ও RedX কুরিয়ার থেকে অর্ডার স্ট্যাটাস স্বয়ংক্রিয়ভাবে আপডেট করে।',
            'is_enabled'         => true,
            'frequency_minutes'  => 10,
            'order_limit'        => 50,
            'last_run_at'        => null,
            'last_run_status'    => null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cron_job_settings');
    }
};
