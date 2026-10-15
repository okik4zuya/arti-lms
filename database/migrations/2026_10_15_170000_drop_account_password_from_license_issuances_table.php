<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The temporary learner password is no longer stored: it is shown once in the key modal, and "Reset password
 * akun" issues a new one. Guarded so it also runs on databases where the earlier column was never created.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('license_issuances', 'account_password')) {
            Schema::table('license_issuances', function (Blueprint $table) {
                $table->dropColumn('account_password');
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty: the stored password is not coming back.
    }
};
