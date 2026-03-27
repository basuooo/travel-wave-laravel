@extends('layouts.admin')

@section('page_title', __('admin.chatbot_knowledge'))
@section('page_description', __('admin.chatbot_knowledge_desc'))

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card admin-card p-4 h-100">
            <div class="text-muted small mb-2">{{ __('admin.total') }}</div>
            <div class="fs-3 fw-semibold">{{ number_format($summary['total']) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card admin-card p-4 h-100">
            <div class="text-muted small mb-2">{{ __('admin.active') }}</div>
            <div class="fs-3 fw-semibold">{{ number_format($summary['active']) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card admin-card p-4 h-100">
            <div class="text-muted small mb-2">{{ __('admin.inactive') }}</div>
            <div class="fs-3 fw-semibold">{{ number_format($summary['inactive']) }}</div>
        </div>
    </div>
</div>

<div class="card admin-card p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.chatbot_knowledge') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.chatbot_knowledge_desc') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.chatbot-settings.edit') }}" class="btn btn-outline-secondary">{{ __('admin.chatbot_manager') }}</a>
            <a href="{{ route('admin.chatbot-knowledge.create') }}" class="btn btn-primary">{{ __('admin.add') }}</a>
        </div>
    </div>

    <form method="get" class="row g-3 mb-3">
        <div class="col-md-5">
            <label class="form-label">{{ __('admin.search') }}</label>
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.chatbot_knowledge_search_placeholder') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select class="form-select" name="status">
                <option value="">{{ __('admin.all') }}</option>
                <option value="active" @selected(request('status') === 'active')>{{ __('admin.active') }}</option>
                <option value="inactive" @selected(request('status') === 'inactive')>{{ __('admin.inactive') }}</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.category') }}</label>
            <select class="form-select" name="category">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button class="btn btn-outline-primary w-100">{{ __('admin.filter') }}</button>
        </div>
    </form>

    @if($items->isEmpty())
        <div class="text-muted">{{ __('admin.chatbot_knowledge_empty') }}</div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>{{ __('admin.title') }}</th>
                        <th>{{ __('admin.category') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th>{{ __('admin.priority') }}</th>
                        <th>{{ __('admin.updated_at') }}</th>
                        <th class="text-end">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $entry)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $entry->title_ar }}</div>
                                <div class="text-muted small">{{ $entry->title_en }}</div>
                            </td>
                            <td>
                                <div>{{ $entry->category_ar ?: '-' }}</div>
                                <div class="text-muted small">{{ $entry->category_en ?: '-' }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $entry->is_active ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    {{ $entry->is_active ? __('admin.active') : __('admin.inactive') }}
                                </span>
                            </td>
                            <td>{{ $entry->priority }}</td>
                            <td>{{ optional($entry->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.chatbot-knowledge.edit', $entry) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.edit') }}</a>
                                    <form method="post" action="{{ route('admin.chatbot-knowledge.destroy', $entry) }}" onsubmit="return confirm('{{ __('admin.delete_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $items->links() }}
    @endif
</div>
@endsection
