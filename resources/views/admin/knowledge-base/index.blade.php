@extends('layouts.admin')

@section('page_title', __('admin.knowledge_base'))
@section('page_description', __('admin.knowledge_base_desc'))

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.kb_published_articles') }}</div><div class="fs-4 fw-semibold">{{ $summary['total_published'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.kb_featured_articles') }}</div><div class="fs-4 fw-semibold">{{ $summary['featured'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.kb_updated_this_week') }}</div><div class="fs-4 fw-semibold">{{ $summary['updated_this_week'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.kb_categories') }}</div><div class="fs-4 fw-semibold">{{ $summary['categories'] }}</div></div></div>
</div>

<div class="card admin-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.knowledge_base') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.knowledge_base_index_hint') }}</p>
        </div>
        @if($canManageKnowledgeBase)
            <div class="d-flex gap-2">
                <a href="{{ route('admin.knowledge-base.categories.index') }}" class="btn btn-outline-secondary">{{ __('admin.kb_categories') }}</a>
                <a href="{{ route('admin.knowledge-base.create') }}" class="btn btn-primary">{{ __('admin.kb_add_article') }}</a>
            </div>
        @endif
    </div>

    <form method="GET" action="{{ route('admin.knowledge-base.index') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.search') }}</label>
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.kb_search_placeholder') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.kb_category') }}</label>
            <select class="form-select" name="knowledge_base_category_id">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) request('knowledge_base_category_id') === (int) $category->id)>{{ $category->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        @if($canManageKnowledgeBase)
            <div class="col-md-2">
                <label class="form-label">{{ __('admin.status') }}</label>
                <select class="form-select" name="status">
                    <option value="">{{ __('admin.all') }}</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('admin.kb_visibility_scope') }}</label>
                <select class="form-select" name="visibility_scope">
                    <option value="">{{ __('admin.all') }}</option>
                    @foreach($visibilityOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('visibility_scope') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.created_by') }}</label>
            <select class="form-select" name="created_by">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}" @selected((int) request('created_by') === (int) $author->id)>{{ $author->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select class="form-select" name="is_featured">
                <option value="">{{ __('admin.all') }}</option>
                <option value="1" @selected(request('is_featured') === '1')>{{ __('admin.kb_featured_only') }}</option>
                <option value="0" @selected(request('is_featured') === '0')>{{ __('admin.kb_non_featured') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.sort_order') }}</label>
            <select class="form-select" name="sort">
                <option value="latest" @selected(request('sort', 'latest') === 'latest')>{{ __('admin.kb_sort_latest') }}</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>{{ __('admin.kb_sort_oldest') }}</option>
                <option value="updated_oldest" @selected(request('sort') === 'updated_oldest')>{{ __('admin.kb_sort_updated_oldest') }}</option>
                <option value="title" @selected(request('sort') === 'title')>{{ __('admin.kb_sort_title') }}</option>
            </select>
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">{{ __('admin.search') }}</button>
            <a href="{{ route('admin.knowledge-base.index') }}" class="btn btn-outline-secondary">{{ __('admin.reset') }}</a>
        </div>
    </form>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card admin-card p-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>{{ __('admin.kb_article') }}</th>
                        <th>{{ __('admin.kb_category') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th>{{ __('admin.kb_visibility_scope') }}</th>
                        <th>{{ __('admin.updated_at') }}</th>
                        <th>{{ __('admin.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->title }}</div>
                                @if($item->summary)
                                    <div class="small text-muted">{{ \Illuminate\Support\Str::limit($item->summary, 120) }}</div>
                                @endif
                            </td>
                            <td>{{ $item->category?->localizedName() ?: '—' }}</td>
                            <td><span class="badge text-bg-{{ $item->statusBadgeClass() }}">{{ $item->localizedStatus() }}</span></td>
                            <td>{{ $item->localizedVisibilityScope() }}</td>
                            <td>{{ optional($item->updated_at)->format('Y-m-d') }}</td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('admin.knowledge-base.show', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                                @if($canManageKnowledgeBase)
                                    <a href="{{ route('admin.knowledge-base.edit', $item) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.edit') }}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">{{ __('admin.kb_empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card admin-card p-4 mb-4">
            <h3 class="h6 mb-3">{{ __('admin.kb_featured_articles') }}</h3>
            @forelse($featuredArticles as $article)
                <div class="mb-3">
                    <a href="{{ route('admin.knowledge-base.show', $article) }}" class="fw-semibold text-decoration-none d-block">{{ $article->title }}</a>
                    <div class="small text-muted">{{ $article->category?->localizedName() ?: '—' }}</div>
                </div>
            @empty
                <div class="text-muted">{{ __('admin.kb_no_featured_articles') }}</div>
            @endforelse
        </div>
        <div class="card admin-card p-4">
            <h3 class="h6 mb-3">{{ __('admin.kb_latest_updates') }}</h3>
            @forelse($latestArticles as $article)
                <div class="mb-3">
                    <a href="{{ route('admin.knowledge-base.show', $article) }}" class="fw-semibold text-decoration-none d-block">{{ $article->title }}</a>
                    <div class="small text-muted">{{ optional($article->updated_at)->diffForHumans() }}</div>
                </div>
            @empty
                <div class="text-muted">{{ __('admin.kb_empty') }}</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
