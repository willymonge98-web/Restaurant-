<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('movable_type'); // Ingredient | Product
            $table->unsignedBigInteger('movable_id');
            $table->string('type'); // in | out | adjust | sale | loss | cancel_restore
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_cost', 12, 4)->nullable();
            $table->decimal('stock_before', 14, 4)->nullable();
            $table->decimal('stock_after', 14, 4)->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['movable_type', 'movable_id']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
