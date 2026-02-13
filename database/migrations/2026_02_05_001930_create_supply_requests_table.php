<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_requests', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique()->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->enum('request_type', ['standard', 'special'])->default('standard');
            $table->text('purpose')->nullable();
            $table->text('special_item_description')->nullable();
            
            $table->enum('status', [
                'pending', 'manager_approved', 'manager_rejected',
                'admin_released', 'admin_rejected'
            ])->default('pending');
            
            $table->foreignId('manager_approved_by')
                  ->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('manager_approved_at')->nullable();
            $table->text('manager_notes')->nullable();
            
            $table->foreignId('admin_released_by')
                  ->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('admin_released_at')->nullable();
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
            
            $table->index('serial_number');
            $table->index('user_id');
            $table->index('department_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_requests');
    }
};