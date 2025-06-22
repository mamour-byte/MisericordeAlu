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
        Schema::create('avoir_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('avoir_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->string('no_avoir'); 
            $table->string('no_order')->nullable(); 
            $table->timestamps();

            $table->foreign('avoir_id')->references('id')->on('avoirs')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
