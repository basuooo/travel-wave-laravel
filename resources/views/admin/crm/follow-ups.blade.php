@extends('layouts.admin')

@section('page_title', __('admin.crm_followups'))
@section('page_description', __('admin.crm_followups_desc'))

@section('content')
<form class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select class="form-select" name="range">
                <option value="">{{ __('admin.all_types') }}</option>
                <option value="today" @selected(request('range') === 'today')>{{ __('admin.crm_due_today') }}</option>
                <option value="upcoming" @selected(request('range') === 'upcoming')>{{ __('admin.crm_upcoming_followups') }}</option>
                <option value="overdue" @selected(request('range') === 'overdue')>{{ __('admin.crm_overdue') }}</option>
                <option value="completed" @selected(request('range') === 'completed')>{{ __('admin.crm_follow_up_completed') }}</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.assigned_to') }}</label>
            <select class="form-select" name="assigned_user_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @if($canViewAllLeads)
                    <option value="unassigned" @selected(request('assigned_user_id') === 'unassigned')>{{ __('admin.crm_unassigned') }}</option>
                @endif
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) request('assigned_user_id') === (string) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.created_date') }}</label><input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.to_date') }}</label><input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">{{ __('admin.search') }}</button></div>
    </div>
</form>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>{{ __('admin.full_name') }}</th><th>{{ __('admin.status') }}</th><th>{{ __('admin.assigned_to') }}</th><th>{{ __('admin.next_follow_up') }}</th><th>{{ __('admin.crm_reminder_before') }}</th><th>{{ __('admin.crm_follow_up_state') }}</th><th></th></tr></thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->inquiry?->full_name }}</td>
                        <td>{{ $item->inquiry?->localizedStatus() }}</td>
                        <td>{{ $item->assignedUser?->name ?: '—' }}</td>
                        <td>{{ optional($item->scheduled_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $item->reminderLabel() }}</td>
                        <td><span class="badge text-bg-light">{{ __('admin.crm_follow_up_' . $item->visualStatus()) }}</span></td>
                        <td class="text-end"><a href="{{ route('admin.crm.leads.show', $item->inquiry) }}" class="btn btn-sm btn-primary">{{ __('admin.view') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted">{{ __('admin.no_search_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
