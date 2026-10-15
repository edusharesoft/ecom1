<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if status column exists, if not add it
        if (!Schema::hasColumn('payment_gateways', 'status')) {
            Schema::table('payment_gateways', function (Blueprint $table) {
                $table->tinyInteger('status')->default(0)->after('prefix');
            });
        }

        // Insert aamarPay entry if it doesn't exist
        $exists = DB::table('payment_gateways')->where('type', 'aamarpay')->exists();
        
        if (!$exists) {
            DB::table('payment_gateways')->insert([
                'type' => 'aamarpay',
                'app_key' => 'aamarpaytest', // Store ID
                'app_secret' => 'dbb74894e82415a2f7ff0ec3a97e4183', // Signature Key
                'base_url' => 'https://sandbox.aamarpay.com/jsonpost.php',
                'status' => 0, // Inactive by default
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove aamarPay entry
        DB::table('payment_gateways')->where('type', 'aamarpay')->delete();
        
        // Note: We don't remove status column as it might be used by other gateways
    }
};
