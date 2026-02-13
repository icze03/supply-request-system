<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 20)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('unit');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('item_code');
            $table->index('category');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};