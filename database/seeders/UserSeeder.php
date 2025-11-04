<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Silvi',
            'email' => 'silvi@admin.com',
            'password' => Hash::make('12345'),
        ]);

        User::create([
            'name' => 'Manager',
            'email' => 'manager@logistik.test',
            'password' => Hash::make('12345'),
        ]);
    }
}