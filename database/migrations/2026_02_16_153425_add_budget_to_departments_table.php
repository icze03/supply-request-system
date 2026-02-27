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
        Schema::table('departments', function (Blueprint $table) {
            // Add columns WITHOUT 'after' clause first
            $table->decimal('annual_budget', 15, 2)->default(0);
            $table->decimal('allocated_budget', 15, 2)->default(0);
            $table->decimal('spent_budget', 15, 2)->default(0);
            $table->decimal('remaining_budget', 15, 2)->default(0);
            $table->year('budget_year')->default(date('Y'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn([
                'annual_budget',
                'allocated_budget',
                'spent_budget',
                'remaining_budget',
                'budget_year'
            ]);
        });
    }
};