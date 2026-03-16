<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,       // system_settings PINs
            AdminUserSeeder::class,   // admin@guess.com
            SuperAdminSeeder::class,  // superadmin@system.com  ← NEW
            PermissionSeeder::class,  // permissions + role_permissions ← NEW
            SupplySeeder::class,      // sample supplies
        ]);
    }
}
