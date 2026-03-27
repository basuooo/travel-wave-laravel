<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.chatbot_knowledge_title_en') }}</label>
        <input type="text" class="form-control" name="title_en" value="{{ old('title_en', $entry->title_en) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.chatbot_knowledge_title_ar') }}</label>
        <input type="text" class="form-control text-end" dir="rtl" name="title_ar" value="{{ old('title_ar', $entry->title_ar) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.chatbot_knowledge_question_en') }}</label>
        <textarea class="form-control" rows="3" name="question_en">{{ old('question_en', $entry->question_en) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.chatbot_knowledge_question_ar') }}</label>
        <textarea class="form-control text-end" dir="rtl" rows="3" name="question_ar">{{ old('question_ar', $entry->question_ar) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.chatbot_knowledge_answer_en') }}</label>
        <textarea class="form-control" rows="7" name="answer_en">{{ old('answer_en', $entry->answer_en) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.chatbot_knowledge_answer_ar') }}</label>
        <textarea class="form-control text-end" dir="rtl" rows="7" name="answer_ar">{{ old('answer_ar', $entry->answer_ar) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.chatbot_knowledge_keywords_en') }}</label>
        <textarea class="form-control" rows="3" name="keywords_en" placeholder="visa, apply, documents, fees">{{ old('keywords_en', $entry->keywords_en) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.chatbot_knowledge_keywords_ar') }}</label>
        <textarea class="form-control text-end" dir="rtl" rows="3" name="keywords_ar" placeholder="تأشيرة، تقديم، أوراق، رسوم">{{ old('keywords_ar', $entry->keywords_ar) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('admin.chatbot_knowledge_category_en') }}</label>
        <input type="text" class="form-control" name="category_en" value="{{ old('category_en', $entry->category_en) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('admin.chatbot_knowledge_category_ar') }}</label>
        <input type="text" class="form-control text-end" dir="rtl" name="category_ar" value="{{ old('category_ar', $entry->category_ar) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">{{ __('admin.priority') }}</label>
        <input type="number" min="0" class="form-control" name="priority" value="{{ old('priority', $entry->priority ?? 0) }}">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <div class="form-check pb-2">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $entry->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
        </div>
    </div>
</div>
