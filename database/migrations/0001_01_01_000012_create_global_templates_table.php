<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates_global', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // user-friendly name, e.g. "Potvrdenie registrácie — profesionálny"
            $table->string('trigger_type'); // registration_confirmed, reminder_24h, etc.
            $table->string('subject');
            $table->longText('body_html');
            $table->integer('delay_minutes')->default(0);
            $table->boolean('is_default')->default(false); // system default vs user-created
            $table->timestamps();

            $table->index(['tenant_id', 'trigger_type']);
        });

        Schema::create('sms_templates_global', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('trigger_type');
            $table->string('message_text', 320);
            $table->integer('delay_minutes')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['tenant_id', 'trigger_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_templates_global');
        Schema::dropIfExists('email_templates_global');
    }
};
