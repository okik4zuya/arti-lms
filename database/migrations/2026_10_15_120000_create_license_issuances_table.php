<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Commercial record of licenses issued through the licensing Worker. The Worker stays the
     * authority for status, dates, seats and activations; the columns here are a display copy.
     * `key` holds the license key encrypted with APP_KEY so the admin can send it again.
     */
    public function up(): void
    {
        Schema::create('license_issuances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key_id')->nullable()->unique();
            $table->text('key')->nullable();
            $table->boolean('key_stale')->default(false);
            $table->timestamp('key_checked_at')->nullable();
            $table->string('order_ref')->unique();
            $table->string('source', 20)->default('manual');
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('tier', 20);
            $table->unsignedSmallInteger('seats')->nullable();
            $table->unsignedSmallInteger('months');
            $table->unsignedInteger('amount_idr')->nullable();
            $table->string('status', 30)->default('pending');
            // dateTime, not timestamp: a MySQL TIMESTAMP stops at 2038 and long (site) licenses can pass it.
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('updates_until')->nullable();
            $table->unsignedSmallInteger('activations_count')->default(0);
            $table->string('buyer_whatsapp', 30)->nullable();
            $table->timestamp('wa_sent_at')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->text('sync_error')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('status');
        });

        Schema::create('license_issuance_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('license_issuance_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 40);
            $table->string('detail')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_issuance_events');
        Schema::dropIfExists('license_issuances');
    }
};
