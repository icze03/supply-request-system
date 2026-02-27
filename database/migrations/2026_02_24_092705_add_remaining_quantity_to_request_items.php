<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->integer('original_quantity')->nullable()->after('quantity');
            $table->integer('remaining_quantity')->nullable()->after('original_quantity');
            $table->integer('released_quantity')->default(0)->after('remaining_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropColumn(['original_quantity', 'remaining_quantity', 'released_quantity']);
        });
    }
};