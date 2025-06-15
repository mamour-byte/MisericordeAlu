<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fabrication_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabrication_id')->constrained('fabrications')->onDelete('cascade');
            $table->text('type');
            $table->integer('width');
            $table->integer('height');
            $table->integer('depth')->nullable();
            $table->integer('price_meter')->default(0);
            $table->integer('quantity')->default(1);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabrication_items');
    }
};
