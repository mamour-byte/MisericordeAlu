<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert stock_min column to DECIMAL(10,2) to match stock_quantity
     */
    public function up(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'stock_min')) {
            DB::statement("ALTER TABLE `products` MODIFY COLUMN `stock_min` DECIMAL(10,2) NOT NULL DEFAULT 0");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'stock_min')) {
            DB::statement("ALTER TABLE `products` MODIFY COLUMN `stock_min` INT NOT NULL DEFAULT 0");
        }
    }
};
