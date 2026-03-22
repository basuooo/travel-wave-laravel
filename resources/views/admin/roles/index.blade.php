@extends('layouts.admin')

@section('title', __('admin.roles_management'))
@section('page_title', __('admin.roles_management'))
@section('page_description', __('admin.roles_management_desc'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">{{ __('admin.create_role') }}</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>{{ __('admin.role_name') }}</th>
                    <th>{{ __('admin.description') }}</th>
                    <th>{{ __('admin.users_count') }}</th>
                    <th>{{ __('admin.permissions_count_label') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->name }}</td>
                        <td>{{ $item->description ?: '—' }}</td>
                        <td>{{ $item->users_count }}</td>
                        <td>{{ $item->permissions_count }}</td>
                        <td>
                            <span class="badge {{ $item->is_system ? 'bg-info-subtle text-info' : 'bg-light text-dark border' }}">
                                {{ $item->is_system ? __('admin.system_role') : __('admin.custom_role') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.roles.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.edit') }}</a>
                                @if(! $item->is_system)
                                    <form method="post" action="{{ route('admin.roles.destroy', $item) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">{{ __('admin.no_roles') }}</td>
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
