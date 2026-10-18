<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Who changed a price, a quota or a promotion, and from what to what. Prices are not secrets. */
    public function up(): void
    {
        Schema::create('setting_changes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key', 80)->index();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_changes');
    }
};
