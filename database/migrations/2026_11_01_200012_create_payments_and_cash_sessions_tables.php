<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('open'); // open | closed
            $table->decimal('opening_float', 12, 2)->default(0);
            $table->decimal('closing_counted', 12, 2)->nullable();
            $table->decimal('theoretical_cash', 12, 2)->nullable();
            $table->decimal('difference', 12, 2)->nullable();
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->json('totals_by_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cash_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method'); // cash | card | mpesa | orange_money | airtel_money
            $table->string('currency', 10)->default('USD');
            $table->decimal('amount', 12, 2);
            $table->decimal('amount_usd', 12, 2)->nullable();
            $table->string('reference')->nullable(); // mobile money refs
            $table->string('status')->default('completed'); // completed | pending | failed | refunded
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('cash_sessions');
    }
};
