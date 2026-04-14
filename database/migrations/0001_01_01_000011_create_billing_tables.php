<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('stripe_price_id')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('interval'); // monthly, yearly, lifetime
            $table->unsignedInteger('max_webinars')->nullable(); // null = unlimited
            $table->unsignedInteger('max_registrants')->nullable(); // null = unlimited
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('billing_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_invoice_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('status'); // paid, failed, pending, refunded
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_history');
        Schema::dropIfExists('billing_plans');
    }
};
