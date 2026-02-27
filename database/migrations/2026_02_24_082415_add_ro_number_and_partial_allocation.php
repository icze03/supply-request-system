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
        // Add RO number to supply_requests table
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->string('ro_number')->nullable()->after('serial_number');
        });
        
        // Add partial allocation fields to request_items table
        Schema::table('request_items', function (Blueprint $table) {
            $table->integer('allocated_quantity')->default(0)->after('quantity');
            $table->boolean('is_fully_allocated')->default(false)->after('allocated_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_requests', function (Blueprint $table) {
            $table->dropColumn('ro_number');
        });
        
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropColumn(['allocated_quantity', 'is_fully_allocated']);
        });
    }
};