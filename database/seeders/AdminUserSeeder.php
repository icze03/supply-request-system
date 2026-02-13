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
            'email' => 'admin@supply.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department_id' => 1, // IT Department
        ]);

        // Create IT Manager
        User::create([
            'name' => 'IT Manager',
            'email' => 'manager.it@supply.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department_id' => 1,
        ]);

        // Create HR Manager
        User::create([
            'name' => 'HR Manager',
            'email' => 'manager.hr@supply.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department_id' => 2,
        ]);

        // Create Finance Manager
        User::create([
            'name' => 'Finance Manager',
            'email' => 'manager.fin@supply.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department_id' => 3,
        ]);

        // Create Sample Employees
        User::create([
            'name' => 'John Employee',
            'email' => 'employee@supply.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department_id' => 1,
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@supply.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department_id' => 2,
        ]);

        User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@supply.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department_id' => 3,
        ]);
    }
}