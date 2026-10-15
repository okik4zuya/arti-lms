<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per download name and day; holds a number only, never anything about the visitor.
        Schema::create('download_counts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32);
            $table->date('day');
            $table->unsignedInteger('downloads')->default(0);
            $table->unique(['name', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_counts');
    }
};
