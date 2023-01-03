<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User_Login;
use Illuminate\Database\Seeder;

class UserLoginSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = new User_Login;

        $user->name = 'Alfi Nusa Mandiri';
        $user->username = 'alfi';
        $user->password = '123';

        $user->save();

        $user2 = new User_Login;

        $user2->name = 'Damenjo Sitepu';
        $user2->username = 'damenjo';
        $user2->password = '123';

        $user2->save();
    }
}
