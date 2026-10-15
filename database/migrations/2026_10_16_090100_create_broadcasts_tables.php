<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->longText('body');                 // Markdown, written by an admin
            $table->string('audience', 20);           // non_buyers | all_active
            $table->string('status', 20)->default('draft'); // draft | sending | sent
            $table->unsignedInteger('recipients_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // One row per person per broadcast: the unique key is what makes a resumed or repeated send safe.
        Schema::create('broadcast_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcast_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscriber_id')->constrained()->cascadeOnDelete();
            $table->timestamp('queued_at')->useCurrent();
            $table->unique(['broadcast_id', 'subscriber_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_deliveries');
        Schema::dropIfExists('broadcasts');
    }
};
