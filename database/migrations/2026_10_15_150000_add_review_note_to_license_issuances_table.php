<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('license_issuances', function (Blueprint $table) {
            // Why a row waits for an admin besides an unverified sale (free license, compensation, unpaid extension).
            $table->string('review_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('license_issuances', function (Blueprint $table) {
            $table->dropColumn('review_note');
        });
    }
};
