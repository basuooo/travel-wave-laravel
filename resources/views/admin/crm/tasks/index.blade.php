@extends('layouts.admin')

@section('page_title', __('admin.crm_tasks'))
@section('page_description', __('admin.crm_tasks_desc'))

@section('content')
@php($isArabic = app()->getLocale() === 'ar')
@php($query = request()->query())

<div class="d-flex flex-wrap gap-2 align-items-center mb-4">
    @foreach($presets as $key => $preset)
        <a href="{{ route($preset['route']) }}" class="btn btn-sm {{ $activePreset === $key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $preset['label'] }}</a>
    @endforeach
    <div class="vr d-none d-md-block"></div>
    <a href="{{ route('admin.crm.tasks.index', $query) }}" class="btn btn-sm {{ request()->routeIs('admin.crm.tasks.index') || request()->routeIs('admin.crm.tasks.board') ? 'btn-dark' : 'btn-outline-dark' }}">عرض كانبان</a>
    <a href="{{ route('admin.crm.tasks.list', $query) }}" class="btn btn-sm {{ request()->routeIs('admin.crm.tasks.list') ? 'btn-dark' : 'btn-outline-dark' }}">عرض جدولي</a>
    <a href="{{ route('admin.crm.tasks.create') }}" class="btn btn-sm btn-success">{{ __('admin.crm_task_create') }}</a>
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('admin.crm.tasks.my', $query) }}" class="btn btn-sm btn-outline-primary">مهامي</a>
    <a href="{{ route('admin.crm.tasks.delayed', $query) }}" class="btn btn-sm btn-outline-danger">المتأخرة</a>
    <a href="{{ route('admin.crm.tasks.today', $query) }}" class="btn btn-sm btn-outline-warning">اليوم</a>
    <a href="{{ route('admin.crm.tasks.list', array_merge($query, ['priority' => \App\Models\CrmTask::PRIORITY_HIGH])) }}" class="btn btn-sm btn-outline-dark">عالية</a>
    <a href="{{ route('admin.crm.tasks.list', array_merge($query, ['linked_state' => 'linked'])) }}" class="btn btn-sm btn-outline-info">مرتبطة بليد</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_open_tasks') }}</div><div class="fs-4 fw-semibold">{{ $summary['open'] }}</div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_tasks_today') }}</div><div class="fs-4 fw-semibold">{{ $summary['today'] }}</div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_delayed_tasks') }}</div><div class="fs-4 fw-semibold text-danger">{{ $summary['delayed'] }}</div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_completed_today') }}</div><div class="fs-4 fw-semibold text-success">{{ $summary['completed_today'] }}</div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_my_open_tasks') }}</div><div class="fs-4 fw-semibold">{{ $summary['my_open'] }}</div></div></div>
    <div class="col-xl-2 col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">المهام العاجلة</div><div class="fs-4 fw-semibold text-danger">{{ $summary['urgent'] }}</div></div></div>
</div>

<form method="GET" class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3"><label class="form-label">{{ __('admin.search') }}</label><input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.assigned_to') }}</label><select class="form-select" name="assigned_user_id"><option value="">{{ __('admin.all') }}</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((int) ($filters['assigned_user_id'] ?? 0) === (int) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.created_by') }}</label><select class="form-select" name="created_by"><option value="">{{ __('admin.all') }}</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((int) ($filters['created_by'] ?? 0) === (int) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.status') }}</label><select class="form-select" name="status"><option value="">{{ __('admin.all') }}</option>@foreach($statusOptions as $value => $label)<option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
        <div class="col-md-1"><label class="form-label">{{ __('admin.priority') }}</label><select class="form-select" name="priority"><option value="">{{ __('admin.all') }}</option>@foreach($priorityOptions as $value => $label)<option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.crm_task_type') }}</label><select class="form-select" name="task_type"><option value="">{{ __('admin.all') }}</option>@foreach($typeOptions as $value => $label)<option value="{{ $value }}" @selected(($filters['task_type'] ?? '') === $value)>{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">التصنيف</label><select class="form-select" name="category"><option value="">{{ __('admin.all') }}</option>@foreach($categoryOptions as $value => $label)<option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.crm_related_lead') }}</label><select class="form-select" name="linked_state"><option value="">{{ __('admin.all') }}</option><option value="linked" @selected(($filters['linked_state'] ?? '') === 'linked')>{{ __('admin.crm_task_linked') }}</option><option value="unlinked" @selected(($filters['linked_state'] ?? '') === 'unlinked')>{{ __('admin.crm_task_unlinked') }}</option></select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.from') }}</label><input type="date" class="form-control" name="from" value="{{ $filters['from'] ?? '' }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.to') }}</label><input type="date" class="form-control" name="to" value="{{ $filters['to'] ?? '' }}"></div>
        <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="delayed_only" value="1" id="delayed_only" @checked(!empty($filters['delayed_only']))><label class="form-check-label" for="delayed_only">{{ __('admin.crm_delayed_tasks') }}</label></div></div>
        <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="completed_only" value="1" id="completed_only" @checked(!empty($filters['completed_only']))><label class="form-check-label" for="completed_only">{{ __('admin.crm_completed_tasks') }}</label></div></div>
        <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="today_only" value="1" id="today_only" @checked(!empty($filters['today_only']))><label class="form-check-label" for="today_only">{{ __('admin.crm_tasks_today') }}</label></div></div>
        <div class="col-md-4 d-flex gap-2"><button class="btn btn-primary">{{ __('admin.search') }}</button><a href="{{ route('admin.crm.tasks.list') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a></div>
    </div>
</form>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.title') }}</th>
                    <th>{{ __('admin.crm_task_type') }}</th>
                    <th>التصنيف</th>
                    <th>{{ __('admin.full_name') }}</th>
                    <th>{{ __('admin.assigned_to') }}</th>
                    <th>{{ __('admin.created_by') }}</th>
                    <th>{{ __('admin.priority') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.crm_task_due_date') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $task)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $task->title }}</div>
                            @if($task->description)
                                <div class="small text-muted">{{ \Illuminate\Support\Str::limit($task->description, 90) }}</div>
                            @endif
                            @if($task->overdueLabel())
                                <div class="small text-danger fw-semibold mt-1">{{ $task->overdueLabel() }}</div>
                            @endif
                        </td>
                        <td><span class="badge text-bg-{{ $task->typeBadgeClass() }}">{{ $task->localizedType() }}</span></td>
                        <td>
                            @if($task->category)
                                <span class="badge text-bg-{{ $task->categoryBadgeClass() }}">{{ $task->localizedCategory() }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($task->inquiry)
                                <a href="{{ route('admin.crm.leads.show', $task->inquiry) }}">{{ $task->inquiry->full_name }}</a>
                            @else
                                <span class="text-muted">{{ __('admin.crm_task_unlinked') }}</span>
                            @endif
                        </td>
                        <td>{{ $task->assignedUser?->name ?: '-' }}</td>
                        <td>{{ $task->creator?->name ?: '-' }}</td>
                        <td><span class="badge" style="{{ $task->priorityBadgeStyle() }}">{{ $task->localizedPriority() }}</span></td>
                        <td><span class="badge text-bg-{{ $task->visualStatus() }}">{{ $task->localizedStatus() }}</span></td>
                        <td>{{ optional($task->due_at)->format('Y-m-d H:i') ?: '-' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.crm.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                                <a href="{{ route('admin.crm.tasks.edit', $task) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.edit') }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
