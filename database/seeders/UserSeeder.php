<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {
            DB::table('users')->insert([
                [
                    'name' => 'user ' . $i,
                    'user_name' => 'user' . $i,
                    'email' => 'user' . $i .'@gmail.com',
                    'password' => Hash::make('12345678'),
                    'company_id' => $i,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
    }
}
