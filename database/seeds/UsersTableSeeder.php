<?php

use Illuminate\Database\Seeder;
use App\model\role\role;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();


        $admin =User::create([
            'name'=>'developer',
            'email'=>'developer@developer.com',
            'password'=>bcrypt('developer')
        ]);

        $admin->assignRole('Super Admin');

    }
}
