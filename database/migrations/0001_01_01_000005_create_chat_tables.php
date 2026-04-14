<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages_fake', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('sender_name');
            $table->text('message_text');
            $table->unsignedInteger('display_at_seconds');
            $table->string('message_type')->default('message'); // message, question, reaction, system
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['webinar_id', 'display_at_seconds']);
        });

        Schema::create('chat_messages_real', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('registrant_id')->constrained()->cascadeOnDelete();
            $table->string('session_id', 64);
            $table->string('sender_name');
            $table->text('message_text');
            $table->boolean('is_admin_reply')->default(false);
            $table->unsignedBigInteger('reply_to_registrant_id')->nullable();
            $table->boolean('is_read_by_admin')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['webinar_id', 'registrant_id']);
            $table->index(['webinar_id', 'is_read_by_admin']);
        });

        Schema::create('chat_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('viewer_count_min')->default(45);
            $table->unsignedInteger('viewer_count_max')->default(120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_configs');
        Schema::dropIfExists('chat_messages_real');
        Schema::dropIfExists('chat_messages_fake');
    }
};
