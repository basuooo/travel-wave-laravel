@extends('layouts.admin')

@section('title', __('admin.media_library'))
@section('page_title', __('admin.media_library'))
@section('page_description', __('admin.media_library_desc'))

@section('content')
    <div class="card admin-card admin-surface-card mb-4">
        <div class="card-body p-4">
            <form method="get" action="{{ route('admin.media-library.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-6">
                    <label class="form-label">{{ __('admin.search') }}</label>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="{{ __('admin.media_search_placeholder') }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">{{ __('admin.type') }}</label>
                    <select name="extension" class="form-select">
                        <option value="">{{ __('admin.all_types') }}</option>
                        @foreach($extensions as $extension)
                            <option value="{{ $extension }}" @selected(request('extension') === $extension)>{{ strtoupper($extension) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1">{{ __('admin.search') }}</button>
                    <a href="{{ route('admin.media-library.index') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card admin-card admin-surface-card mb-4">
        <div class="card-body p-4">
            <form method="post" enctype="multipart/form-data" action="{{ route('admin.media-library.store') }}" class="row g-3 align-items-end">
                @csrf
                <div class="col-lg-9">
                    <label class="form-label">{{ __('admin.upload_images') }}</label>
                    <input type="file" name="files[]" class="form-control" multiple accept="image/*,.svg">
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-primary w-100">{{ __('admin.upload_to_library') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-media-grid">
        @forelse($items as $item)
            <article class="admin-media-card">
                <div class="admin-media-card__preview">
                    <img src="{{ $item->public_url }}" alt="{{ $item->alt_text ?: $item->title }}"
                         onerror="this.onerror=null;this.closest('.admin-media-card__preview').classList.add('is-missing');this.src='data:image/svg+xml;charset=UTF-8,{{ rawurlencode("<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"640\" height=\"420\" viewBox=\"0 0 640 420\"><rect width=\"640\" height=\"420\" rx=\"28\" fill=\"#F5F6FB\"/><rect x=\"88\" y=\"92\" width=\"464\" height=\"236\" rx=\"20\" fill=\"#FFFFFF\" stroke=\"#E7E9F2\" stroke-width=\"2\"/><circle cx=\"188\" cy=\"172\" r=\"34\" fill=\"#ECE9FF\"/><path d=\"M166 282L258 204L332 258L392 214L474 282H166Z\" fill=\"#EDEFFD\"/><text x=\"320\" y=\"360\" text-anchor=\"middle\" fill=\"#7C859D\" font-family=\"Segoe UI, Arial, sans-serif\" font-size=\"22\">Preview unavailable</text></svg>") }}'">
                </div>
                <div class="admin-media-card__body">
                    <strong>{{ $item->title ?: $item->file_name }}</strong>
                    <div class="small text-muted">{{ $item->file_name }}</div>
                    <div class="small text-muted">{{ strtoupper($item->extension ?: '—') }} · {{ $item->created_at?->format('Y-m-d') }}</div>
                    <div class="small text-muted">{{ number_format(($item->size ?? 0) / 1024, 1) }} KB</div>
                    @php($usageCount = $usageCounts[\App\Support\MediaLibraryService::normalizePath((string) $item->path)] ?? 0)
                    <div class="small mt-2 {{ $usageCount < 1 ? 'text-muted' : 'text-success' }}">
                        {{ $usageCount < 1 ? __('admin.media_unused') : __('admin.media_used_in', ['count' => $usageCount]) }}
                    </div>
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="navigator.clipboard.writeText('{{ $item->public_url }}')">{{ __('admin.copy_url') }}</button>
                        <form method="post" action="{{ route('admin.media-library.update', $item) }}" class="d-inline-flex gap-2">
                            @csrf
                            @method('put')
                            <input type="hidden" name="is_favorite" value="{{ $item->is_favorite ? 0 : 1 }}">
                            <button type="submit" class="btn btn-outline-secondary btn-sm">{{ $item->is_favorite ? __('admin.unfavorite') : __('admin.favorite') }}</button>
                        </form>
                        <form method="post" action="{{ route('admin.media-library.destroy', $item) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                            @csrf
                            @method('delete')
                            <button class="btn btn-outline-danger btn-sm" @disabled($usageCount > 0)>{{ __('admin.delete') }}</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="card admin-card p-5 text-center text-muted">
                {{ __('admin.no_media_assets') }}
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
@endsection
