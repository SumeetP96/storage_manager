<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'      => 'name',
            'username'  => 'admin',
            'password'  => Hash::make('pass'),
            'dm_token'  => Hash::make('full-version')
        ]);
    }
}
