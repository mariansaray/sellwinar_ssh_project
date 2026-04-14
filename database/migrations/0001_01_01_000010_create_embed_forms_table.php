<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('embed_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->json('form_config')->nullable();
            $table->json('domain_restrictions')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'webinar_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embed_forms');
    }
};
