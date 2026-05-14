<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table for AI Providers configuration
        Schema::create('ai_bot_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'default' or 'whatsapp_bot'
            $table->boolean('enabled')->default(false);
            $table->string('provider')->default('openai'); // openai, gemini, deepseek, claude
            $table->text('system_prompt_ar')->nullable();
            $table->text('system_prompt_en')->nullable();
            
            // Credentials (Stored as encrypted/text to handle large keys)
            $table->text('openai_api_key')->nullable();
            $table->string('openai_model')->default('gpt-4o-mini');
            
            $table->text('gemini_api_key')->nullable();
            $table->string('gemini_model')->default('gemini-1.5-flash');
            
            $table->text('deepseek_api_key')->nullable();
            $table->string('deepseek_model')->default('deepseek-chat');
            
            $table->text('claude_api_key')->nullable();
            $table->string('claude_model')->default('claude-3-haiku-20240307');
            
            // Advanced Params
            $table->integer('max_tokens')->default(1000);
            $table->float('temperature')->default(0.7);
            $table->boolean('fallback_to_keyword')->default(true);
            
            $table->timestamps();
        });

        // Table for WhatsApp Cloud API specific settings
        Schema::create('whatsapp_configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->text('access_token')->nullable();
            $table->string('phone_number_id')->nullable();
            $table->string('business_account_id')->nullable();
            $table->string('verify_token')->nullable();
            $table->string('handover_keyword')->default('وكيل');
            $table->boolean('human_handover_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_bot_configs');
        Schema::dropIfExists('whatsapp_configs');
    }
};
