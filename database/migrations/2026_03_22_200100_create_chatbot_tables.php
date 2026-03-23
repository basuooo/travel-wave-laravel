<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_knowledge_items', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 80);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('source_key', 120)->nullable();
            $table->string('locale', 10)->default('ar');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('url')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['source_type', 'locale']);
            $table->index(['source_type', 'source_id']);
            $table->index(['source_key', 'locale']);
        });

        Schema::create('chatbot_interactions', function (Blueprint $table) {
            $table->id();
            $table->string('session_key', 100)->nullable();
            $table->string('locale', 10)->default('ar');
            $table->text('question');
            $table->longText('answer')->nullable();
            $table->json('matched_sources')->nullable();
            $table->boolean('was_answered')->default(false);
            $table->boolean('used_handoff')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['was_answered', 'created_at']);
            $table->index(['locale', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_interactions');
        Schema::dropIfExists('chatbot_knowledge_items');
    }
};
