<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_pixels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->string('pixel_type'); // facebook, ga4, google_ads, tiktok, custom
            $table->string('pixel_id')->nullable();
            $table->json('page_placement')->nullable(); // ["registration", "thankyou", "webinar_room"]
            $table->json('custom_events')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'webinar_id']);
        });

        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('registrant_id')->nullable();
            $table->string('session_id', 64);
            $table->string('event_type'); // page_view, registration, video_load, video_play, video_pause, video_resume, video_complete, video_progress, cta_show, cta_click
            $table->json('event_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referrer_url')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'webinar_id', 'event_type']);
            $table->index(['webinar_id', 'session_id']);
            $table->index(['webinar_id', 'created_at']);
            $table->index('registrant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('tracking_pixels');
    }
};
