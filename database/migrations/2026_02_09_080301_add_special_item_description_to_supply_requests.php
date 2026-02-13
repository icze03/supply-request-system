<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('supply_requests', 'special_item_description')) {
                $table->text('special_item_description')->nullable()->after('purpose');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->dropColumn('special_item_description');
        });
    }
};