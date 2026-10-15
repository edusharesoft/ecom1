<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vendor_id');
            $table->string('type'); // earning | withdraw
            $table->string('status')->default('completed'); // completed | pending | rejected
            $table->decimal('amount', 14, 2);
            $table->string('source_type')->nullable(); // order | withdraw
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_wallet_transactions');
    }
};
