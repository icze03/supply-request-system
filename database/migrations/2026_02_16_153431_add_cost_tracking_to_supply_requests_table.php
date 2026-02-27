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
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->decimal('estimated_cost', 15, 2)->nullable()->after('budget_type');
            $table->decimal('actual_cost', 15, 2)->nullable()->after('estimated_cost');
            $table->boolean('budget_deducted')->default(false)->after('actual_cost');
            $table->timestamp('budget_deducted_at')->nullable()->after('budget_deducted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->dropColumn([
                'estimated_cost',
                'actual_cost',
                'budget_deducted',
                'budget_deducted_at'
            ]);
        });
    }
};