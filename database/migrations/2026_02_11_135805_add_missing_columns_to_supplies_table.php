<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplies', function (Blueprint $table) {
            if (!Schema::hasColumn('supplies', 'item_code')) {
                $table->string('item_code')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('supplies', 'stock_quantity')) {
                $table->integer('stock_quantity')->default(0)->after('unit');
            }
            if (!Schema::hasColumn('supplies', 'minimum_stock')) {
                $table->integer('minimum_stock')->default(10)->after('stock_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supplies', function (Blueprint $table) {
            $table->dropColumn(['item_code', 'stock_quantity', 'minimum_stock']);
        });
    }
};