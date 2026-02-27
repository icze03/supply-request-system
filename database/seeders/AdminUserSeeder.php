<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create System Administrator
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@system.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'department_id' => 1, // IT Department
        ]);



       

    }
}