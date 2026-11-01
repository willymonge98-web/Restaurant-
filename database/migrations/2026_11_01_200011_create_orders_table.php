<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('type'); // dine_in | takeaway | delivery
            $table->string('status')->default('ouverte'); // ouverte | en_preparation | prete | servie | payee | annulee
            $table->foreignId('dining_table_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // waiter / cashier
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('delivery_address')->nullable();
            $table->unsignedInteger('covers')->default(1);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('cost_total', 12, 2)->default(0); // ingredient cost
            $table->boolean('stock_deducted')->default(false);
            $table->text('notes')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('product_name');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('line_total', 12, 2);
            $table->decimal('line_cost', 12, 2)->default(0);
            $table->string('kitchen_status')->default('pending'); // pending | preparing | ready | served | cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
