<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // WhatsApp Conversations
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('wa_id', 50)->unique()->comment('WhatsApp phone number e.g. 201012345678');
            $table->string('contact_name')->nullable();
            $table->string('locale', 10)->default('ar');
            $table->enum('status', ['active', 'human_handover', 'closed'])->default('active');
            $table->boolean('ai_active')->default(true)->comment('Whether AI replies are on');
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->json('metadata')->nullable()->comment('lead score, crm_lead_id, etc.');
            $table->timestamps();

            $table->foreign('assigned_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['status', 'last_message_at']);
            $table->index('ai_active');
        });

        // WhatsApp Messages
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->string('wa_message_id', 100)->nullable()->unique()->comment('WhatsApp message_id from API');
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('message_type', ['text', 'image', 'audio', 'document', 'interactive', 'template', 'unknown'])->default('text');
            $table->text('body')->nullable();
            $table->json('payload')->nullable()->comment('Full WhatsApp message payload');
            $table->string('ai_provider', 30)->nullable()->comment('Which AI generated this reply');
            $table->string('status', 30)->default('sent')->comment('sent|delivered|read|failed');
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('whatsapp_conversations')->cascadeOnDelete();
            $table->index(['conversation_id', 'created_at']);
            $table->index(['direction', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
    }
};
