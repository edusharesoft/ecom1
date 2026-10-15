<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reseller_landing_newsletter_subscribers')) {
            Schema::create('reseller_landing_newsletter_subscribers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('reseller_landing_page_id');
                $table->string('email', 100);
                $table->timestamps();

                $table->foreign('reseller_landing_page_id', 'rlp_newsletter_landing_fk')->references('id')->on('reseller_landing_pages')->onDelete('cascade');
                $table->unique(['reseller_landing_page_id', 'email'], 'rlp_newsletter_email_unique');
            });
        } else {
            try {
                Schema::table('reseller_landing_newsletter_subscribers', function (Blueprint $table) {
                    $table->foreign('reseller_landing_page_id', 'rlp_newsletter_landing_fk')->references('id')->on('reseller_landing_pages')->onDelete('cascade');
                });
            } catch (\Throwable $e) {
                if (strpos($e->getMessage(), 'Duplicate foreign key') === false) {
                    throw $e;
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_landing_newsletter_subscribers');
    }
};
