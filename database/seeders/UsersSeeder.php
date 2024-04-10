<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('ZAQ!2wsxCDE#4rfv'),
                'role_id' => 1,
                'banned_time' => null,
                'banned_from' => null,
                'banned_to' => null,
            ],
            [
                'name' => 'Adminforum',
                'email' => 'admin1@gmail.com',
                'password' => bcrypt('!QAZxsw2#EDC'),
                'role_id' => 2,
                'banned_time' => null,
                'banned_from' => null,
                'banned_to' => null,
            ],
            [
                'name' => 'Admingame',
                'email' => 'admin2@gmail.com',
                'password' => bcrypt('QWErtyHGFdsa142'),
                'role_id' => 3,
                'banned_time' => null,
                'banned_from' => null,
                'banned_to' => null,
            ],
        ];

        foreach($users as $user) {
            User::create($user);
        }
    }
}
