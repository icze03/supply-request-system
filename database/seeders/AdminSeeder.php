<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Department;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin Department ─────────────────────────────────────────
        $adminDept = Department::firstOrCreate(
            ['code' => 'ADMIN'],
            [
                'name'             => 'Administration',
                'cost_center'      => 'CC-ADMIN-001',
                'passcode'         => '0000',
                'annual_budget'    => 0,
                'allocated_budget' => 0,
                'spent_budget'     => 0,
                'remaining_budget' => 0,
                'budget_year'      => date('Y'),
            ]
        );

        // ── Users ────────────────────────────────────────────────────
        $users = [
            [
                'name'          => 'System Administrator',
                'email'         => 'admin@guess.com',
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'admin',
                'department_id' => $adminDept->id,
            ],
            [
                'name'          => 'Manager User',
                'email'         => 'manager@guess.com',
                'password'      => Hash::make('Manager@1234'),
                'role'          => 'manager',
                'department_id' => $adminDept->id,
            ],
            [
                'name'          => 'Employee User',
                'email'         => 'employee@guess.com',
                'password'      => Hash::make('Employee@1234'),
                'role'          => 'employee',
                'department_id' => $adminDept->id,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, [
                    'email_verified_at' => now(),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ])
            );
        }

        // ── Department Page PIN ──────────────────────────────────────
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'department_page_pin'],
            [
                'value'      => Hash::make('dept1234'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // ── Users Page PIN ───────────────────────────────────────────
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'users_page_pin'],
            [
                'value'      => Hash::make('user1234'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // ── Output ───────────────────────────────────────────────────
        $this->command->info('✓ Seeded credentials:');
        $this->command->info('  Admin    → admin@guess.com    / Admin@1234');
        $this->command->info('  Manager  → manager@guess.com  / Manager@1234');
        $this->command->info('  Employee → employee@guess.com / Employee@1234');
        $this->command->newLine();
        $this->command->info('✓ Department page PIN : dept1234');
        $this->command->info('✓ Users page PIN      : user1234');
        $this->command->warn('  Change these passwords and PINs in production!');
    }
}