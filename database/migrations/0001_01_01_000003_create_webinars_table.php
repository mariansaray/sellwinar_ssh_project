<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('type')->default('evergreen'); // evergreen, smart_video
            $table->string('video_source')->nullable(); // youtube, vimeo, custom
            $table->string('video_url')->nullable();
            $table->unsignedInteger('video_duration_seconds')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->json('player_config')->nullable();
            $table->json('schedule_config')->nullable();
            $table->json('registration_page_config')->nullable();
            $table->json('thankyou_page_config')->nullable();
            $table->boolean('chat_enabled')->default(true);
            $table->json('cta_config')->nullable();
            $table->string('status')->default('draft'); // draft, active, paused, archived
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'type']);
        });

        Schema::create('webinar_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('schedule_type')->default('jit'); // jit, fixed, interval
            $table->json('fixed_times')->nullable();
            $table->unsignedInteger('jit_delay_minutes')->default(15);
            $table->unsignedInteger('interval_hours')->nullable();
            $table->string('timezone')->default('Europe/Bratislava');
            $table->boolean('hide_night_times')->default(true);
            $table->timestamps();

            $table->index('webinar_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_schedules');
        Schema::dropIfExists('webinars');
    }
};
