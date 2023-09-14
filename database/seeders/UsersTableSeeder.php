<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
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
                'first_name'        => 'FIRST_NAME',
                'last_name'         => 'LAST_NAME',
                'username'          => 'admin',
                'email'             => 'admin@shop.ir',
                'email_verified_at' => now(),
                'password'          => bcrypt('12345678'),
            ],
        ];
        User::insert($users);

    }
}
