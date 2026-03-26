@extends('layouts.admin')

@section('page_title', __('admin.commission_statements'))
@section('page_description', __('admin.goals_commissions_desc'))

@section('content')
@if(auth()->user()?->hasPermission('goals_commissions.manage'))
    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.generate_commission_statement') }}</h2>
        <form method="post" action="{{ route('admin.goals-commissions.commissions.store') }}" class="row g-3">
            @csrf
            <div class="col-md-3"><label class="form-label">{{ __('admin.employee') }}</label><select name="user_id" class="form-select" required><option value="">{{ __('admin.all') }}</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label">{{ __('admin.commission_basis') }}</label><select name="basis_type" class="form-select" required>@foreach($basisTypes as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">{{ __('admin.from_date') }}</label><input type="date" name="period_start" value="{{ $defaultPeriodStart }}" class="form-control" required></div>
            <div class="col-md-2"><label class="form-label">{{ __('admin.to_date') }}</label><input type="date" name="period_end" value="{{ $defaultPeriodEnd }}" class="form-control" required></div>
            <div class="col-md-2"><label class="form-label">{{ __('admin.notes') }}</label><input name="note" class="form-control"></div>
            <div class="col-12"><button class="btn btn-primary">{{ __('admin.generate_commission_statement') }}</button></div>
        </form>
    </div>
@endif

<form class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3"><label class="form-label">{{ __('admin.employee') }}</label><select name="user_id" class="form-select"><option value="">{{ __('admin.all') }}</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((int) ($filters['user_id'] ?? 0) === (int) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.commission_basis') }}</label><select name="basis_type" class="form-select"><option value="">{{ __('admin.all') }}</option>@foreach($basisTypes as $value => $label)<option value="{{ $value }}" @selected(($filters['basis_type'] ?? null) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.commission_payment_status') }}</label><select name="payment_status" class="form-select"><option value="">{{ __('admin.all') }}</option><option value="unpaid" @selected(($filters['payment_status'] ?? null) === 'unpaid')>{{ __('admin.commission_payment_status_unpaid') }}</option><option value="partially_paid" @selected(($filters['payment_status'] ?? null) === 'partially_paid')>{{ __('admin.commission_payment_status_partially_paid') }}</option><option value="fully_paid" @selected(($filters['payment_status'] ?? null) === 'fully_paid')>{{ __('admin.commission_payment_status_fully_paid') }}</option></select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.from_date') }}</label><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control"></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.to_date') }}</label><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control"></div>
        <div class="col-md-4 d-flex gap-2"><button class="btn btn-primary">{{ __('admin.search') }}</button><a href="{{ route('admin.goals-commissions.commissions.index') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a></div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.commission_earned') }}</div><div class="fs-4 fw-semibold">{{ number_format((float) $summary['earned'], 2) }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.commission_paid') }}</div><div class="fs-4 fw-semibold text-success">{{ number_format((float) $summary['paid'], 2) }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.commission_remaining') }}</div><div class="fs-4 fw-semibold text-danger">{{ number_format((float) $summary['remaining'], 2) }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.commission_unpaid_count') }}</div><div class="fs-4 fw-semibold">{{ $summary['unpaid_count'] }}</div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.employee') }}</th>
                            <th>{{ __('admin.goal_period') }}</th>
                            <th>{{ __('admin.commission_basis') }}</th>
                            <th>{{ __('admin.commission_earned') }}</th>
                            <th>{{ __('admin.commission_paid') }}</th>
                            <th>{{ __('admin.commission_remaining') }}</th>
                            <th>{{ __('admin.commission_payment_status') }}</th>
                            <th class="text-end">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->user?->name ?: '-' }}</td>
                            <td>{{ optional($item->period_start)->format('Y-m-d') }} - {{ optional($item->period_end)->format('Y-m-d') }}</td>
                            <td>{{ $item->localizedBasisType() }}</td>
                            <td>{{ number_format((float) $item->earned_amount, 2) }}</td>
                            <td>{{ number_format((float) $item->paid_amount, 2) }}</td>
                            <td>{{ number_format((float) $item->remaining_amount, 2) }}</td>
                            <td><span class="badge text-bg-{{ $item->payment_status === 'fully_paid' ? 'success' : ($item->payment_status === 'partially_paid' ? 'warning' : 'secondary') }}">{{ $item->localizedPaymentStatus() }}</span></td>
                            <td class="text-end"><a href="{{ route('admin.goals-commissions.commissions.show', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">{{ __('admin.no_data_available') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $items->links() }}</div>
    </div>
    <div class="col-lg-4">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.seller_ranking') }}</h2>
            <div class="d-grid gap-3">
                @forelse($ranking as $row)
                    <div class="border rounded-4 p-3">
                        <div class="fw-semibold">{{ $row['user']?->name ?: '-' }}</div>
                        <div class="small text-muted">{{ __('admin.commission_earned') }}: {{ number_format((float) $row['earned_amount'], 2) }}</div>
                        <div class="small text-muted">{{ __('admin.commission_remaining') }}: {{ number_format((float) $row['remaining_amount'], 2) }}</div>
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.no_data_available') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
