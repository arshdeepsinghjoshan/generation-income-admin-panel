<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        DB::statement("DROP TABLE IF EXISTS `support_departments`;");
        DB::statement("
        CREATE TABLE IF NOT EXISTS `support_departments` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(255) DEFAULT NULL,
          `description` varchar(255) DEFAULT NULL,
          `image` text DEFAULT NULL,
          `type_id` varchar(64) DEFAULT NULL,
          `state_id` int(11) DEFAULT 0,
          `created_by_id` int(11) NOT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          INDEX(`title`),
          INDEX(`created_by_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        DB::statement("DROP TABLE IF EXISTS `supports`;");
        DB::statement("
        CREATE TABLE IF NOT EXISTS `supports` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(255) DEFAULT NULL,
          `department_id` varchar(64) DEFAULT NULL,
          `priority_id` varchar(64) DEFAULT NULL,
          `message` varchar(255) DEFAULT NULL,
          `image` text DEFAULT NULL,
          `type_id` varchar(64) DEFAULT NULL,
          `state_id` int(11) DEFAULT 0,
          `created_by_id` int(11) NOT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          INDEX(`title`),
          INDEX(`created_by_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        DB::statement("DROP TABLE IF EXISTS `support_replies`;");
        DB::statement("
        CREATE TABLE IF NOT EXISTS `support_replies` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `support_id` int(11) DEFAULT 0,
        `message` varchar(64) DEFAULT NULL,
        `image` text DEFAULT NULL,
        `type_id` varchar(255) DEFAULT NULL,
        `state_id` int(11) DEFAULT 0,
        `created_by_id` int(11) NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
         PRIMARY KEY (`id`),
         INDEX(`message`),
         INDEX(`created_by_id`)
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support');
        Schema::dropIfExists('support_departments');
        Schema::dropIfExists('support_reply');
    }
};
