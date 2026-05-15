@extends('layouts.admin')

@section('page_title', __('admin.chatbot_manager'))
@section('page_description', __('admin.chatbot_manager_desc'))

@php
    $selectedSources = old('chatbot_content_sources', $setting->chatbotContentSources());
    $suggestedEn = old('chatbot_suggested_questions_en', implode(PHP_EOL, $setting->chatbot_suggested_questions_en ?? []));
    $suggestedAr = old('chatbot_suggested_questions_ar', implode(PHP_EOL, $setting->chatbot_suggested_questions_ar ?? []));
    
    // Modular Configs
    $aiBotConfig = \App\Models\AiBotConfig::getDefault();
    $whatsappConfig = \App\Models\WhatsappConfig::get();
@endphp

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card admin-card p-4 h-100">
            <div class="text-muted small mb-2">{{ __('admin.chatbot_status') }}</div>
            <div class="fs-3 fw-semibold mb-1">{{ $setting->chatbot_enabled ? __('admin.active') : __('admin.inactive') }}</div>
            <div class="text-muted">{{ $setting->chatbotBotName() }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card admin-card p-4 h-100">
            <div class="text-muted small mb-2">{{ __('admin.chatbot_manual_knowledge') }}</div>
            <div class="fs-3 fw-semibold mb-1">{{ number_format($manualKnowledgeCount) }}</div>
            <div class="text-muted">{{ __('admin.chatbot_knowledge_items') }}: {{ number_format($knowledgeCount) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card admin-card p-4 h-100">
            <div class="text-muted small mb-2">{{ __('admin.chatbot_unanswered_count') }}</div>
            <div class="fs-3 fw-semibold mb-1">{{ number_format($unansweredCount) }}</div>
            <a href="#chatbot-logs" class="text-decoration-none">{{ __('admin.chatbot_open_logs_anchor') }}</a>
        </div>
    </div>
</div>

<form method="post" action="{{ route('admin.chatbot-settings.rebuild') }}" id="chatbot-rebuild-form" class="d-none">
    @csrf
</form>

<form method="post" action="{{ route('admin.chatbot-settings.update') }}">
    @csrf
    @method('PUT')

    <div class="card admin-card p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1">{{ __('admin.chatbot_overview') }}</h2>
                <p class="text-muted mb-0">{{ __('admin.chatbot_manager_desc') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.ai-bots.index') }}" class="btn btn-outline-info">🤖 إدارة البوتات (Multi-Bot)</a>
                <a href="{{ route('admin.chatbot-knowledge.index') }}" class="btn btn-outline-primary">{{ __('admin.chatbot_manage_knowledge') }}</a>
                <button class="btn btn-primary px-4">{{ __('admin.update') }}</button>
                <button type="submit" form="chatbot-rebuild-form" class="btn btn-outline-secondary">{{ __('admin.chatbot_rebuild_knowledge') }}</button>
                <button type="button" class="btn btn-outline-danger" onclick="if(confirm('هل أنت متأكد من مسح كل المعرفة؟')) document.getElementById('chatbot-clear-form').submit();">مسح كل المعرفة</button>
            </div>
        </div>

        <form method="post" action="{{ route('admin.chatbot-settings.clear') }}" id="chatbot-clear-form" class="d-none">
            @csrf
        </form>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-check mt-1">
                    <input type="hidden" name="chatbot_enabled" value="0">
                    <input type="checkbox" class="form-check-input" id="chatbot_enabled" name="chatbot_enabled" value="1" @checked(old('chatbot_enabled', $setting->chatbot_enabled))>
                    <label class="form-check-label" for="chatbot_enabled">{{ __('admin.chatbot_enabled_label') }}</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.chatbot_primary_language') }}</label>
                <select name="chatbot_primary_language" class="form-select">
                    <option value="ar" @selected(old('chatbot_primary_language', $setting->chatbot_primary_language ?: 'ar') === 'ar')>{{ __('ui.language_arabic') }}</option>
                    <option value="en" @selected(old('chatbot_primary_language', $setting->chatbot_primary_language ?: 'ar') === 'en')>{{ __('ui.language_english') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.chatbot_bot_name') }} EN</label>
                <input type="text" class="form-control" name="chatbot_bot_name_en" value="{{ old('chatbot_bot_name_en', $setting->chatbot_bot_name_en) }}" placeholder="Travel Wave Assistant">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.chatbot_bot_name') }} AR</label>
                <input type="text" class="form-control text-end" dir="rtl" name="chatbot_bot_name_ar" value="{{ old('chatbot_bot_name_ar', $setting->chatbot_bot_name_ar) }}" placeholder="مساعد ترافل ويف">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.chatbot_welcome_message') }} EN</label>
                <textarea class="form-control" rows="3" name="chatbot_welcome_message_en" placeholder="{{ __('ui.chatbot_default_welcome') }}">{{ old('chatbot_welcome_message_en', $setting->chatbot_welcome_message_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.chatbot_welcome_message') }} AR</label>
                <textarea class="form-control text-end" dir="rtl" rows="3" name="chatbot_welcome_message_ar" placeholder="{{ __('ui.chatbot_default_welcome') }}">{{ old('chatbot_welcome_message_ar', $setting->chatbot_welcome_message_ar) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.chatbot_fallback_message') }} EN</label>
                <textarea class="form-control" rows="3" name="chatbot_fallback_message_en" placeholder="{{ __('ui.chatbot_default_fallback') }}">{{ old('chatbot_fallback_message_en', $setting->chatbot_fallback_message_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.chatbot_fallback_message') }} AR</label>
                <textarea class="form-control text-end" dir="rtl" rows="3" name="chatbot_fallback_message_ar" placeholder="{{ __('ui.chatbot_default_fallback') }}">{{ old('chatbot_fallback_message_ar', $setting->chatbot_fallback_message_ar) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.chatbot_suggested_questions') }}</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">EN</label>
                <textarea class="form-control" rows="5" name="chatbot_suggested_questions_en" placeholder="One question per line">{{ $suggestedEn }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">AR</label>
                <textarea class="form-control text-end" dir="rtl" rows="5" name="chatbot_suggested_questions_ar" placeholder="سؤال واحد في كل سطر">{{ $suggestedAr }}</textarea>
            </div>
            <div class="col-12">
                <div class="form-text">{{ __('admin.chatbot_suggested_questions_help') }}</div>
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.chatbot_content_sources_label') }}</h2>
        <div class="row g-3">
            @foreach($sourceOptions as $sourceKey => $label)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="chatbot-source-{{ $sourceKey }}" name="chatbot_content_sources[]" value="{{ $sourceKey }}" @checked(in_array($sourceKey, $selectedSources, true))>
                        <label class="form-check-label" for="chatbot-source-{{ $sourceKey }}">
                            {{ __('admin.chatbot_source_' . $sourceKey) !== 'admin.chatbot_source_' . $sourceKey ? __('admin.chatbot_source_' . $sourceKey) : $label }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.chatbot_handoff') }}</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-check">
                    <input type="hidden" name="chatbot_show_whatsapp_handoff" value="0">
                    <input class="form-check-input" type="checkbox" id="chatbot_show_whatsapp_handoff" name="chatbot_show_whatsapp_handoff" value="1" @checked(old('chatbot_show_whatsapp_handoff', $setting->chatbot_show_whatsapp_handoff ?? true))>
                    <label class="form-check-label" for="chatbot_show_whatsapp_handoff">{{ __('admin.chatbot_show_whatsapp_handoff') }}</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input type="hidden" name="chatbot_show_contact_handoff" value="0">
                    <input class="form-check-input" type="checkbox" id="chatbot_show_contact_handoff" name="chatbot_show_contact_handoff" value="1" @checked(old('chatbot_show_contact_handoff', $setting->chatbot_show_contact_handoff ?? true))>
                    <label class="form-check-label" for="chatbot_show_contact_handoff">{{ __('admin.chatbot_show_contact_handoff') }}</label>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== AI GATEWAY SECTION ===================== --}}
    <div class="card admin-card p-4 mb-4" style="border-left: 4px solid #6366f1;">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div style="font-size:1.6rem;">🤖</div>
            <div>
                <h2 class="h5 mb-0">بوابة الذكاء الاصطناعي — AI Gateway</h2>
                <p class="text-muted mb-0 small">اربط البوت بـ ChatGPT أو Gemini أو DeepSeek أو Claude للردود الذكية الحقيقية</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-check mt-2">
                    <input type="hidden" name="ai_gateway_enabled" value="0">
                    <input class="form-check-input" type="checkbox" id="ai_gateway_enabled" name="ai_gateway_enabled" value="1"
                        @checked(old('ai_gateway_enabled', $aiBotConfig->enabled))>
                    <label class="form-check-label fw-semibold" for="ai_gateway_enabled">تفعيل الذكاء الاصطناعي</label>
                </div>
                <div class="form-text">عند التفعيل، سيستخدم الـ AI لتوليد ردود ذكية بدل الـ keyword matching</div>
            </div>
            <div class="col-md-4">
                <div class="form-check mt-2">
                    <input type="hidden" name="ai_fallback_to_keyword" value="0">
                    <input class="form-check-input" type="checkbox" id="ai_fallback_to_keyword" name="ai_fallback_to_keyword" value="1"
                        @checked(old('ai_fallback_to_keyword', $aiBotConfig->fallback_to_keyword ?? true))>
                    <label class="form-check-label" for="ai_fallback_to_keyword">Fallback للـ Keyword Matching لو فشل الـ AI</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">الـ Provider الافتراضي</label>
                <select name="ai_default_provider" class="form-select">
                    <option value="openai" @selected(old('ai_default_provider', $aiBotConfig->provider ?? 'openai') === 'openai')>🟢 OpenAI (ChatGPT)</option>
                    <option value="gemini" @selected(old('ai_default_provider', $aiBotConfig->provider) === 'gemini')>🔵 Google Gemini</option>
                    <option value="deepseek" @selected(old('ai_default_provider', $aiBotConfig->provider) === 'deepseek')>🟣 DeepSeek (الأرخص)</option>
                    <option value="claude" @selected(old('ai_default_provider', $aiBotConfig->provider) === 'claude')>🟠 Claude (Anthropic)</option>
                </select>
            </div>

            {{-- OpenAI --}}
            <div class="col-12"><hr class="my-1"><p class="fw-semibold mb-2">🟢 OpenAI (ChatGPT)</p></div>
            <div class="col-md-8">
                <label class="form-label">API Key</label>
                <input type="password" class="form-control" name="ai_openai_api_key" value="{{ old('ai_openai_api_key') }}"
                    placeholder="{{ filled($aiBotConfig->openai_api_key) ? '••••• (محفوظ)' : 'sk-...' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Model</label>
                <select name="ai_openai_model" class="form-select">
                    <option value="gpt-4o-mini" @selected(old('ai_openai_model', $aiBotConfig->openai_model ?? 'gpt-4o-mini') === 'gpt-4o-mini')>gpt-4o-mini (الأرخص)</option>
                    <option value="gpt-4o" @selected(old('ai_openai_model', $aiBotConfig->openai_model) === 'gpt-4o')>gpt-4o</option>
                    <option value="gpt-3.5-turbo" @selected(old('ai_openai_model', $aiBotConfig->openai_model) === 'gpt-3.5-turbo')>gpt-3.5-turbo</option>
                </select>
            </div>

            {{-- Gemini --}}
            <div class="col-12"><hr class="my-1"><p class="fw-semibold mb-2">🔵 Google Gemini</p></div>
            <div class="col-md-8">
                <label class="form-label">API Key</label>
                <input type="password" class="form-control" name="ai_gemini_api_key" value="{{ old('ai_gemini_api_key') }}"
                    placeholder="{{ filled($aiBotConfig->gemini_api_key) ? '••••• (محفوظ)' : 'AIza...' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Model</label>
                <select name="ai_gemini_model" class="form-select">
                    <option value="gemini-1.5-flash" @selected(old('ai_gemini_model', $aiBotConfig->gemini_model ?? 'gemini-1.5-flash') === 'gemini-1.5-flash')>gemini-1.5-flash (مجاني)</option>
                    <option value="gemini-1.5-pro" @selected(old('ai_gemini_model', $aiBotConfig->gemini_model) === 'gemini-1.5-pro')>gemini-1.5-pro</option>
                </select>
            </div>

            {{-- DeepSeek --}}
            <div class="col-12"><hr class="my-1"><p class="fw-semibold mb-2">🟣 DeepSeek</p></div>
            <div class="col-md-8">
                <label class="form-label">API Key</label>
                <input type="password" class="form-control" name="ai_deepseek_api_key" value="{{ old('ai_deepseek_api_key') }}"
                    placeholder="{{ filled($aiBotConfig->deepseek_api_key) ? '••••• (محفوظ)' : 'sk-...' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Model</label>
                <select name="ai_deepseek_model" class="form-select">
                    <option value="deepseek-chat" @selected(old('ai_deepseek_model', $aiBotConfig->deepseek_model ?? 'deepseek-chat') === 'deepseek-chat')>deepseek-chat</option>
                    <option value="deepseek-reasoner" @selected(old('ai_deepseek_model', $aiBotConfig->deepseek_model) === 'deepseek-reasoner')>deepseek-reasoner</option>
                </select>
            </div>

            {{-- Claude --}}
            <div class="col-12"><hr class="my-1"><p class="fw-semibold mb-2">🟠 Claude (Anthropic)</p></div>
            <div class="col-md-8">
                <label class="form-label">API Key</label>
                <input type="password" class="form-control" name="ai_claude_api_key" value="{{ old('ai_claude_api_key') }}"
                    placeholder="{{ filled($aiBotConfig->claude_api_key) ? '••••• (محفوظ)' : 'sk-ant-...' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Model</label>
                <select name="ai_claude_model" class="form-select">
                    <option value="claude-3-haiku-20240307" @selected(old('ai_claude_model', $aiBotConfig->claude_model ?? 'claude-3-haiku-20240307') === 'claude-3-haiku-20240307')>claude-3-haiku (الأسرع والأرخص)</option>
                    <option value="claude-3-5-sonnet-20241022" @selected(old('ai_claude_model', $aiBotConfig->claude_model) === 'claude-3-5-sonnet-20241022')>claude-3.5-sonnet</option>
                </select>
            </div>

            {{-- System Prompt --}}
            <div class="col-12"><hr class="my-1"><p class="fw-semibold mb-2">📝 System Prompt (تعليمات الـ AI)</p></div>
            <div class="col-md-6">
                <label class="form-label">بالعربية</label>
                <textarea class="form-control text-end" dir="rtl" rows="4" name="ai_system_prompt_ar"
                    placeholder="أنت مساعد ذكي متخصص في خدمات الفيزا...">{{ old('ai_system_prompt_ar', $aiBotConfig->system_prompt_ar) }}</textarea>
                <div class="form-text">اترك فارغًا لاستخدام الـ prompt الافتراضي</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">بالإنجليزية</label>
                <textarea class="form-control" rows="4" name="ai_system_prompt_en"
                    placeholder="You are a smart assistant specialized in visa services...">{{ old('ai_system_prompt_en', $aiBotConfig->system_prompt_en) }}</textarea>
            </div>

            {{-- Tokens / Temperature --}}
            <div class="col-md-6">
                <label class="form-label">Max Tokens <span class="text-muted">(الحد الأقصى للرد)</span></label>
                <input type="number" class="form-control" name="ai_max_tokens" min="100" max="4000"
                    value="{{ old('ai_max_tokens', $aiBotConfig->max_tokens ?? 1000) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Temperature <span class="text-muted">(0 = محافظ، 1 = إبداعي)</span></label>
                <input type="number" class="form-control" name="ai_temperature" min="0" max="2" step="0.1"
                    value="{{ old('ai_temperature', $aiBotConfig->temperature ?? 0.7) }}">
            </div>
        </div>
    </div>

    {{-- ===================== WHATSAPP CLOUD API SECTION ===================== --}}
    <div class="card admin-card p-4 mb-4" style="border-left: 4px solid #25d366;">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div style="font-size:1.6rem;">💬</div>
            <div>
                <h2 class="h5 mb-0">WhatsApp Cloud API</h2>
                <p class="text-muted mb-0 small">اربط البوت بـ WhatsApp Business ليرد تلقائيًا على رسائل العملاء</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-check mt-2">
                    <input type="hidden" name="whatsapp_bot_enabled" value="0">
                    <input class="form-check-input" type="checkbox" id="whatsapp_bot_enabled" name="whatsapp_bot_enabled" value="1"
                        @checked(old('whatsapp_bot_enabled', $whatsappConfig->enabled))>
                    <label class="form-check-label fw-semibold" for="whatsapp_bot_enabled">تفعيل بوت WhatsApp</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check mt-2">
                    <input type="hidden" name="whatsapp_human_handover_enabled" value="0">
                    <input class="form-check-input" type="checkbox" id="whatsapp_human_handover_enabled" name="whatsapp_human_handover_enabled" value="1"
                        @checked(old('whatsapp_human_handover_enabled', $whatsappConfig->human_handover_enabled ?? true))>
                    <label class="form-check-label" for="whatsapp_human_handover_enabled">تفعيل Human Handover</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">كلمة التحويل للموظف</label>
                <input type="text" class="form-control text-end" dir="rtl" name="whatsapp_handover_keyword"
                    value="{{ old('whatsapp_handover_keyword', $whatsappConfig->handover_keyword ?? 'وكيل') }}"
                    placeholder="وكيل">
                <div class="form-text">لما العميل يكتب هذه الكلمة، يتحول للموظف البشري</div>
            </div>

            <div class="col-12">
                <div class="alert alert-info py-2 mb-0">
                    <strong>Webhook URL لـ Meta:</strong>
                    <code>{{ url('/api/webhooks/whatsapp') }}</code>
                    <span class="text-muted ms-2">(أضفه في Meta Developers → WhatsApp → Configuration)</span>
                </div>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Access Token <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="whatsapp_cloud_api_token"
                    value="{{ old('whatsapp_cloud_api_token') }}"
                    placeholder="{{ filled($whatsappConfig->access_token) ? '••••• (محفوظ)' : 'EAAxxxxxxxx...' }}">
                <div class="form-text">من Meta Developers → WhatsApp → API Setup</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Phone Number ID <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="whatsapp_phone_number_id"
                    value="{{ old('whatsapp_phone_number_id', $whatsappConfig->phone_number_id) }}"
                    placeholder="123456789012345">
            </div>
            <div class="col-md-6">
                <label class="form-label">WhatsApp Business Account ID</label>
                <input type="text" class="form-control" name="whatsapp_business_account_id"
                    value="{{ old('whatsapp_business_account_id', $whatsappConfig->business_account_id) }}"
                    placeholder="123456789012345">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Webhook Verify Token <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="whatsapp_webhook_verify_token"
                    value="{{ old('whatsapp_webhook_verify_token', $whatsappConfig->verify_token) }}"
                    placeholder="أي كلمة سر اختارها أنت">
                <div class="form-text">اكتب نفس الكلمة في Meta عند إعداد الـ Webhook</div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <a href="{{ route('admin.whatsapp.conversations.index') }}" class="btn btn-outline-success w-100">
                    💬 عرض محادثات WhatsApp
                </a>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-5">
        <button class="btn btn-primary px-4">{{ __('admin.update') }}</button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
    </div>
</form>

<div class="card admin-card p-4" id="chatbot-logs">
    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.chatbot_recent_interactions') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.chatbot_logs') }}</p>
        </div>
        <span class="badge bg-light text-dark">{{ number_format($latestInteractions->count()) }}</span>
    </div>

    @if($latestInteractions->isEmpty())
        <div class="text-muted">{{ __('admin.chatbot_no_interactions') }}</div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>{{ __('admin.chatbot_question') }}</th>
                        <th>{{ __('admin.chatbot_answer') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th>{{ __('admin.chatbot_last_activity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestInteractions as $interaction)
                        <tr>
                            <td class="fw-semibold">{{ \Illuminate\Support\Str::limit($interaction->question, 120) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($interaction->answer, 160) }}</td>
                            <td>
                                <span class="badge {{ $interaction->was_answered ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">
                                    {{ $interaction->was_answered ? __('admin.chatbot_answered') : __('admin.chatbot_unanswered') }}
                                </span>
                            </td>
                            <td>{{ optional($interaction->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
