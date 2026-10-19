<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // What the buyer picked on the landing form (`va` or `qris`); `payment_method` is what was actually paid with.
            $table->string('method_choice', 20)->nullable()->after('gateway_ref');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('method_choice');
        });
    }
};
