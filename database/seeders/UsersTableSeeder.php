<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Get all table names
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . DB::getDatabaseName();
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            DB::table($tableName)->truncate();
        }
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@lbm.in',
            'password' => Hash::make('password'),
            'referral_id' => 'JJOF1714714247',
            'state_id' => User::STATE_ACTIVE,
            'role_id' => User::ROLE_ADMIN,
            'created_by_id' => User::ROLE_ADMIN,
            'referrad_code' => User::ROLE_ADMIN
        ]);

        DB::table('wallets')->insert([
            'wallet_number' => (new Wallet())->generateWalletNumber(),
            'state_id' => Wallet::STATE_ACTIVE,
            'created_by_id' => 1,
        ]);
    }
}
