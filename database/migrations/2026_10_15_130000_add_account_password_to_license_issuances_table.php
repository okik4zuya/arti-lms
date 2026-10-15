<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `account_password` keeps the temporary learner password, encrypted with APP_KEY like the key
     * column, so the admin can put it in the WhatsApp message. Only set for accounts created together
     * with the license.
     */
    public function up(): void
    {
        Schema::table('license_issuances', function (Blueprint $table) {
            $table->text('account_password')->nullable()->after('key_checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_issuances', function (Blueprint $table) {
            $table->dropColumn('account_password');
        });
    }
};
