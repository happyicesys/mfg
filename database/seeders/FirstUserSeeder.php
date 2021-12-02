<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class FirstUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::create([
        //     'name' => 'brian',
        //     'username' => 'brian',
        //     'email' => 'leehongjie91@gmail.com',
        //     'password' => 'brian',
        //     'phone_number' => '0182269545'
        // ]);

        // User::create([
        //     'name' => 'daniel',
        //     'username' => 'daniel',
        //     'email' => 'daniel.ma@happyice.com.sg',
        //     'password' => 'daniel',
        //     'phone_number' => '98888888'
        // ]);

        User::create([
            'name' => 'staff',
            'username' => 'staff',
            'email' => 'staff@happyice.com.sg',
            'password' => 'staff',
            'phone_number' => '98888881'
        ]);
    }
}
