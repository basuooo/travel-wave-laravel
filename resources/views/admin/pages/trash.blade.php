@extends('layouts.admin')

@section('page_title', 'Pages Trash')
@section('page_description', 'Review deleted pages, restore them to the active list, or remove them permanently when you are fully sure.')

@section('content')
<div class="card admin-card p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h5 mb-1">Pages Trash</h2>
            <p class="text-muted mb-0">Deleted pages stay here safely until you restore them or permanently delete them.</p>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Back to Pages</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Title AR</th>
                    <th>Title EN</th>
                    <th>Key</th>
                    <th>Deleted At</th>
                    <th>Deleted By</th>
                    <th class="text-end">Actions</th>
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
                                <form method="post" action="{{ route('admin.pages.restore', $page->id) }}" onsubmit="return confirm('Restore this page to the active Pages list?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Restore</button>
                                </form>
                                <form method="post" action="{{ route('admin.pages.force-destroy', $page->id) }}" onsubmit="return confirm('Permanently delete this page? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete Permanently</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Trash is empty.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
