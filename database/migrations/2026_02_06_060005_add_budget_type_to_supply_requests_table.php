<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->enum('budget_type', ['budgeted', 'not_budgeted'])
                ->default('budgeted')
                ->after('request_type');
        });
    }

    public function down(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->dropColumn('budget_type');
        });
    }
};