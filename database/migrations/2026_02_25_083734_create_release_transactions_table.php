<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('release_transactions', function (Blueprint $table) {
            $table->id();

            // Which supply request this transaction belongs to
            $table->foreignId('supply_request_id')
                  ->constrained('supply_requests')
                  ->onDelete('cascade');

            // Round number (1, 2, 3…)
            $table->unsignedTinyInteger('round')->default(1);

            // Serial number assigned for this release round
            $table->string('serial_number')->nullable();

            // Optional RO reference
            $table->string('ro_number')->nullable();

            // Notes for this round
            $table->text('notes')->nullable();

            // Who released
            $table->foreignId('released_by')
                  ->constrained('users')
                  ->onDelete('restrict');

            // Whether this round fully closed the request
            $table->boolean('is_final_release')->default(false);

            // JSON snapshot of items released this round
            // Each entry: { item_id, item_name, item_code, qty_released, qty_remaining_after, stock_before, stock_after }
            $table->json('items_snapshot');

            // Totals for quick display
            $table->unsignedInteger('total_items_in_request')->default(0);
            $table->unsignedInteger('items_fully_released_this_round')->default(0);
            $table->unsignedInteger('items_still_pending_after')->default(0);

            $table->timestamps();

            $table->index('supply_request_id');
            $table->index('serial_number');
            $table->index('released_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('release_transactions');
    }
};