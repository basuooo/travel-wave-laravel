@extends('layouts.admin')

@section('title', __('admin.users_management'))
@section('page_title', __('admin.users_management'))
@section('page_description', __('admin.users_management_desc'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        @if (auth()->user()?->hasPermission('users.create'))
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">{{ __('admin.create_user') }}</a>
        @endif
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>{{ __('admin.name') }}</th>
                    <th>{{ __('admin.email') }}</th>
                    <th>{{ __('admin.phone') }}</th>
                    <th>{{ __('admin.roles_label') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.last_login') }}</th>
                    <th>{{ __('admin.created_date') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->phone ?: '—' }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($item->roles as $role)
                                    <span class="badge bg-light text-dark border">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $item->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $item->is_active ? __('admin.active') : __('admin.inactive') }}
                            </span>
                        </td>
                        <td>{{ $item->last_login_at?->format('Y-m-d H:i') ?: '—' }}</td>
                        <td>{{ $item->created_at?->format('Y-m-d') }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                @if (auth()->user()?->hasPermission('users.edit'))
                                    <a href="{{ route('admin.users.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.edit') }}</a>
                                @endif
                                @if (auth()->user()?->hasPermission('users.delete'))
                                    <form method="post" action="{{ route('admin.users.destroy', $item) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
                        <td colspan="8" class="text-center text-muted py-5">{{ __('admin.no_users') }}</td>
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
