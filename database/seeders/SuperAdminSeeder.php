<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@guess.com'],
            [
                'name'          => 'Super Administrator',
                'password'      => Hash::make('SuperAdmin1234'),
                'role'          => 'super_admin',
                'department_id' => null,
            ]
        );

        $this->command->info('✓ Super Admin user created');
        $this->command->info('  Email    : superadmin@guess.com');
        $this->command->info('  Password : SuperAdmin1234');
        $this->command->warn('  ⚠ Change this password before going to production!');
    }
}
