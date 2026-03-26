<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">{{ __('admin.kb_article_title') }}</label>
        <input type="text" class="form-control" name="title" value="{{ old('title', $article->title) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('admin.slug_key') }}</label>
        <input type="text" class="form-control" name="slug" value="{{ old('slug', $article->slug) }}" placeholder="internal-procedure-france">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('admin.kb_category') }}</label>
        <select class="form-select" name="knowledge_base_category_id">
            <option value="">{{ __('admin.all') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('knowledge_base_category_id', $article->knowledge_base_category_id) === (int) $category->id)>{{ $category->localizedName() }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('admin.status') }}</label>
        <select class="form-select" name="status" required>
            @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $article->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('admin.kb_visibility_scope') }}</label>
        <select class="form-select" name="visibility_scope" required>
            @foreach($visibilityOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('visibility_scope', $article->visibility_scope) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">{{ __('admin.sort_order') }}</label>
        <input type="number" min="0" class="form-control" name="sort_order" value="{{ old('sort_order', $article->sort_order ?? 0) }}">
    </div>
    <div class="col-md-12">
        <label class="form-label">{{ __('admin.summary') }}</label>
        <textarea class="form-control" name="summary" rows="3">{{ old('summary', $article->summary) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('admin.kb_content') }}</label>
        <textarea class="form-control" name="content" rows="16" required>{{ old('content', $article->content) }}</textarea>
        <div class="form-text">{{ __('admin.kb_content_help') }}</div>
    </div>
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $article->is_featured))>
            <label class="form-check-label" for="is_featured">{{ __('admin.kb_featured_article') }}</label>
        </div>
    </div>
</div>
