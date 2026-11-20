<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['id' => 1], [
            'username' => config('developer.username'),
            'full_name' => config('developer.name'),
            'email' => config('developer.email'),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
    }
}
