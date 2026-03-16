<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // Display label e.g. "Releases"
            $table->string('slug')->unique();   // Machine key e.g. "releases"
            $table->string('route_pattern');    // fnmatch pattern e.g. "admin.releases.*"
            $table->string('url_prefix')->nullable(); // URL prefix e.g. "/admin/releases"
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
