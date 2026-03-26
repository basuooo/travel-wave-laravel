@extends('layouts.admin')

@section('page_title', __('admin.accounting_treasuries'))
@section('page_description', __('admin.accounting_treasuries_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <form method="GET" action="{{ route('admin.accounting.treasuries.index') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.search') }}</label>
            <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="{{ __('admin.accounting_treasury_search_placeholder') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.accounting_treasury_type') }}</label>
            <select name="type" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($typeOptions as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select name="status" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                <option value="active" @selected(request('status') === 'active')>{{ __('admin.active') }}</option>
                <option value="inactive" @selected(request('status') === 'inactive')>{{ __('admin.inactive') }}</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button class="btn btn-primary w-100">{{ __('admin.search') }}</button>
            @if(auth()->user()?->hasPermission('accounting.manage'))
                <a href="{{ route('admin.accounting.treasuries.create') }}" class="btn btn-outline-secondary text-nowrap">{{ __('admin.add') }}</a>
            @endif
        </div>
    </form>
</div>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.accounting_treasury_name') }}</th>
                    <th>{{ __('admin.accounting_treasury_type') }}</th>
                    <th>{{ __('admin.accounting_treasury_identifier') }}</th>
                    <th>{{ __('admin.accounting_treasury_incoming') }}</th>
                    <th>{{ __('admin.accounting_treasury_outgoing') }}</th>
                    <th>{{ __('admin.accounting_treasury_current_balance') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->localizedType() }}</td>
                        <td>{{ $item->identifier ?: '-' }}</td>
                        <td class="text-success">{{ number_format((float) ($item->incoming_total ?? 0), 2) }}</td>
                        <td class="text-danger">{{ number_format((float) ($item->outgoing_total ?? 0), 2) }}</td>
                        <td>{{ number_format($item->currentBalance(), 2) }}</td>
                        <td>
                            <span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $item->is_active ? __('admin.active') : __('admin.inactive') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.accounting.treasuries.show', $item) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.view') }}</a>
                                @if(auth()->user()?->hasPermission('accounting.manage'))
                                    <a href="{{ route('admin.accounting.treasuries.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.edit') }}</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <div>{{ __('admin.no_data') }}</div>
                            @if(auth()->user()?->hasPermission('accounting.manage'))
                                <div class="mt-2">
                                    <a href="{{ route('admin.accounting.treasuries.create') }}" class="btn btn-sm btn-outline-primary">{{ __('admin.accounting_add_treasury') }}</a>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
