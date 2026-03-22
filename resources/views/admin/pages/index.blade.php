@extends('layouts.admin')

@section('page_title', 'Pages')
@section('page_description', 'Create, edit, duplicate, view, and safely manage both core pages and custom pages.')

@section('content')
<div class="card admin-card p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h5 mb-1">Pages Manager</h2>
            <p class="text-muted mb-0">Manage active pages here, and move any page safely to Trash when it should leave the live list.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.pages.trash') }}" class="btn btn-outline-secondary">Pages Trash</a>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">Create New Page</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Title AR</th>
                    <th>Title EN</th>
                    <th>Key</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td class="text-end" dir="rtl">{{ $page->title_ar }}</td>
                        <td>{{ $page->title_en }}</td>
                        <td><code>{{ $page->key }}</code></td>
                        <td>
                            <span class="badge {{ $page->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $page->is_active ? 'Active' : 'Draft' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-primary">Edit</a>
                                @if($page->frontendUrl())
                                    <a href="{{ $page->frontendUrl() }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer">View</a>
                                @endif
                                <form method="post" action="{{ route('admin.pages.duplicate', $page) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Duplicate</button>
                                </form>
                                <form method="post" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Move this page to Trash? You can restore it later.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No pages found yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
