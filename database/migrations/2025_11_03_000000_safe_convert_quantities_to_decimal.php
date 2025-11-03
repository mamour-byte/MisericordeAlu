<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration uses raw ALTER TABLE statements so it does not require doctrine/dbal.
     * It converts integer quantity columns to DECIMAL(10,2) preserving existing values.
     */
    public function up(): void
    {
        // stock_movements.quantity
        if (Schema::hasTable('stock_movements') && Schema::hasColumn('stock_movements', 'quantity')) {
            DB::statement("ALTER TABLE `stock_movements` MODIFY COLUMN `quantity` DECIMAL(10,2) NOT NULL");
        }

        // products.stock_quantity
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'stock_quantity')) {
            DB::statement("ALTER TABLE `products` MODIFY COLUMN `stock_quantity` DECIMAL(10,2) NOT NULL DEFAULT 0");
        }

        // order_items.quantity
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'quantity')) {
            DB::statement("ALTER TABLE `order_items` MODIFY COLUMN `quantity` DECIMAL(10,2) NOT NULL");
        }

        // invoice_items.quantity
        if (Schema::hasTable('invoice_items') && Schema::hasColumn('invoice_items', 'quantity')) {
            DB::statement("ALTER TABLE `invoice_items` MODIFY COLUMN `quantity` DECIMAL(10,2) NOT NULL");
        }

        // quote_items.quantity
        if (Schema::hasTable('quote_items') && Schema::hasColumn('quote_items', 'quantity')) {
            DB::statement("ALTER TABLE `quote_items` MODIFY COLUMN `quantity` DECIMAL(10,2) NOT NULL");
        }

        // avoir_items.quantity
        if (Schema::hasTable('avoir_items') && Schema::hasColumn('avoir_items', 'quantity')) {
            DB::statement("ALTER TABLE `avoir_items` MODIFY COLUMN `quantity` DECIMAL(10,2) NOT NULL");
        }

        // fabrication_items.quantity (optional)
        if (Schema::hasTable('fabrication_items') && Schema::hasColumn('fabrication_items', 'quantity')) {
            DB::statement("ALTER TABLE `fabrication_items` MODIFY COLUMN `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1");
        }
    }

    /**
     * Reverse the migrations.
     * Note: reverting to integer will truncate decimals; run only if you accept losing fractional part.
     */
    public function down(): void
    {
        if (Schema::hasTable('stock_movements') && Schema::hasColumn('stock_movements', 'quantity')) {
            DB::statement("ALTER TABLE `stock_movements` MODIFY COLUMN `quantity` INT NOT NULL");
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'stock_quantity')) {
            DB::statement("ALTER TABLE `products` MODIFY COLUMN `stock_quantity` INT NOT NULL DEFAULT 0");
        }

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'quantity')) {
            DB::statement("ALTER TABLE `order_items` MODIFY COLUMN `quantity` INT NOT NULL");
        }

        if (Schema::hasTable('invoice_items') && Schema::hasColumn('invoice_items', 'quantity')) {
            DB::statement("ALTER TABLE `invoice_items` MODIFY COLUMN `quantity` INT NOT NULL");
        }

        if (Schema::hasTable('quote_items') && Schema::hasColumn('quote_items', 'quantity')) {
            DB::statement("ALTER TABLE `quote_items` MODIFY COLUMN `quantity` INT NOT NULL");
        }

        if (Schema::hasTable('avoir_items') && Schema::hasColumn('avoir_items', 'quantity')) {
            DB::statement("ALTER TABLE `avoir_items` MODIFY COLUMN `quantity` INT NOT NULL");
        }

        if (Schema::hasTable('fabrication_items') && Schema::hasColumn('fabrication_items', 'quantity')) {
            DB::statement("ALTER TABLE `fabrication_items` MODIFY COLUMN `quantity` INT NOT NULL DEFAULT 1");
        }
    }
};
