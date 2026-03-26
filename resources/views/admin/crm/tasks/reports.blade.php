@extends('layouts.admin')

@section('page_title', __('admin.crm_task_reports'))
@section('page_description', __('admin.crm_tasks_desc'))

@section('content')
<form method="GET" class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3"><label class="form-label">{{ __('admin.from') }}</label><input type="date" name="from" class="form-control" value="{{ $filters['from'] }}"></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.to') }}</label><input type="date" name="to" class="form-control" value="{{ $filters['to'] }}"></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.assigned_to') }}</label><select name="assigned_user_id" class="form-select"><option value="">{{ __('admin.all') }}</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((int) ($filters['assigned_user_id'] ?? 0) === (int) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary">{{ __('admin.search') }}</button><a href="{{ route('admin.crm.tasks.reports') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a></div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_tasks') }}</div><div class="fs-4 fw-semibold">{{ $summary['total_tasks'] }}</div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_open_tasks') }}</div><div class="fs-4 fw-semibold">{{ $summary['open_tasks'] }}</div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_completed_tasks') }}</div><div class="fs-4 fw-semibold text-success">{{ $summary['completed_tasks'] }}</div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_delayed_tasks') }}</div><div class="fs-4 fw-semibold text-danger">{{ $summary['delayed_tasks'] }}</div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_tasks_today') }}</div><div class="fs-4 fw-semibold">{{ $summary['today_tasks'] }}</div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">المهام العاجلة</div><div class="fs-4 fw-semibold text-danger">{{ $summary['urgent_open_tasks'] }}</div></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_task_employee_performance') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.assigned_to') }}</th><th>مفتوحة</th><th>عاجلة</th><th>{{ __('admin.crm_completed_tasks') }}</th><th>{{ __('admin.crm_delayed_tasks') }}</th><th>{{ __('admin.crm_task_completion_rate') }}</th><th>{{ __('admin.crm_task_delayed_rate') }}</th><th>{{ __('admin.crm_task_last_activity') }}</th></tr></thead>
                    <tbody>
                    @forelse($employeePerformance as $row)
                        <tr>
                            <td>{{ $row['user']->name }}</td>
                            <td>{{ $row['open'] }}</td>
                            <td>{{ $row['urgent'] }}</td>
                            <td>{{ $row['completed'] }}</td>
                            <td>{{ $row['delayed'] }}</td>
                            <td>{{ $row['completion_rate'] }}%</td>
                            <td>{{ $row['delayed_rate'] }}%</td>
                            <td>{{ optional($row['last_activity_at'])->format('Y-m-d H:i') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">تقرير الأعمار</h2>
            <div class="table-responsive"><table class="table align-middle mb-0"><tbody>
                <tr><td>متأخرة +1 يوم</td><td class="text-end">{{ $aging['over_1_day'] }}</td></tr>
                <tr><td>متأخرة +3 أيام</td><td class="text-end">{{ $aging['over_3_days'] }}</td></tr>
                <tr><td>متأخرة +7 أيام</td><td class="text-end">{{ $aging['over_7_days'] }}</td></tr>
                <tr><td>متأخرة +14 يوم</td><td class="text-end">{{ $aging['over_14_days'] }}</td></tr>
            </tbody></table></div>
        </div>
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">توزيع الأنواع</h2>
            <div class="table-responsive"><table class="table align-middle mb-0"><tbody>@foreach($typeCounts as $row)<tr><td>{{ $row['label'] }}</td><td class="text-end">{{ $row['count'] }}</td></tr>@endforeach</tbody></table></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.status') }}</h2>
            <div class="table-responsive"><table class="table align-middle mb-0"><tbody>@foreach($statusCounts as $row)<tr><td>{{ $row['label'] }}</td><td class="text-end">{{ $row['count'] }}</td></tr>@endforeach</tbody></table></div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.priority') }}</h2>
            <div class="table-responsive"><table class="table align-middle mb-0"><tbody>@foreach($priorityCounts as $row)<tr><td>{{ $row['label'] }}</td><td class="text-end">{{ $row['count'] }}</td></tr>@endforeach</tbody></table></div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">التصنيفات</h2>
            <div class="table-responsive"><table class="table align-middle mb-0"><tbody>@foreach($categoryCounts as $row)<tr><td>{{ $row['label'] }}</td><td class="text-end">{{ $row['count'] }}</td></tr>@endforeach</tbody></table></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">ضغط العمل على الموظفين</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.assigned_to') }}</th><th>مفتوحة</th><th>عاجلة</th><th>متأخرة</th></tr></thead>
                    <tbody>
                    @forelse($workloadRows as $row)
                        <tr>
                            <td>{{ $row['user']->name }}</td>
                            <td>{{ $row['open'] }}</td>
                            <td>{{ $row['urgent'] }}</td>
                            <td>{{ $row['delayed'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">كثافة المهام على الليدات</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>الليد</th><th>إجمالي</th><th>مفتوحة</th><th>متأخرة</th></tr></thead>
                    <tbody>
                    @forelse($leadDensity->take(10) as $row)
                        <tr>
                            <td><a href="{{ route('admin.crm.leads.show', $row['lead']) }}">{{ $row['lead']->full_name }}</a></td>
                            <td>{{ $row['total'] }}</td>
                            <td>{{ $row['open'] }}</td>
                            <td>{{ $row['delayed'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_delayed_tasks') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.title') }}</th><th>{{ __('admin.assigned_to') }}</th><th>{{ __('admin.crm_task_due_date') }}</th></tr></thead>
                    <tbody>
                    @forelse($delayedItems as $item)
                        <tr>
                            <td><a href="{{ route('admin.crm.tasks.show', $item) }}">{{ $item->title }}</a><div class="small text-danger">{{ $item->overdueLabel() }}</div></td>
                            <td>{{ $item->assignedUser?->name ?: '-' }}</td>
                            <td>{{ optional($item->due_at)->format('Y-m-d H:i') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">اليوم / غدًا</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.title') }}</th><th>التوقيت</th><th>{{ __('admin.assigned_to') }}</th></tr></thead>
                    <tbody>
                    @forelse($todayItems->merge($tomorrowItems)->take(12) as $item)
                        <tr>
                            <td><a href="{{ route('admin.crm.tasks.show', $item) }}">{{ $item->title }}</a></td>
                            <td>{{ optional($item->due_at)->format('Y-m-d H:i') ?: '-' }}</td>
                            <td>{{ $item->assignedUser?->name ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">القادم هذا الأسبوع</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.title') }}</th><th>{{ __('admin.assigned_to') }}</th><th>{{ __('admin.crm_task_due_date') }}</th></tr></thead>
                    <tbody>
                    @forelse($upcomingItems->take(10) as $item)
                        <tr>
                            <td><a href="{{ route('admin.crm.tasks.show', $item) }}">{{ $item->title }}</a></td>
                            <td>{{ $item->assignedUser?->name ?: '-' }}</td>
                            <td>{{ optional($item->due_at)->format('Y-m-d H:i') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
