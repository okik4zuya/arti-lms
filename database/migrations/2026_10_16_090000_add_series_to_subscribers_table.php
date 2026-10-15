<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            // How many emails of the welcome series this person has been queued so far (0 = none yet).
            $table->unsignedTinyInteger('series_step')->default(0)->after('buyer_at');
            $table->timestamp('series_sent_at')->nullable()->after('series_step');
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn(['series_step', 'series_sent_at']);
        });
    }
};
