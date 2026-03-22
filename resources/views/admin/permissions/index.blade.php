@extends('layouts.admin')

@section('title', __('admin.permissions_management'))
@section('page_title', __('admin.permissions_management'))
@section('page_description', __('admin.permissions_management_desc'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">{{ __('admin.create_permission') }}</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>{{ __('admin.name') }}</th>
                    <th>{{ __('admin.slug_key') }}</th>
                    <th>{{ __('admin.module') }}</th>
                    <th>{{ __('admin.roles_count') }}</th>
                    <th>{{ __('admin.description') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->name }}</td>
                        <td><code>{{ $item->slug }}</code></td>
                        <td class="text-capitalize">{{ str_replace('_', ' ', $item->module) }}</td>
                        <td>{{ $item->roles_count }}</td>
                        <td>{{ $item->description ?: '—' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.permissions.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.edit') }}</a>
                                <form method="post" action="{{ route('admin.permissions.destroy', $item) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">{{ __('admin.no_permissions') }}</td>
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
