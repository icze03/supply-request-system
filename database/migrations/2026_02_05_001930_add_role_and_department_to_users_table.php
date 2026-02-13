<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['employee', 'manager', 'admin'])
                  ->default('employee')->after('email');
            $table->foreignId('department_id')
                  ->nullable()->constrained()->onDelete('set null')->after('role');
            
            $table->index('role');
            $table->index('department_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropIndex(['role']);
            $table->dropIndex(['department_id']);
            $table->dropColumn(['role', 'department_id']);
        });
    }
};