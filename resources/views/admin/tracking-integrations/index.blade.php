@extends('layouts.admin')

@section('page_title', __('admin.tracking_manager'))
@section('page_description', __('admin.tracking_manager_desc'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="{{ route('admin.tracking-integrations.create') }}" class="btn btn-primary">{{ __('admin.create_tracking') }}</a>
</div>

<div class="card admin-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>{{ __('admin.name') }}</th>
                    <th>{{ __('admin.type') }}</th>
                    <th>{{ __('admin.placement') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.sort_order') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->name }}</div>
                            <div class="text-muted small">{{ $item->slug }}</div>
                        </td>
                        <td>{{ $typeLabels[$item->integration_type] ?? $item->integration_type }}</td>
                        <td>{{ $placementLabels[$item->placement] ?? $item->placement }}</td>
                        <td>
                            <span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $item->is_active ? __('admin.active') : __('admin.inactive') }}
                            </span>
                        </td>
                        <td>{{ $item->sort_order }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.tracking-integrations.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.edit') }}</a>
                                <form method="post" action="{{ route('admin.tracking-integrations.duplicate', $item) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary">{{ __('admin.duplicate') }}</button>
                                </form>
                                <form method="post" action="{{ route('admin.tracking-integrations.destroy', $item) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">{{ __('admin.no_tracking_integrations') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $items->links() }}
</div>
@endsection
