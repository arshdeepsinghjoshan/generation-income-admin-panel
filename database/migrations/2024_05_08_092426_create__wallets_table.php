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
        Schema::dropIfExists('wallets');
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_number')->unique();
            $table->string('credit')->nullable();
            $table->string('debit')->nullable();
            $table->string('balance')->nullable()->default(0);
            $table->string('state_id')->default(1);
            $table->string('type_id')->nullable();
            $table->integer('created_by_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
