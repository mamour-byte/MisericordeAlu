<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: Changing column types requires the doctrine/dbal package.
     * If you don't have it installed run: composer require doctrine/dbal
     */
    public function up(): void
    {
        // Convert various quantity columns to decimal(10,2)
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock_quantity', 10, 2)->default(0)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
        });

        Schema::table('avoir_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->change();
        });

        // If you also need fabrication_items to support decimals, uncomment below
        // Schema::table('fabrication_items', function (Blueprint $table) {
        //     $table->decimal('quantity', 10, 2)->change();
        // });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('avoir_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        // Schema::table('fabrication_items', function (Blueprint $table) {
        //     $table->integer('quantity')->default(1)->change();
        // });
    }
};
