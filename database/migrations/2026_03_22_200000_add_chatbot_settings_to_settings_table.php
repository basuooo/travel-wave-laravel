<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'chatbot_enabled')) {
                $table->boolean('chatbot_enabled')->default(false)->after('meta_conversion_api_default_event_source_url');
            }
            if (! Schema::hasColumn('settings', 'chatbot_bot_name_en')) {
                $table->text('chatbot_bot_name_en')->nullable()->after('chatbot_enabled');
            }
            if (! Schema::hasColumn('settings', 'chatbot_bot_name_ar')) {
                $table->text('chatbot_bot_name_ar')->nullable()->after('chatbot_bot_name_en');
            }
            if (! Schema::hasColumn('settings', 'chatbot_welcome_message_en')) {
                $table->text('chatbot_welcome_message_en')->nullable()->after('chatbot_bot_name_ar');
            }
            if (! Schema::hasColumn('settings', 'chatbot_welcome_message_ar')) {
                $table->text('chatbot_welcome_message_ar')->nullable()->after('chatbot_welcome_message_en');
            }
            if (! Schema::hasColumn('settings', 'chatbot_fallback_message_en')) {
                $table->text('chatbot_fallback_message_en')->nullable()->after('chatbot_welcome_message_ar');
            }
            if (! Schema::hasColumn('settings', 'chatbot_fallback_message_ar')) {
                $table->text('chatbot_fallback_message_ar')->nullable()->after('chatbot_fallback_message_en');
            }
            if (! Schema::hasColumn('settings', 'chatbot_primary_language')) {
                $table->text('chatbot_primary_language')->nullable()->after('chatbot_fallback_message_ar');
            }
            if (! Schema::hasColumn('settings', 'chatbot_suggested_questions_en')) {
                $table->longText('chatbot_suggested_questions_en')->nullable()->after('chatbot_primary_language');
            }
            if (! Schema::hasColumn('settings', 'chatbot_suggested_questions_ar')) {
                $table->longText('chatbot_suggested_questions_ar')->nullable()->after('chatbot_suggested_questions_en');
            }
            if (! Schema::hasColumn('settings', 'chatbot_show_whatsapp_handoff')) {
                $table->boolean('chatbot_show_whatsapp_handoff')->default(true)->after('chatbot_suggested_questions_ar');
            }
            if (! Schema::hasColumn('settings', 'chatbot_show_contact_handoff')) {
                $table->boolean('chatbot_show_contact_handoff')->default(true)->after('chatbot_show_whatsapp_handoff');
            }
            if (! Schema::hasColumn('settings', 'chatbot_content_sources')) {
                $table->longText('chatbot_content_sources')->nullable()->after('chatbot_show_contact_handoff');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $columns = [
                'chatbot_enabled',
                'chatbot_bot_name_en',
                'chatbot_bot_name_ar',
                'chatbot_welcome_message_en',
                'chatbot_welcome_message_ar',
                'chatbot_fallback_message_en',
                'chatbot_fallback_message_ar',
                'chatbot_primary_language',
                'chatbot_suggested_questions_en',
                'chatbot_suggested_questions_ar',
                'chatbot_show_whatsapp_handoff',
                'chatbot_show_contact_handoff',
                'chatbot_content_sources',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
