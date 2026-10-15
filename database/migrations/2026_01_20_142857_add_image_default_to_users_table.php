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
        // Add column if missing
        if (!Schema::hasColumn('users', 'image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('image')->nullable()->default('public/uploads/default/user.png')->after('status');
            });
        } else {
            // If column exists, run raw alter to avoid DBAL requirement
            $default = "public/uploads/default/user.png";
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE users MODIFY image VARCHAR(255) NULL DEFAULT '{$default}'"
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
