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
        Schema::table('vendors', function (Blueprint $table) {
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->string('voter_id_front')->nullable()->after('verification_status');
            $table->string('voter_id_back')->nullable()->after('voter_id_front');
            $table->text('verification_note')->nullable()->after('voter_id_back');
            $table->timestamp('verified_at')->nullable()->after('verification_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'voter_id_front',
                'voter_id_back',
                'verification_note',
                'verified_at'
            ]);
        });
    }
};
