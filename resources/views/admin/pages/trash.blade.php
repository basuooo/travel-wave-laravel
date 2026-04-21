@extends('layouts.admin')

@section('page_title', 'Pages Trash')
@section('page_description', 'Review deleted pages, restore them to the active list, or remove them permanently when you are fully sure.')

@section('content')
<div class="card admin-card p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.pages_trash') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.trash_desc') }}</p>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">{{ __('admin.back_to_pages') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>{{ __('admin.title_ar') }}</th>
                    <th>{{ __('admin.title_en') }}</th>
                    <th>{{ __('admin.key') }}</th>
                    <th>{{ __('admin.deleted_at') }}</th>
                    <th>{{ __('admin.deleted_by') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td class="text-end" dir="rtl">{{ $page->title_ar }}</td>
                        <td>{{ $page->title_en }}</td>
                        <td><code>{{ $page->key }}</code></td>
                        <td>{{ optional($page->deleted_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $page->deletedBy?->name ?? 'System' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                <form method="post" action="{{ route('admin.pages.restore', $page->id) }}" onsubmit="return confirm('{{ __('admin.confirm_restore') }}')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('admin.restore') }}</button>
                                </form>
                                <form method="post" action="{{ route('admin.pages.force-destroy', $page->id) }}" onsubmit="return confirm('{{ __('admin.confirm_delete_permanently') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">{{ __('admin.delete_permanently') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">{{ __('admin.trash_is_empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
