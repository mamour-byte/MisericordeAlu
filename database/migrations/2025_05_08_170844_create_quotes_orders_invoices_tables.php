<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['quote', 'order', 'invoice'] as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();
                $table->string('customer_name');
                $table->string('customer_email');
                $table->string('customer_phone')->nullable();
                $table->string('customer_address')->nullable();
                $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
                $table->decimal('total_amount', 10, 2);
                $table->timestamps();
            });

            Schema::create("{$table}_items", function (Blueprint $itemTable) use ($table) {
                $itemTable->id();

                $itemTable->foreignId("{$table}_id")->constrained($table)->onDelete('cascade');

                $itemTable->string("no_{$table}");

                $itemTable->foreignId('product_id')->constrained()->onDelete('cascade');

                $itemTable->integer('quantity');
                $itemTable->decimal('unit_price', 10, 2);
                $itemTable->timestamps();
            });
        }
    }

    public function down(): void
    {
        foreach (['quotes', 'orders', 'invoices'] as $table) {
            Schema::dropIfExists("{$table}_items");
            Schema::dropIfExists($table);
        }
    }
};
