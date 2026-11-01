<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->nullable()->unique();
            $table->string('unit')->default('g'); // g, kg, ml, L, unit
            $table->decimal('stock_qty', 14, 3)->default(0);
            $table->decimal('stock_alert', 14, 3)->default(0);
            $table->decimal('unit_cost', 12, 4)->default(0); // cost per unit
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
