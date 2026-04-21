@extends('layouts.admin')

@section('page_title', 'Visa Countries')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <a href="{{ route('admin.visa-countries.trash') }}" class="btn btn-outline-secondary">{{ __('admin.trash') }}</a>
    <a href="{{ route('admin.visa-countries.create') }}" class="btn btn-primary">{{ __('admin.add_country') }}</a>
</div>
<div class="card admin-card p-4">
    <table class="table align-middle">
        <thead><tr><th>{{ __('admin.country') }}</th><th>{{ __('admin.category') }}</th><th>{{ __('admin.featured') }}</th><th>{{ __('admin.status') }}</th><th class="text-end">{{ __('admin.actions') }}</th></tr></thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->name_ar ?: $item->name_en }}</td>
                <td>{{ $item->category?->name_ar ?: $item->category?->name_en }}</td>
                <td>{{ $item->is_featured ? __('admin.yes') : __('admin.no') }}</td>
                <td><span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_active ? __('admin.active_status') : __('admin.draft_status') }}</span></td>
                <td class="text-end">
                    <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ route('admin.visa-countries.edit', $item) }}" class="btn btn-sm btn-primary">{{ __('admin.edit') }}</a>
                        @if($item->frontendUrl())
                            <a href="{{ $item->frontendUrl() }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer">{{ __('admin.view') }}</a>
                        @endif
                        <form method="post" action="{{ route('admin.visa-countries.duplicate', $item) }}">@csrf<button class="btn btn-sm btn-outline-primary">{{ __('admin.duplicate') }}</button></form>
                        <form method="post" action="{{ route('admin.visa-countries.destroy', $item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('admin.confirm_move_to_trash') }}')">{{ __('admin.delete') }}</button></form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->links() }}
</div>
@endsection
