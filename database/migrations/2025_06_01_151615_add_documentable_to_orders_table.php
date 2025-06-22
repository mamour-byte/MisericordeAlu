<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('quote_id')->nullable();

            $table->foreign('invoice_id')->references('id')->on('invoice')->nullOnDelete();
            $table->foreign('quote_id')->references('id')->on('quotecl')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['quote_id']);
            $table->dropColumn(['invoice_id', 'quote_id']);
        });
    }
};
