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
        Schema::dropIfExists('subscribed_plans');
        Schema::create('subscribed_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('plan_id')->nullable();
            $table->integer('state_id')->default(0);
            $table->integer('type_id')->nullable();
            $table->integer('roi_count')->nullable()->default(0);
            $table->integer('roi_complete_count')->nullable()->default(0);
            $table->integer('created_by_id')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribed_plans');
    }
};
