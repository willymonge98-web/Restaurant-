<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('LÉNA RESTAURANT');
            $table->string('logo_path')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('currency_primary', 10)->default('USD');
            $table->string('currency_secondary', 10)->default('CDF');
            $table->decimal('exchange_rate', 14, 4)->default(2800); // 1 USD = X CDF
            $table->text('ticket_footer')->nullable();
            $table->string('tax_label')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_settings');
    }
};
