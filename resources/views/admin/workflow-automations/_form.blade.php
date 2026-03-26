@php($conditions = $automation->conditions ?? [])
@php($actions = $automation->actions ?? [])

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.workflow_ui_rule_name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $automation->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.workflow_ui_trigger') }}</label>
                <select name="trigger_type" class="form-select @error('trigger_type') is-invalid @enderror" required>
                    @foreach($triggerOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('trigger_type', $automation->trigger_type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('trigger_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.workflow_ui_priority') }}</label>
                <input type="number" name="priority" min="0" class="form-control @error('priority') is-invalid @enderror" value="{{ old('priority', $automation->priority ?? 100) }}">
                @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('admin.workflow_ui_description') }}</label>
                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $automation->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.workflow_ui_cooldown_minutes') }}</label>
                <input type="number" min="1" name="cooldown_minutes" class="form-control @error('cooldown_minutes') is-invalid @enderror" value="{{ old('cooldown_minutes', $automation->cooldown_minutes) }}">
                @error('cooldown_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $automation->is_active ?? true))>
                    <label class="form-check-label" for="is_active">{{ __('admin.workflow_ui_active') }}</label>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                    <input type="hidden" name="run_once" value="0">
                    <input class="form-check-input" type="checkbox" name="run_once" value="1" id="run_once" @checked(old('run_once', $automation->run_once ?? false))>
                    <label class="form-check-label" for="run_once">{{ __('admin.workflow_ui_run_once_per_record') }}</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white"><strong>{{ __('admin.workflow_ui_conditions') }}</strong></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.workflow_ui_source') }}</label>
                <select name="crm_source_id" class="form-select">
                    <option value="">{{ __('admin.workflow_ui_all') }}</option>
                    @foreach($leadSources as $source)
                        <option value="{{ $source->id }}" @selected((string) old('crm_source_id', $conditions['crm_source_id'] ?? '') === (string) $source->id)>{{ $source->localizedName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.workflow_ui_lead_status') }}</label>
                <select name="lead_status_id" class="form-select">
                    <option value="">{{ __('admin.workflow_ui_all') }}</option>
                    @foreach($leadStatuses as $status)
                        <option value="{{ $status->id }}" @selected((string) old('lead_status_id', $conditions['lead_status_id'] ?? '') === (string) $status->id)>{{ $status->localizedName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.workflow_ui_customer_stage') }}</label>
                <select name="customer_stage" class="form-select">
                    <option value="">{{ __('admin.workflow_ui_all') }}</option>
                    @foreach($customerStages as $value => $labels)
                        <option value="{{ $value }}" @selected(old('customer_stage', $conditions['customer_stage'] ?? '') === $value)>{{ $labels[app()->getLocale() === 'ar' ? 'ar' : 'en'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.workflow_ui_task_status') }}</label>
                <select name="task_status" class="form-select">
                    <option value="">{{ __('admin.workflow_ui_all') }}</option>
                    @foreach($taskStatuses as $value => $labels)
                        <option value="{{ $value }}" @selected(old('task_status', $conditions['task_status'] ?? '') === $value)>{{ $labels[app()->getLocale() === 'ar' ? 'ar' : 'en'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.workflow_ui_information_priority') }}</label>
                <select name="information_priority" class="form-select">
                    <option value="">{{ __('admin.workflow_ui_all') }}</option>
                    <option value="normal" @selected(old('information_priority', $conditions['information_priority'] ?? '') === 'normal')>{{ __('admin.workflow_ui_normal') }}</option>
                    <option value="important" @selected(old('information_priority', $conditions['information_priority'] ?? '') === 'important')>{{ __('admin.workflow_ui_important') }}</option>
                    <option value="urgent" @selected(old('information_priority', $conditions['information_priority'] ?? '') === 'urgent')>{{ __('admin.workflow_ui_urgent') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.workflow_ui_payment_status') }}</label>
                <select name="payment_status" class="form-select">
                    <option value="">{{ __('admin.workflow_ui_all') }}</option>
                    <option value="unpaid" @selected(old('payment_status', $conditions['payment_status'] ?? '') === 'unpaid')>{{ __('admin.workflow_ui_payment_status_unpaid') }}</option>
                    <option value="partially_paid" @selected(old('payment_status', $conditions['payment_status'] ?? '') === 'partially_paid')>{{ __('admin.workflow_ui_payment_status_partially_paid') }}</option>
                    <option value="fully_paid" @selected(old('payment_status', $conditions['payment_status'] ?? '') === 'fully_paid')>{{ __('admin.workflow_ui_payment_status_fully_paid') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.workflow_ui_min_overdue_days') }}</label>
                <input type="number" min="1" name="min_overdue_days" class="form-control" value="{{ old('min_overdue_days', $conditions['min_overdue_days'] ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.workflow_ui_inactive_days') }}</label>
                <input type="number" min="1" name="inactive_days" class="form-control" value="{{ old('inactive_days', $conditions['inactive_days'] ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.workflow_ui_min_amount') }}</label>
                <input type="number" step="0.01" min="0" name="amount_min" class="form-control" value="{{ old('amount_min', $conditions['amount_min'] ?? '') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                    <input type="hidden" name="assigned_user_empty" value="0">
                    <input class="form-check-input" type="checkbox" name="assigned_user_empty" value="1" id="assigned_user_empty" @checked(old('assigned_user_empty', $conditions['assigned_user_empty'] ?? false))>
                    <label class="form-check-label" for="assigned_user_empty">{{ __('admin.workflow_ui_only_if_unassigned') }}</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white"><strong>{{ __('admin.workflow_ui_actions') }}</strong></div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-12">
                <label class="form-label">{{ __('admin.workflow_ui_action_assign_user') }}</label>
                <select name="assign_user_id" class="form-select">
                    <option value="">{{ __('admin.workflow_ui_none') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string) old('assign_user_id', $actions['assign_user']['user_id'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white"><strong>{{ __('admin.workflow_ui_action_create_task') }}</strong></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                    <input type="hidden" name="create_task_enabled" value="0">
                    <input class="form-check-input" type="checkbox" name="create_task_enabled" value="1" id="create_task_enabled" @checked(old('create_task_enabled', ! empty($actions['create_task'])))>
                    <label class="form-check-label" for="create_task_enabled">{{ __('admin.workflow_ui_enable') }}</label>
                </div>
            </div>
            <div class="col-md-9">
                <label class="form-label">{{ __('admin.workflow_ui_title') }}</label>
                <input type="text" name="create_task_title" class="form-control" value="{{ old('create_task_title', $actions['create_task']['title'] ?? '') }}" placeholder="{{ __('admin.workflow_ui_task_title_placeholder') }}">
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('admin.workflow_ui_description') }}</label>
                <textarea name="create_task_description" rows="3" class="form-control">{{ old('create_task_description', $actions['create_task']['description'] ?? '') }}</textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.workflow_ui_priority') }}</label>
                <select name="create_task_priority" class="form-select">
                    @foreach($taskPriorities as $value => $labels)
                        <option value="{{ $value }}" @selected(old('create_task_priority', $actions['create_task']['priority'] ?? \App\Models\CrmTask::PRIORITY_MEDIUM) === $value)>{{ $labels[app()->getLocale() === 'ar' ? 'ar' : 'en'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.workflow_ui_category') }}</label>
                <select name="create_task_category" class="form-select">
                    @foreach($taskCategories as $value => $labels)
                        <option value="{{ $value }}" @selected(old('create_task_category', $actions['create_task']['category'] ?? \App\Models\CrmTask::CATEGORY_INTERNAL) === $value)>{{ $labels[app()->getLocale() === 'ar' ? 'ar' : 'en'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.workflow_ui_due_in_days') }}</label>
                <input type="number" min="0" name="create_task_due_in_days" class="form-control" value="{{ old('create_task_due_in_days', $actions['create_task']['due_in_days'] ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.workflow_ui_assigned_user') }}</label>
                <select name="create_task_assign_to" class="form-select">
                    @foreach($taskAssigneeOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('create_task_assign_to', $actions['create_task']['assign_to'] ?? 'linked_owner') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.workflow_ui_specific_user_optional') }}</label>
                <select name="create_task_assigned_user_id" class="form-select">
                    <option value="">{{ __('admin.workflow_ui_none') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string) old('create_task_assigned_user_id', $actions['create_task']['assigned_user_id'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white"><strong>{{ __('admin.workflow_ui_action_send_notification') }}</strong></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                    <input type="hidden" name="send_notification_enabled" value="0">
                    <input class="form-check-input" type="checkbox" name="send_notification_enabled" value="1" id="send_notification_enabled" @checked(old('send_notification_enabled', ! empty($actions['send_notification'])))>
                    <label class="form-check-label" for="send_notification_enabled">{{ __('admin.workflow_ui_enable') }}</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.workflow_ui_notification_recipient') }}</label>
                <select name="notification_recipient_mode" class="form-select">
                    @foreach($notificationRecipientOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('notification_recipient_mode', $actions['send_notification']['recipient_mode'] ?? 'linked_owner') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">{{ __('admin.workflow_ui_specific_user_optional') }}</label>
                <select name="notification_user_id" class="form-select">
                    <option value="">{{ __('admin.workflow_ui_none') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string) old('notification_user_id', $actions['send_notification']['user_id'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.workflow_ui_priority') }}</label>
                <select name="notification_severity" class="form-select">
                    <option value="info" @selected(old('notification_severity', $actions['send_notification']['severity'] ?? 'info') === 'info')>{{ __('admin.workflow_ui_info') }}</option>
                    <option value="success" @selected(old('notification_severity', $actions['send_notification']['severity'] ?? 'info') === 'success')>{{ __('admin.workflow_ui_success') }}</option>
                    <option value="warning" @selected(old('notification_severity', $actions['send_notification']['severity'] ?? 'info') === 'warning')>{{ __('admin.workflow_ui_warning') }}</option>
                    <option value="danger" @selected(old('notification_severity', $actions['send_notification']['severity'] ?? 'info') === 'danger')>{{ __('admin.workflow_ui_danger') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.workflow_ui_title_ar') }}</label>
                <input type="text" name="notification_title_ar" class="form-control" value="{{ old('notification_title_ar', $actions['send_notification']['title_ar'] ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.workflow_ui_title_en') }}</label>
                <input type="text" name="notification_title_en" class="form-control" value="{{ old('notification_title_en', $actions['send_notification']['title_en'] ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.workflow_ui_message_ar') }}</label>
                <textarea name="notification_message_ar" rows="3" class="form-control">{{ old('notification_message_ar', $actions['send_notification']['message_ar'] ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.workflow_ui_message_en') }}</label>
                <textarea name="notification_message_en" rows="3" class="form-control">{{ old('notification_message_en', $actions['send_notification']['message_en'] ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button class="btn btn-primary">{{ $formMethod === 'POST' ? __('admin.workflow_ui_save') : __('admin.workflow_ui_update') }}</button>
    <a href="{{ route('admin.workflow-automations.index') }}" class="btn btn-outline-secondary">{{ __('admin.workflow_ui_cancel') }}</a>
</div>
