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
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('referral_id')->unique()->nullable();
            $table->string('state_id');
            $table->string('role_id');
            $table->string('type_id')->nullable();
            $table->string('position_id')->nullable();
            $table->string('parent_id')->nullable();
            $table->string('referrad_code');
            $table->integer('otp')->nullable();
            $table->integer('otp_email')->nullable();
            $table->integer('otp_verified')->default(0);
            $table->integer('email_verified')->default(0);
            $table->string('average_rating', 16)->default('0');
            $table->string('gender')->nullable();
            $table->string('profile_image')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('about_me')->nullable();
            $table->integer('tos')->nullable();
            $table->string('language', 32)->nullable();
            $table->string('latitude', 32)->nullable();
            $table->string('longitude', 32)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('address')->nullable();
            $table->dateTime('last_visit_time')->nullable();
            $table->dateTime('last_action_time')->nullable();
            $table->text('take_image')->nullable();
            $table->string('created_by_id');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
