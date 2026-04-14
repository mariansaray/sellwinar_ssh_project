<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('buyer_name');
            $table->string('product_name');
            $table->unsignedInteger('display_at_seconds');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['webinar_id', 'display_at_seconds']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_alerts');
    }
};
