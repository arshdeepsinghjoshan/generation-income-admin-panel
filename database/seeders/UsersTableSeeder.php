<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();
        User::truncate();
        DB::table('users')->insert([
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'referral_id' => 'JJOF1714714247',
            'state_id' => User::STATE_ACTIVE,
            'role_id' => User::ROLE_ADMIN,
            'created_by_id' => User::ROLE_ADMIN,
            'referrad_code' => User::ROLE_ADMIN
        ]);
    }
}
