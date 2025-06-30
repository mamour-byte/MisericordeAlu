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
        $table->unsignedBigInteger('invoice_id')->nullable()->after('id')->constraints('invoices', 'id')->onDelete('cascade');
        $table->unsignedBigInteger('quote_id')->nullable()->after('invoice_id')->constraints('quotes', 'id')->onDelete('cascade');;
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            //
        });
    }
};
