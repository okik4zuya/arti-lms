<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('license_issuances', function (Blueprint $table) {
            $table->string('payment_ref')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->foreignUuid('payment_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('payment_verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('license_issuances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_verified_by');
            $table->dropColumn(['payment_ref', 'payment_proof_path', 'payment_verified_at']);
        });
    }
};
