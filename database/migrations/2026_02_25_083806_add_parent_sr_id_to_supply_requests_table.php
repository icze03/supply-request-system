<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            // Tracks which SR this was re-queued from
            $table->foreignId('parent_sr_id')
                  ->nullable()
                  ->after('admin_notes')
                  ->constrained('supply_requests')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->dropForeign(['parent_sr_id']);
            $table->dropColumn('parent_sr_id');
        });
    }
};