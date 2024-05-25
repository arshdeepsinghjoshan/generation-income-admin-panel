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
        Schema::dropIfExists('subscription_plans');
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title', 128);
            $table->text('description')->nullable();
            $table->string('price', 32)->default(0);
            $table->string('product_id', 128)->nullable();
            $table->integer('state_id')->default(0);
            $table->integer('type_id')->nullable();
            $table->integer('duration_type')->nullable();//Store duration type like day => 0 or month => 1 
            $table->integer('duration')->nullable();//Enter number of duration like 1, 2, 3, 4, 5..12, 13..24
            $table->integer('created_by_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
