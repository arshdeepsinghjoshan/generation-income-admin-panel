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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->string('type_id'); // 'credit' or 'debit'
            $table->string('transaction_type'); // 'level' or 'roi'
            $table->string('state_id'); // 'pending', 'completed', 'failed'
            $table->string('wallet_id');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->string('user_id')->nullable();
            $table->string('created_by_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
