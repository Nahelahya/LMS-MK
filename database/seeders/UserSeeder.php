<?php

namespace Database\Seeders;

use App\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Symfony\Component\Mime\Email;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::Create(['name'=> 'admin', 'email' => 'admin@gmail.com', 'password'=> 'admin', 'role' => 'admin', 'status' => 'active']);
        User::Create(['name'=> 'staff', 'email' => 'staff@gmail.com', 'password'=> 'staff', 'role' => 'staff', 'status' => 'active']);
        User::Create(['name'=> 'student', 'email' => 'student@gmail.com', 'password'=> 'student', 'role' => 'student', 'status' => 'active']);
    }
}