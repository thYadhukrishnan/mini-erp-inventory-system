<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->decimal('unit_cost', 10, 2)->default(0.00);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(10);
            $table->timestamps();

            // Composite index for low-stock queries
            $table->index(['stock', 'low_stock_threshold']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
