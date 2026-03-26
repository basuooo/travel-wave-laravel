@extends('layouts.admin')

@section('page_title', __('admin.crm_tasks'))
@section('page_description', __('admin.crm_tasks_desc'))

@section('content')
<style>
.crm-task-board-toolbar { display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; justify-content:space-between; margin-bottom:1.25rem; }
.crm-task-board-shell { overflow-x:auto; padding-bottom:1rem; }
.crm-task-board { display:grid; grid-template-columns:repeat(5, minmax(310px, 1fr)); gap:1rem; min-width:1620px; align-items:start; }
.crm-task-column { background:linear-gradient(180deg, #f8fafc 0%, #eef3f8 100%); border:1px solid rgba(15, 23, 42, .08); border-radius:22px; box-shadow:0 12px 24px rgba(15, 23, 42, .05); min-height:72vh; }
.crm-task-column__header { padding:1rem 1rem .9rem; border-bottom:1px solid rgba(15, 23, 42, .08); position:sticky; top:0; background:inherit; z-index:2; border-radius:22px 22px 0 0; backdrop-filter:blur(6px); }
.crm-task-column__body { padding:1rem; display:grid; gap:.85rem; min-height:220px; }
.crm-task-card { border-radius:18px; border:1px solid rgba(15, 23, 42, .08); background:#fff; padding:1rem; box-shadow:0 8px 18px rgba(15, 23, 42, .07); cursor:grab; transition:transform .15s ease, box-shadow .15s ease, border-color .15s ease; }
.crm-task-card:hover { transform:translateY(-1px); box-shadow:0 12px 24px rgba(15, 23, 42, .10); }
.crm-task-card.is-delayed { border-color:rgba(220, 53, 69, .38); box-shadow:0 12px 24px rgba(220, 53, 69, .10); }
.crm-task-card__title { font-size:.96rem; line-height:1.45; }
.crm-task-card__snippet { color:#6c757d; font-size:.84rem; line-height:1.45; }
.crm-task-card__meta { display:flex; flex-wrap:wrap; gap:.45rem; align-items:center; }
.crm-task-card__meta-row { display:flex; justify-content:space-between; gap:.75rem; align-items:center; color:#6c757d; font-size:.82rem; }
.crm-task-card__handle { font-size:.9rem; color:#6c757d; cursor:grab; letter-spacing:.08em; }
.crm-task-empty { border:1px dashed rgba(15, 23, 42, .14); border-radius:16px; padding:1rem; text-align:center; color:#6c757d; background:rgba(255,255,255,.65); }
.crm-task-drop-ghost { opacity:.55; transform:rotate(1deg); }
.crm-task-drop-chosen { box-shadow:0 16px 28px rgba(13, 110, 253, .18); }
.crm-task-filter-chips { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; }
.crm-task-filter-chips .btn { border-radius:999px; }
.crm-task-quick-add-btn { border-radius:999px; }
.crm-task-column-count { min-width:38px; text-align:center; }
.crm-task-overdue { color:#dc3545; font-weight:600; }
.crm-task-card .badge { font-weight:600; }
.crm-task-card [data-open-task-modal] { cursor:pointer; }
</style>

@php($query = request()->query())
@php($isArabic = app()->getLocale() === 'ar')

<div class="crm-task-board-toolbar">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        @foreach($presets as $key => $preset)
            <a href="{{ route($preset['route']) }}" class="btn btn-sm {{ $activePreset === $key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $preset['label'] }}</a>
        @endforeach
        <div class="vr d-none d-md-block"></div>
        <a href="{{ route('admin.crm.tasks.index', $query) }}" class="btn btn-sm {{ request()->routeIs('admin.crm.tasks.index') || request()->routeIs('admin.crm.tasks.board') ? 'btn-dark' : 'btn-outline-dark' }}">عرض كانبان</a>
        <a href="{{ route('admin.crm.tasks.list', $query) }}" class="btn btn-sm {{ request()->routeIs('admin.crm.tasks.list') ? 'btn-dark' : 'btn-outline-dark' }}">عرض جدولي</a>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.crm.tasks.create') }}" class="btn btn-success btn-sm">{{ __('admin.crm_task_create') }}</a>
    </div>
</div>

<div class="crm-task-filter-chips">
    <a href="{{ route('admin.crm.tasks.my', $query) }}" class="btn btn-sm {{ $activePreset === 'my' ? 'btn-primary' : 'btn-outline-primary' }}">مهامي</a>
    <a href="{{ route('admin.crm.tasks.delayed', $query) }}" class="btn btn-sm {{ !empty($filters['delayed_only']) || $activePreset === 'delayed' ? 'btn-danger' : 'btn-outline-danger' }}">المتأخرة</a>
    <a href="{{ route('admin.crm.tasks.today', $query) }}" class="btn btn-sm {{ !empty($filters['today_only']) || $activePreset === 'today' ? 'btn-warning' : 'btn-outline-warning' }}">اليوم</a>
    <a href="{{ route('admin.crm.tasks.index', array_merge($query, ['priority' => \App\Models\CrmTask::PRIORITY_HIGH])) }}" class="btn btn-sm {{ ($filters['priority'] ?? '') === \App\Models\CrmTask::PRIORITY_HIGH ? 'btn-dark' : 'btn-outline-dark' }}">عالية</a>
    <a href="{{ route('admin.crm.tasks.index', array_merge($query, ['priority' => \App\Models\CrmTask::PRIORITY_URGENT])) }}" class="btn btn-sm {{ ($filters['priority'] ?? '') === \App\Models\CrmTask::PRIORITY_URGENT ? 'btn-danger' : 'btn-outline-danger' }}">عاجلة</a>
    <a href="{{ route('admin.crm.tasks.index', array_merge($query, ['linked_state' => 'linked'])) }}" class="btn btn-sm {{ ($filters['linked_state'] ?? '') === 'linked' ? 'btn-info' : 'btn-outline-info' }}">مرتبطة بليد</a>
    <a href="{{ route('admin.crm.tasks.index', array_merge($query, ['task_type' => \App\Models\CrmTask::TYPE_GENERAL])) }}" class="btn btn-sm {{ ($filters['task_type'] ?? '') === \App\Models\CrmTask::TYPE_GENERAL ? 'btn-secondary' : 'btn-outline-secondary' }}">داخلية</a>
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
        <div class="col-md-2"><label class="form-label">{{ __('admin.priority') }}</label><select class="form-select" name="priority"><option value="">{{ __('admin.all') }}</option>@foreach($priorityOptions as $value => $label)<option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.crm_task_type') }}</label><select class="form-select" name="task_type"><option value="">{{ __('admin.all') }}</option>@foreach($typeOptions as $value => $label)<option value="{{ $value }}" @selected(($filters['task_type'] ?? '') === $value)>{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">التصنيف</label><select class="form-select" name="category"><option value="">{{ __('admin.all') }}</option>@foreach($categoryOptions as $value => $label)<option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.status') }}</label><select class="form-select" name="status"><option value="">{{ __('admin.all') }}</option>@foreach($statusOptions as $value => $label)<option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.crm_related_lead') }}</label><select class="form-select" name="linked_state"><option value="">{{ __('admin.all') }}</option><option value="linked" @selected(($filters['linked_state'] ?? '') === 'linked')>{{ __('admin.crm_task_linked') }}</option><option value="unlinked" @selected(($filters['linked_state'] ?? '') === 'unlinked')>{{ __('admin.crm_task_unlinked') }}</option></select></div>
        <div class="col-md-2"><label class="form-label">ليد محدد</label><select class="form-select" name="inquiry_id"><option value="">{{ __('admin.all') }}</option>@foreach($leads as $lead)<option value="{{ $lead->id }}" @selected((int) ($filters['inquiry_id'] ?? 0) === (int) $lead->id)>{{ $lead->full_name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.from') }}</label><input type="date" class="form-control" name="from" value="{{ $filters['from'] ?? '' }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.to') }}</label><input type="date" class="form-control" name="to" value="{{ $filters['to'] ?? '' }}"></div>
        <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="delayed_only" value="1" id="board_delayed_only" @checked(!empty($filters['delayed_only']))><label class="form-check-label" for="board_delayed_only">{{ __('admin.crm_delayed_tasks') }}</label></div></div>
        <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="today_only" value="1" id="board_today_only" @checked(!empty($filters['today_only']))><label class="form-check-label" for="board_today_only">{{ __('admin.crm_tasks_today') }}</label></div></div>
        <div class="col-md-4 d-flex gap-2"><button class="btn btn-primary">{{ __('admin.search') }}</button><a href="{{ route($presets[$activePreset]['route']) }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a></div>
    </div>
</form>

<div class="crm-task-board-shell">
    <div class="crm-task-board">
        @foreach($columns as $status => $tasks)
            <section class="crm-task-column">
                <div class="crm-task-column__header">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <h2 class="h6 mb-1">{{ $statusOptions[$status][$isArabic ? 'ar' : 'en'] ?? $status }}</h2>
                            <div class="small text-muted">
                                {{ $tasks->filter(fn ($task) => $task->isDelayed())->count() }} {{ __('admin.crm_delayed_tasks') }}
                            </div>
                        </div>
                        <span class="badge text-bg-secondary crm-task-column-count">{{ $tasks->count() }}</span>
                    </div>
                    @if($canManageTasks)
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary crm-task-quick-add-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#crmTaskQuickAddModal"
                            data-task-status="{{ $status }}"
                            data-task-status-label="{{ $statusOptions[$status][$isArabic ? 'ar' : 'en'] ?? $status }}"
                        >
                            + {{ __('admin.add_task') }}
                        </button>
                    @endif
                </div>
                <div class="crm-task-column__body" data-task-column data-status="{{ $status }}">
                    @forelse($tasks as $task)
                        <article
                            class="crm-task-card {{ $task->isDelayed() ? 'is-delayed' : '' }}"
                            data-task-card
                            data-task-id="{{ $task->id }}"
                            data-task-title="{{ e($task->title) }}"
                            data-task-description="{{ e($task->description ?: '-') }}"
                            data-task-status="{{ e($task->localizedStatus()) }}"
                            data-task-priority="{{ e($task->localizedPriority()) }}"
                            data-task-type="{{ e($task->localizedType()) }}"
                            data-task-category="{{ e($task->category ? $task->localizedCategory() : '-') }}"
                            data-task-assignee="{{ e($task->assignedUser?->name ?: '-') }}"
                            data-task-creator="{{ e($task->creator?->name ?: '-') }}"
                            data-task-due="{{ e(optional($task->due_at)->format('Y-m-d H:i') ?: '-') }}"
                            data-task-overdue="{{ e($task->overdueLabel() ?: '-') }}"
                            data-task-lead="{{ e($task->inquiry?->full_name ?: __('admin.crm_task_unlinked')) }}"
                            data-task-lead-url="{{ $task->inquiry ? route('admin.crm.leads.show', $task->inquiry) : '' }}"
                            data-task-view-url="{{ route('admin.crm.tasks.show', $task) }}"
                            data-task-edit-url="{{ route('admin.crm.tasks.edit', $task) }}"
                            data-task-completed="{{ $task->status === \App\Models\CrmTask::STATUS_COMPLETED ? '1' : '0' }}"
                        >
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="crm-task-card__title fw-semibold" data-open-task-modal>{{ $task->title }}</div>
                                <span class="crm-task-card__handle">::</span>
                            </div>
                            @if($task->description)
                                <div class="crm-task-card__snippet mb-3">{{ \Illuminate\Support\Str::limit($task->description, 95) }}</div>
                            @endif
                            <div class="crm-task-card__meta mb-2">
                                <span class="badge" style="{{ $task->priorityBadgeStyle() }}">{{ $task->localizedPriority() }}</span>
                                <span class="badge text-bg-{{ $task->typeBadgeClass() }}">{{ $task->localizedType() }}</span>
                                @if($task->category)
                                    <span class="badge text-bg-{{ $task->categoryBadgeClass() }}">{{ $task->localizedCategory() }}</span>
                                @endif
                                @if($task->isDelayed())
                                    <span class="badge text-bg-danger">متأخر</span>
                                @endif
                            </div>
                            <div class="crm-task-card__meta-row mb-1">
                                <span>{{ __('admin.assigned_to') }}</span>
                                <strong>{{ $task->assignedUser?->name ?: '-' }}</strong>
                            </div>
                            <div class="crm-task-card__meta-row mb-1">
                                <span>{{ __('admin.crm_related_lead') }}</span>
                                @if($task->inquiry)
                                    <a href="{{ route('admin.crm.leads.show', $task->inquiry) }}" class="text-decoration-none">{{ \Illuminate\Support\Str::limit($task->inquiry->full_name, 22) }}</a>
                                @else
                                    <span>{{ __('admin.crm_task_unlinked') }}</span>
                                @endif
                            </div>
                            <div class="crm-task-card__meta-row mt-3">
                                <div>
                                    <div class="small text-muted">{{ optional($task->due_at)->format('Y-m-d H:i') ?: '-' }}</div>
                                    @if($task->overdueLabel())
                                        <div class="small crm-task-overdue">{{ $task->overdueLabel() }}</div>
                                    @endif
                                </div>
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.crm.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                                    @if($canViewAllTasks || $task->assigned_user_id === auth()->id() || $task->created_by === auth()->id())
                                        <a href="{{ route('admin.crm.tasks.edit', $task) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.edit') }}</a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="crm-task-empty">لا توجد مهام في هذا العمود</div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>
</div>

@if($canManageTasks)
<div class="modal fade" id="crmTaskQuickAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.crm.tasks.store') }}">
                @csrf
                <input type="hidden" name="status" id="quick_add_status" value="{{ \App\Models\CrmTask::STATUS_NEW }}">
                <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                <div class="modal-header">
                    <h2 class="modal-title h5 mb-0">إضافة مهمة سريعة</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border py-2 px-3 mb-3">
                        ستُضاف المهمة داخل عمود: <strong id="quick_add_status_label">{{ $statusOptions[\App\Models\CrmTask::STATUS_NEW][$isArabic ? 'ar' : 'en'] }}</strong>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">{{ __('admin.title') }}</label><input class="form-control" name="title" required></div>
                        <div class="col-md-3"><label class="form-label">{{ __('admin.assigned_to') }}</label><select class="form-select" name="assigned_user_id" required>@foreach($users as $user)<option value="{{ $user->id }}" @selected((int) auth()->id() === (int) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
                        <div class="col-md-3"><label class="form-label">{{ __('admin.priority') }}</label><select class="form-select" name="priority">@foreach($priorityOptions as $value => $label)<option value="{{ $value }}" @selected($value === \App\Models\CrmTask::PRIORITY_MEDIUM)>{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
                        <div class="col-md-4"><label class="form-label">{{ __('admin.crm_task_type') }}</label><select class="form-select" name="task_type">@foreach($typeOptions as $value => $label)<option value="{{ $value }}">{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
                        <div class="col-md-4"><label class="form-label">التصنيف</label><select class="form-select" name="category"><option value="">-</option>@foreach($categoryOptions as $value => $label)<option value="{{ $value }}">{{ $label[$isArabic ? 'ar' : 'en'] }}</option>@endforeach</select></div>
                        <div class="col-md-4"><label class="form-label">{{ __('admin.crm_task_due_date') }}</label><input type="datetime-local" class="form-control" name="due_at"></div>
                        <div class="col-md-6"><label class="form-label">{{ __('admin.crm_related_lead') }}</label><select class="form-select" name="inquiry_id"><option value="">{{ __('admin.crm_task_unlinked') }}</option>@foreach($leads as $lead)<option value="{{ $lead->id }}">{{ $lead->full_name }}{{ $lead->phone ? ' - ' . $lead->phone : '' }}</option>@endforeach</select></div>
                        <div class="col-md-6"><label class="form-label">{{ __('admin.notes') }}</label><input class="form-control" name="notes"></div>
                        <div class="col-12"><label class="form-label">{{ __('admin.description') }}</label><textarea class="form-control" name="description" rows="3"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                    <button class="btn btn-primary">{{ __('admin.add_task') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="crmTaskDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5 mb-0" id="task_modal_title">تفاصيل المهمة</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="fw-semibold mb-2" id="task_modal_heading">-</div>
                        <div class="text-muted mb-3" id="task_modal_description">-</div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge text-bg-light" id="task_modal_status">-</span>
                            <span class="badge text-bg-light" id="task_modal_priority">-</span>
                            <span class="badge text-bg-light" id="task_modal_type">-</span>
                            <span class="badge text-bg-light" id="task_modal_category">-</span>
                        </div>
                        <div class="small mb-2">المسؤول: <strong id="task_modal_assignee">-</strong></div>
                        <div class="small mb-2">أنشأ بواسطة: <strong id="task_modal_creator">-</strong></div>
                        <div class="small mb-2">الاستحقاق: <strong id="task_modal_due">-</strong></div>
                        <div class="small text-danger fw-semibold" id="task_modal_overdue"></div>
                    </div>
                    <div class="col-lg-4">
                        <div class="border rounded-4 p-3 bg-light h-100">
                            <div class="small text-muted mb-1">{{ __('admin.crm_related_lead') }}</div>
                            <div id="task_modal_lead_wrapper"><span id="task_modal_lead">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success" data-quick-status="{{ \App\Models\CrmTask::STATUS_COMPLETED }}">إنهاء</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-quick-status="{{ \App\Models\CrmTask::STATUS_IN_PROGRESS }}">جاري العمل</button>
                    <button type="button" class="btn btn-sm btn-outline-warning" data-quick-status="{{ \App\Models\CrmTask::STATUS_WAITING }}">بانتظار رد</button>
                </div>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-primary" id="task_modal_view_link">{{ __('admin.view') }}</a>
                    <a href="#" class="btn btn-primary" id="task_modal_edit_link">{{ __('admin.edit') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = @json(csrf_token());
    const canDrag = @json(auth()->user()?->hasPermission('leads.view'));
    const quickAddModal = document.getElementById('crmTaskQuickAddModal');
    const detailsModalElement = document.getElementById('crmTaskDetailsModal');
    const detailsModal = detailsModalElement && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(detailsModalElement) : null;
    let activeTaskId = null;

    const updateTaskStatus = async (taskId, status) => {
        const response = await fetch(`{{ url('/admin/crm/tasks') }}/${taskId}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ status }),
        });

        if (!response.ok) {
            throw new Error('Unable to update task status');
        }

        return response.json();
    };

    if (quickAddModal) {
        quickAddModal.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            const statusInput = quickAddModal.querySelector('#quick_add_status');
            const statusLabel = quickAddModal.querySelector('#quick_add_status_label');
            statusInput.value = trigger?.dataset.taskStatus || '{{ \App\Models\CrmTask::STATUS_NEW }}';
            statusLabel.textContent = trigger?.dataset.taskStatusLabel || '';
        });
    }

    document.querySelectorAll('[data-open-task-modal]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const card = trigger.closest('[data-task-card]');
            if (!card || !detailsModal) return;

            activeTaskId = card.dataset.taskId;
            document.getElementById('task_modal_heading').textContent = card.dataset.taskTitle || '-';
            document.getElementById('task_modal_description').textContent = card.dataset.taskDescription || '-';
            document.getElementById('task_modal_status').textContent = card.dataset.taskStatus || '-';
            document.getElementById('task_modal_priority').textContent = card.dataset.taskPriority || '-';
            document.getElementById('task_modal_type').textContent = card.dataset.taskType || '-';
            document.getElementById('task_modal_category').textContent = card.dataset.taskCategory || '-';
            document.getElementById('task_modal_assignee').textContent = card.dataset.taskAssignee || '-';
            document.getElementById('task_modal_creator').textContent = card.dataset.taskCreator || '-';
            document.getElementById('task_modal_due').textContent = card.dataset.taskDue || '-';
            document.getElementById('task_modal_overdue').textContent = card.dataset.taskOverdue && card.dataset.taskOverdue !== '-' ? card.dataset.taskOverdue : '';
            document.getElementById('task_modal_view_link').href = card.dataset.taskViewUrl || '#';
            document.getElementById('task_modal_edit_link').href = card.dataset.taskEditUrl || '#';

            const leadWrapper = document.getElementById('task_modal_lead_wrapper');
            if (card.dataset.taskLeadUrl) {
                leadWrapper.innerHTML = `<a href="${card.dataset.taskLeadUrl}" class="text-decoration-none">${card.dataset.taskLead}</a>`;
            } else {
                leadWrapper.textContent = card.dataset.taskLead || '-';
            }

            detailsModal.show();
        });
    });

    document.querySelectorAll('[data-quick-status]').forEach((button) => {
        button.addEventListener('click', async () => {
            if (!activeTaskId) return;

            try {
                await updateTaskStatus(activeTaskId, button.dataset.quickStatus);
                window.location.reload();
            } catch (error) {
                console.error(error);
                window.location.reload();
            }
        });
    });

    if (!canDrag || typeof Sortable === 'undefined') return;

    document.querySelectorAll('[data-task-column]').forEach((column) => {
        Sortable.create(column, {
            group: 'crm-task-board',
            animation: 180,
            ghostClass: 'crm-task-drop-ghost',
            chosenClass: 'crm-task-drop-chosen',
            draggable: '[data-task-card]',
            onEnd: async (event) => {
                const taskCard = event.item;
                const taskId = taskCard.dataset.taskId;
                const targetStatus = event.to.dataset.status;

                if (!taskId || !targetStatus) return;

                try {
                    await updateTaskStatus(taskId, targetStatus);
                    window.location.reload();
                } catch (error) {
                    console.error(error);
                    window.location.reload();
                }
            },
        });
    });
});
</script>
@endsection
