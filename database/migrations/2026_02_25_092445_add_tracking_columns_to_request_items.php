<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            if (!Schema::hasColumn('request_items', 'original_quantity')) {
                $table->integer('original_quantity')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('request_items', 'released_quantity')) {
                $table->integer('released_quantity')->default(0)->after('original_quantity');
            }
            if (!Schema::hasColumn('request_items', 'remaining_quantity')) {
                $table->integer('remaining_quantity')->nullable()->after('released_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropColumn(['original_quantity', 'released_quantity', 'remaining_quantity']);
        });
    }
};