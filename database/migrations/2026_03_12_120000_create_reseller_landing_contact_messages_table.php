<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_landing_contact_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reseller_landing_page_id');
            $table->string('full_name', 255);
            $table->string('mobile', 50);
            $table->string('email', 100)->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('details');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->foreign('reseller_landing_page_id', 'rlc_landing_fk')->references('id')->on('reseller_landing_pages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_landing_contact_messages');
    }
};
