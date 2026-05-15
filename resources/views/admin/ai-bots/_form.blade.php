<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-bold">اسم البوت (مثلاً: WhatsApp Sales Bot)</label>
        <input type="text" class="form-control" name="name" value="{{ old('name', $bot->name) }}" required>
    </div>
    <div class="col-md-4">
        <div class="form-check mt-4">
            <input type="hidden" name="enabled" value="0">
            <input class="form-check-input" type="checkbox" id="enabled" name="enabled" value="1" @checked(old('enabled', $bot->enabled))>
            <label class="form-check-label fw-bold" for="enabled">تفعيل البوت</label>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">المزود (Provider)</label>
        <select name="provider" class="form-select" onchange="toggleProviderSettings(this.value)">
            <option value="openai" @selected(old('provider', $bot->provider) === 'openai')>OpenAI (ChatGPT)</option>
            <option value="gemini" @selected(old('provider', $bot->provider) === 'gemini')>Google Gemini</option>
            <option value="deepseek" @selected(old('provider', $bot->provider) === 'deepseek')>DeepSeek</option>
            <option value="claude" @selected(old('provider', $bot->provider) === 'claude')>Claude</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Max Tokens</label>
        <input type="number" class="form-control" name="max_tokens" value="{{ old('max_tokens', $bot->max_tokens) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Temperature</label>
        <input type="number" step="0.1" class="form-control" name="temperature" value="{{ old('temperature', $bot->temperature) }}">
    </div>

    <div class="col-12"><hr></div>

    {{-- API Keys Section --}}
    <div class="col-md-6 provider-setting openai-setting">
        <label class="form-label">OpenAI API Key</label>
        <input type="password" class="form-control" name="openai_api_key" placeholder="{{ $bot->openai_api_key ? 'محفوظ (اتركه فارغاً للحفاظ عليه)' : '' }}">
    </div>
    <div class="col-md-6 provider-setting openai-setting">
        <label class="form-label">OpenAI Model</label>
        <input type="text" class="form-control" name="openai_model" value="{{ old('openai_model', $bot->openai_model) }}">
    </div>

    <div class="col-md-6 provider-setting gemini-setting">
        <label class="form-label">Gemini API Key</label>
        <input type="password" class="form-control" name="gemini_api_key" placeholder="{{ $bot->gemini_api_key ? 'محفوظ' : '' }}">
    </div>
    <div class="col-md-6 provider-setting gemini-setting">
        <label class="form-label">Gemini Model</label>
        <input type="text" class="form-control" name="gemini_model" value="{{ old('gemini_model', $bot->gemini_model) }}">
    </div>

    <div class="col-md-6 provider-setting deepseek-setting">
        <label class="form-label">DeepSeek API Key</label>
        <input type="password" class="form-control" name="deepseek_api_key" placeholder="{{ $bot->deepseek_api_key ? 'محفوظ' : '' }}">
    </div>
    <div class="col-md-6 provider-setting deepseek-setting">
        <label class="form-label">DeepSeek Model</label>
        <input type="text" class="form-control" name="deepseek_model" value="{{ old('deepseek_model', $bot->deepseek_model) }}">
    </div>

    <div class="col-md-6 provider-setting claude-setting">
        <label class="form-label">Claude API Key</label>
        <input type="password" class="form-control" name="claude_api_key" placeholder="{{ $bot->claude_api_key ? 'محفوظ' : '' }}">
    </div>
    <div class="col-md-6 provider-setting claude-setting">
        <label class="form-label">Claude Model</label>
        <input type="text" class="form-control" name="claude_model" value="{{ old('claude_model', $bot->claude_model) }}">
    </div>

    <div class="col-12"><hr></div>

    <div class="col-md-6">
        <label class="form-label fw-bold">التعليمات البرمجية (System Prompt) - AR</label>
        <textarea class="form-control text-end" dir="rtl" rows="6" name="system_prompt_ar">{{ old('system_prompt_ar', $bot->system_prompt_ar) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">System Prompt - EN</label>
        <textarea class="form-control" rows="6" name="system_prompt_en">{{ old('system_prompt_en', $bot->system_prompt_en) }}</textarea>
    </div>

    <div class="col-12">
        <div class="form-check">
            <input type="hidden" name="fallback_to_keyword" value="0">
            <input class="form-check-input" type="checkbox" id="fallback_to_keyword" name="fallback_to_keyword" value="1" @checked(old('fallback_to_keyword', $bot->fallback_to_keyword))>
            <label class="form-check-label" for="fallback_to_keyword">تفعيل الـ Fallback لقاعدة المعرفة لو فشل الـ AI</label>
        </div>
    </div>
</div>

<script>
function toggleProviderSettings(provider) {
    document.querySelectorAll('.provider-setting').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.' + provider + '-setting').forEach(el => el.style.display = 'block');
}
// Run on load
document.addEventListener('DOMContentLoaded', () => toggleProviderSettings('{{ old('provider', $bot->provider) }}'));
</script>
