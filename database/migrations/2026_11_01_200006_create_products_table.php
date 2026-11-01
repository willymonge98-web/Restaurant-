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
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->nullable()->unique();
            $table->string('type')->default('food'); // food | drink
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('cost_price', 12, 2)->default(0); // computed from recipe when available
            $table->boolean('track_stock')->default(false); // for drinks sold as units
            $table->decimal('stock_qty', 12, 3)->default(0);
            $table->decimal('stock_alert', 12, 3)->default(5);
            $table->string('unit')->default('unit');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
