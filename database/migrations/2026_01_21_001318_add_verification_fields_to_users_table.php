<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->nullable()->after('wallet_balance');
            $table->string('voter_id_front')->nullable()->after('verification_status');
            $table->string('voter_id_back')->nullable()->after('voter_id_front');
            $table->string('self_image')->nullable()->after('voter_id_back');
            $table->text('verification_note')->nullable()->after('self_image');
            $table->timestamp('verified_at')->nullable()->after('verification_note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'voter_id_front',
                'voter_id_back',
                'self_image',
                'verification_note',
                'verified_at'
            ]);
        });
    }
};
