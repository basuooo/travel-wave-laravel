@extends('layouts.admin')

@section('page_title', $article->title)
@section('page_description', $article->summary ?: __('admin.knowledge_base_desc'))

@section('content')
<div class="row g-4">
    <div class="col-xl-8">
        <div class="card admin-card p-4">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge text-bg-{{ $article->statusBadgeClass() }}">{{ $article->localizedStatus() }}</span>
                <span class="badge text-bg-light">{{ $article->localizedVisibilityScope() }}</span>
                @if($article->is_featured)
                    <span class="badge text-bg-warning">{{ __('admin.kb_featured_article') }}</span>
                @endif
            </div>
            <div class="mb-3">
                <div class="text-muted small">{{ __('admin.kb_category') }}</div>
                <div class="fw-semibold">{{ $article->category?->localizedName() ?: '—' }}</div>
            </div>
            @if($article->summary)
                <div class="border rounded-3 p-3 mb-4 bg-light-subtle">
                    {{ $article->summary }}
                </div>
            @endif
            <div class="knowledge-base-article-content" style="white-space: pre-line;">{{ $article->content }}</div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card admin-card p-4 mb-4">
            <h3 class="h6 mb-3">{{ __('admin.details') }}</h3>
            <dl class="row mb-0">
                <dt class="col-sm-5">{{ __('admin.created_by') }}</dt>
                <dd class="col-sm-7">{{ $article->creator?->name ?: '—' }}</dd>
                <dt class="col-sm-5">{{ __('admin.updated_by') }}</dt>
                <dd class="col-sm-7">{{ $article->updater?->name ?: '—' }}</dd>
                <dt class="col-sm-5">{{ __('admin.created_date') }}</dt>
                <dd class="col-sm-7">{{ optional($article->created_at)->format('Y-m-d H:i') }}</dd>
                <dt class="col-sm-5">{{ __('admin.updated_at') }}</dt>
                <dd class="col-sm-7">{{ optional($article->updated_at)->format('Y-m-d H:i') }}</dd>
                <dt class="col-sm-5">{{ __('admin.slug_key') }}</dt>
                <dd class="col-sm-7 text-break">{{ $article->slug }}</dd>
            </dl>
            @if($canManageKnowledgeBase)
                <div class="mt-3">
                    <a href="{{ route('admin.knowledge-base.edit', $article) }}" class="btn btn-outline-primary">{{ __('admin.kb_edit_article') }}</a>
                </div>
            @endif
        </div>
        <div class="card admin-card p-4">
            <h3 class="h6 mb-3">{{ __('admin.kb_related_articles') }}</h3>
            @forelse($relatedArticles as $related)
                <div class="mb-3">
                    <a href="{{ route('admin.knowledge-base.show', $related) }}" class="fw-semibold text-decoration-none d-block">{{ $related->title }}</a>
                    <div class="small text-muted">{{ $related->category?->localizedName() ?: '—' }}</div>
                </div>
            @empty
                <div class="text-muted">{{ __('admin.no_data_available') }}</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
