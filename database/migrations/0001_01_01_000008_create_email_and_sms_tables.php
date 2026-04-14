<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('smtp_host')->nullable();
            $table->unsignedSmallInteger('smtp_port')->default(587);
            $table->string('smtp_user')->nullable();
            $table->text('smtp_pass_encrypted')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('reply_to')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index('tenant_id');
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->string('trigger_type'); // registration_confirmed, reminder_24h, reminder_1h, reminder_15m, reminder_5m, missed, replay
            $table->string('subject');
            $table->longText('body_html');
            $table->boolean('is_active')->default(true);
            $table->integer('delay_minutes')->default(0); // negative = before scheduled_at
            $table->timestamps();

            $table->index(['webinar_id', 'trigger_type']);
        });

        Schema::create('sms_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('twilio_sid')->nullable();
            $table->text('twilio_token_encrypted')->nullable();
            $table->string('twilio_phone')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index('tenant_id');
        });

        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->string('trigger_type');
            $table->string('message_text', 320); // SMS max ~160 chars but allow for templates
            $table->boolean('is_active')->default(true);
            $table->integer('delay_minutes')->default(0);
            $table->timestamps();

            $table->index(['webinar_id', 'trigger_type']);
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('registrant_id')->nullable();
            $table->string('channel'); // email, sms
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('status')->default('queued'); // queued, sent, delivered, failed, bounced
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'channel']);
            $table->index(['registrant_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('sms_configs');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('email_configs');
    }
};
