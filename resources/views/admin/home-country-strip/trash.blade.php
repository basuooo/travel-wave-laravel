@extends('layouts.admin')

@section('page_title', 'Country Items Trash')
@section('page_description', 'Review deleted country strip items, restore them to the active list, or permanently remove them when you are sure.')

@section('content')
<div class="card admin-card p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h5 mb-1">Country Items Trash</h2>
            <p class="text-muted mb-0">Deleted country items stay here safely until you restore them or permanently delete them.</p>
        </div>
        <a href="{{ route('admin.home-country-strip.index') }}" class="btn btn-outline-secondary">Back to Country Items</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Linked Page</th>
                    <th>Deleted At</th>
                    <th>Deleted By</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->displayName('en') }}</div>
                            <div class="text-muted small" dir="rtl">{{ $item->displayName('ar') }}</div>
                        </td>
                        <td>{{ $item->visaCountry?->name_en ?: ($item->custom_url ?: 'Not set') }}</td>
                        <td>{{ optional($item->deleted_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $item->deletedBy?->name ?? 'System' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                <form method="post" action="{{ route('admin.home-country-strip.restore', $item->id) }}" onsubmit="return confirm('Restore this country item to the active list?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Restore</button>
                                </form>
                                <form method="post" action="{{ route('admin.home-country-strip.force-destroy', $item->id) }}" onsubmit="return confirm('Permanently delete this country item? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete Permanently</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Trash is empty.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
