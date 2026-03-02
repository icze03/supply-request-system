<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Department Page PIN ──────────────────────────────────────
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'department_page_pin'],
            [
                'value'      => Hash::make('123456'),
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
        $this->command->info('✓ Department page PIN : 123456');
        $this->command->info('✓ Users page PIN      : user1234');
        $this->command->warn('  Change these PINs in production!');
    }
}