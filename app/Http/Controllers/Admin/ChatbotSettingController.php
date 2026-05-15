<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotInteraction;
use App\Models\ChatbotKnowledgeEntry;
use App\Models\ChatbotKnowledgeItem;
use App\Models\Setting;
use App\Support\ChatbotKnowledgeManager;
use Illuminate\Http\Request;

class ChatbotSettingController extends Controller
{
    public function __construct(
        protected ChatbotKnowledgeManager $knowledgeManager
    ) {
    }

    public function edit()
    {
        $setting = Setting::query()->firstOrCreate([]);

        return view('admin.chatbot-settings.edit', [
            'setting' => $setting,
            'sourceOptions' => $this->knowledgeManager->sourceOptions(),
            'knowledgeCount' => ChatbotKnowledgeItem::query()->count(),
            'manualKnowledgeCount' => ChatbotKnowledgeEntry::query()->count(),
            'latestInteractions' => ChatbotInteraction::query()->latest()->limit(20)->get(),
            'unansweredCount' => ChatbotInteraction::query()->where('was_answered', false)->count(),
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

        $data = $request->validate([
            'chatbot_bot_name_en'             => ['nullable', 'string', 'max:255'],
            'chatbot_bot_name_ar'             => ['nullable', 'string', 'max:255'],
            'chatbot_welcome_message_en'      => ['nullable', 'string'],
            'chatbot_welcome_message_ar'      => ['nullable', 'string'],
            'chatbot_fallback_message_en'     => ['nullable', 'string'],
            'chatbot_fallback_message_ar'     => ['nullable', 'string'],
            'chatbot_primary_language'        => ['nullable', 'in:ar,en'],
            'chatbot_suggested_questions_en'  => ['nullable', 'string'],
            'chatbot_suggested_questions_ar'  => ['nullable', 'string'],
            'chatbot_content_sources'         => ['array'],
            'chatbot_content_sources.*'       => ['nullable', 'string', 'max:50'],
            // AI Gateway
            'ai_default_provider'    => ['nullable', 'in:openai,gemini,deepseek,claude'],
            'ai_openai_api_key'      => ['nullable', 'string', 'max:255'],
            'ai_openai_model'        => ['nullable', 'string', 'max:60'],
            'ai_gemini_api_key'      => ['nullable', 'string', 'max:255'],
            'ai_gemini_model'        => ['nullable', 'string', 'max:60'],
            'ai_deepseek_api_key'    => ['nullable', 'string', 'max:255'],
            'ai_deepseek_model'      => ['nullable', 'string', 'max:60'],
            'ai_claude_api_key'      => ['nullable', 'string', 'max:255'],
            'ai_claude_model'        => ['nullable', 'string', 'max:60'],
            'ai_system_prompt_en'    => ['nullable', 'string'],
            'ai_system_prompt_ar'    => ['nullable', 'string'],
            'ai_max_tokens'          => ['nullable', 'integer', 'min:100', 'max:4000'],
            'ai_temperature'         => ['nullable', 'numeric', 'min:0', 'max:2'],
            // WhatsApp
            'whatsapp_cloud_api_token'        => ['nullable', 'string', 'max:500'],
            'whatsapp_phone_number_id'        => ['nullable', 'string', 'max:50'],
            'whatsapp_business_account_id'    => ['nullable', 'string', 'max:50'],
            'whatsapp_webhook_verify_token'   => ['nullable', 'string', 'max:255'],
            'whatsapp_handover_keyword'       => ['nullable', 'string', 'max:50'],
        ]);

        $setting->update([
            'chatbot_enabled'                => $request->boolean('chatbot_enabled'),
            'chatbot_bot_name_en'            => $data['chatbot_bot_name_en'] ?? null,
            'chatbot_bot_name_ar'            => $data['chatbot_bot_name_ar'] ?? null,
            'chatbot_welcome_message_en'     => $data['chatbot_welcome_message_en'] ?? null,
            'chatbot_welcome_message_ar'     => $data['chatbot_welcome_message_ar'] ?? null,
            'chatbot_fallback_message_en'    => $data['chatbot_fallback_message_en'] ?? null,
            'chatbot_fallback_message_ar'    => $data['chatbot_fallback_message_ar'] ?? null,
            'chatbot_primary_language'       => $data['chatbot_primary_language'] ?? 'ar',
            'chatbot_suggested_questions_en' => $this->normalizeLines($data['chatbot_suggested_questions_en'] ?? null),
            'chatbot_suggested_questions_ar' => $this->normalizeLines($data['chatbot_suggested_questions_ar'] ?? null),
            'chatbot_show_whatsapp_handoff'  => $request->boolean('chatbot_show_whatsapp_handoff', true),
            'chatbot_show_contact_handoff'   => $request->boolean('chatbot_show_contact_handoff', true),
            'chatbot_content_sources'        => collect($request->input('chatbot_content_sources', []))
                ->filter(fn ($item) => filled($item))
                ->values()
                ->all(),
        ]);

        // AI Gateway Modular Config
        \App\Models\AiBotConfig::getDefault()->update([
            'enabled'             => $request->boolean('ai_gateway_enabled'),
            'fallback_to_keyword' => $request->boolean('ai_fallback_to_keyword', true),
            'provider'            => $data['ai_default_provider'] ?? 'openai',
            'openai_api_key'      => $data['ai_openai_api_key'] ?? \App\Models\AiBotConfig::getDefault()->openai_api_key,
            'openai_model'        => $data['ai_openai_model'] ?? 'gpt-4o-mini',
            'gemini_api_key'      => $data['ai_gemini_api_key'] ?? \App\Models\AiBotConfig::getDefault()->gemini_api_key,
            'gemini_model'        => $data['ai_gemini_model'] ?? 'gemini-1.5-flash',
            'deepseek_api_key'    => $data['ai_deepseek_api_key'] ?? \App\Models\AiBotConfig::getDefault()->deepseek_api_key,
            'deepseek_model'      => $data['ai_deepseek_model'] ?? 'deepseek-chat',
            'claude_api_key'      => $data['ai_claude_api_key'] ?? \App\Models\AiBotConfig::getDefault()->claude_api_key,
            'claude_model'        => $data['ai_claude_model'] ?? 'claude-3-haiku-20240307',
            'system_prompt_en'    => $data['ai_system_prompt_en'] ?? null,
            'system_prompt_ar'    => $data['ai_system_prompt_ar'] ?? null,
            'max_tokens'          => (int) ($data['ai_max_tokens'] ?? 1000),
            'temperature'         => (float) ($data['ai_temperature'] ?? 0.7),
        ]);

        // WhatsApp Modular Config
        \App\Models\WhatsappConfig::get()->update([
            'enabled'                => $request->boolean('whatsapp_bot_enabled'),
            'human_handover_enabled' => $request->boolean('whatsapp_human_handover_enabled', true),
            'access_token'           => $data['whatsapp_cloud_api_token'] ?? \App\Models\WhatsappConfig::get()->access_token,
            'phone_number_id'        => $data['whatsapp_phone_number_id'] ?? null,
            'business_account_id'    => $data['whatsapp_business_account_id'] ?? null,
            'verify_token'           => $data['whatsapp_webhook_verify_token'] ?? \App\Models\WhatsappConfig::get()->verify_token,
            'handover_keyword'       => $data['whatsapp_handover_keyword'] ?? 'وكيل',
        ]);

        return back()->with('success', __('admin.chatbot_settings_updated'));
    }

    public function rebuildKnowledge()
    {
        $count = $this->knowledgeManager->rebuild();

        return back()->with('success', trans_choice('admin.chatbot_knowledge_rebuilt', $count, ['count' => $count]));
    }

    public function clearKnowledge()
    {
        ChatbotKnowledgeItem::query()->delete();

        return back()->with('success', 'Chatbot knowledge cleared successfully.');
    }

    protected function normalizeLines(?string $value): ?array
    {
        if (! filled($value)) {
            return null;
        }

        $items = collect(preg_split('/\r\n|\r|\n/', $value) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();

        return $items !== [] ? $items : null;
    }
}
