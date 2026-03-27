@extends('layouts.admin')

@section('page_title', __('admin.chatbot_manager'))
@section('page_description', __('admin.chatbot_manager_desc'))

@php
    $selectedSources = old('chatbot_content_sources', $setting->chatbotContentSources());
    $suggestedEn = old('chatbot_suggested_questions_en', implode(PHP_EOL, $setting->chatbot_suggested_questions_en ?? []));
    $suggestedAr = old('chatbot_suggested_questions_ar', implode(PHP_EOL, $setting->chatbot_suggested_questions_ar ?? []));
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
                <a href="{{ route('admin.chatbot-knowledge.index') }}" class="btn btn-outline-primary">{{ __('admin.chatbot_manage_knowledge') }}</a>
                <button class="btn btn-primary px-4">{{ __('admin.update') }}</button>
                <button type="submit" form="chatbot-rebuild-form" class="btn btn-outline-secondary">{{ __('admin.chatbot_rebuild_knowledge') }}</button>
            </div>
        </div>

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
