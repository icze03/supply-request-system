<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->string('sr_number')->unique()->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->dropColumn('sr_number');
        });
    }
};