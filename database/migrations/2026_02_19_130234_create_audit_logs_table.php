<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // e.g., 'request_created', 'request_approved', 'supply_released'
            $table->string('model_type')->nullable(); // e.g., 'App\Models\SupplyRequest'
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected model
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Who performed the action
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete(); // Which department
            $table->string('ip_address', 45)->nullable(); // User's IP address
            $table->string('user_agent')->nullable(); // Browser/device info
            $table->json('old_values')->nullable(); // Data before change
            $table->json('new_values')->nullable(); // Data after change
            $table->text('description')->nullable(); // Human-readable description
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for performance
            $table->index('action');
            $table->index('model_type');
            $table->index('model_id');
            $table->index('user_id');
            $table->index('department_id');
            $table->index('created_at');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};