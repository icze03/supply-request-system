<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin doesn't belong to a department — null avoids FK constraint failure
        User::firstOrCreate(
            ['email' => 'admin@guess.com'],
            [
                'name'          => 'System Administrator',
                'password'      => Hash::make('Admin1234'),
                'role'          => 'admin',
                'department_id' => 1,
            ]
        );
    }
}