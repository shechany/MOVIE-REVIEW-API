<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(
            [
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => "johndoe@gmail.com", // change this to an email address you have access to.
            'role' => 1, 
            'password' => bcrypt("password")
        ]);
    }
}
