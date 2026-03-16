<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend the role ENUM to include super_admin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('employee','manager','admin','super_admin') NOT NULL DEFAULT 'employee'");
    }

    public function down(): void
    {
        // Demote any super_admin users before removing the enum value
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('employee','manager','admin') NOT NULL DEFAULT 'employee'");
    }
};
