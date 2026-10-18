<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gateway orders. `public_id` is the id the gateway sees (Midtrans `order_id`, Xendit `external_id`) and is also the
     * license `order_ref`, so one order can never issue two keys. `launch_slot` (1..quota) is unique:
     * the database, not the application, is what stops two payments from sharing the last launch place.
     */
    public function up(): void
    {
        // Time-limited promotions (admin-edited). A promotion with a quota hands out `promo_slot` numbers the same way.
        Schema::create('promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('label', 120);
            $table->unsignedInteger('price');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->unsignedSmallInteger('quota')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('public_id', 40)->unique();
            $table->string('type', 20)->default('purchase');
            $table->string('name');
            $table->string('email')->index();
            $table->string('whatsapp', 30)->nullable();
            $table->unsignedInteger('amount');
            $table->string('price_tier', 20);
            $table->unsignedSmallInteger('launch_slot')->nullable()->unique();
            $table->foreignUuid('promotion_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('promo_slot')->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->string('gateway', 20)->default('midtrans'); // midtrans | xendit; an order keeps the gateway it was created with
            $table->string('gateway_ref')->nullable()->unique();
            $table->text('checkout_url')->nullable();
            $table->string('payment_method', 60)->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('license_issuance_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('fulfilled_at')->nullable();
            $table->dateTime('email_sent_at')->nullable();
            $table->dateTime('whatsapp_sent_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->string('refund_reason')->nullable();
            $table->string('attention_note')->nullable();
            $table->timestamps();

            $table->unique(['promotion_id', 'promo_slot']);
        });

        // What happened to an order. Never holds a key, a token or a password.
        Schema::create('order_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 40);
            $table->string('detail')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_events');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('promotions');
    }
};
