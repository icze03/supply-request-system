<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('supply_id')->nullable()->constrained()->onDelete('set null');
            $table->string('item_name');
            $table->string('item_code');
            $table->integer('quantity');
            $table->timestamps();
            
            $table->index('supply_request_id');
            $table->index('supply_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_items');
    }
};