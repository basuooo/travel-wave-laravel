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
            'chatbot_bot_name_en' => ['nullable', 'string', 'max:255'],
            'chatbot_bot_name_ar' => ['nullable', 'string', 'max:255'],
            'chatbot_welcome_message_en' => ['nullable', 'string'],
            'chatbot_welcome_message_ar' => ['nullable', 'string'],
            'chatbot_fallback_message_en' => ['nullable', 'string'],
            'chatbot_fallback_message_ar' => ['nullable', 'string'],
            'chatbot_primary_language' => ['nullable', 'in:ar,en'],
            'chatbot_suggested_questions_en' => ['nullable', 'string'],
            'chatbot_suggested_questions_ar' => ['nullable', 'string'],
            'chatbot_content_sources' => ['array'],
            'chatbot_content_sources.*' => ['nullable', 'string', 'max:50'],
        ]);

        $setting->update([
            'chatbot_enabled' => $request->boolean('chatbot_enabled'),
            'chatbot_bot_name_en' => $data['chatbot_bot_name_en'] ?? null,
            'chatbot_bot_name_ar' => $data['chatbot_bot_name_ar'] ?? null,
            'chatbot_welcome_message_en' => $data['chatbot_welcome_message_en'] ?? null,
            'chatbot_welcome_message_ar' => $data['chatbot_welcome_message_ar'] ?? null,
            'chatbot_fallback_message_en' => $data['chatbot_fallback_message_en'] ?? null,
            'chatbot_fallback_message_ar' => $data['chatbot_fallback_message_ar'] ?? null,
            'chatbot_primary_language' => $data['chatbot_primary_language'] ?? 'ar',
            'chatbot_suggested_questions_en' => $this->normalizeLines($data['chatbot_suggested_questions_en'] ?? null),
            'chatbot_suggested_questions_ar' => $this->normalizeLines($data['chatbot_suggested_questions_ar'] ?? null),
            'chatbot_show_whatsapp_handoff' => $request->boolean('chatbot_show_whatsapp_handoff', true),
            'chatbot_show_contact_handoff' => $request->boolean('chatbot_show_contact_handoff', true),
            'chatbot_content_sources' => collect($request->input('chatbot_content_sources', []))
                ->filter(fn ($item) => filled($item))
                ->values()
                ->all(),
        ]);

        return back()->with('success', __('admin.chatbot_settings_updated'));
    }

    public function rebuildKnowledge()
    {
        $count = $this->knowledgeManager->rebuild();

        return back()->with('success', trans_choice('admin.chatbot_knowledge_rebuilt', $count, ['count' => $count]));
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
