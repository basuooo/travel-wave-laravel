<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.title') }}</label>
        <input name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $task->title) }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('admin.crm_task_type') }}</label>
        <select name="task_type" class="form-select @error('task_type') is-invalid @enderror">
            @foreach($typeOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('task_type', $task->task_type) === $value)>{{ $label[app()->getLocale() === 'ar' ? 'ar' : 'en'] }}</option>
            @endforeach
        </select>
        @error('task_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">التصنيف</label>
        <select name="category" class="form-select @error('category') is-invalid @enderror">
            <option value="">-</option>
            @foreach($categoryOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $task->category) === $value)>{{ $label[app()->getLocale() === 'ar' ? 'ar' : 'en'] }}</option>
            @endforeach
        </select>
        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('admin.status') }}</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror">
            @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $task->status) === $value)>{{ $label[app()->getLocale() === 'ar' ? 'ar' : 'en'] }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('admin.assigned_to') }}</label>
        <select name="assigned_user_id" class="form-select @error('assigned_user_id') is-invalid @enderror">
            <option value="">-</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((int) old('assigned_user_id', $task->assigned_user_id) === (int) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        @error('assigned_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('admin.priority') }}</label>
        <select name="priority" class="form-select @error('priority') is-invalid @enderror">
            @foreach($priorityOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('priority', $task->priority) === $value)>{{ $label[app()->getLocale() === 'ar' ? 'ar' : 'en'] }}</option>
            @endforeach
        </select>
        @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('admin.crm_task_due_date') }}</label>
        <input type="datetime-local" name="due_at" class="form-control @error('due_at') is-invalid @enderror" value="{{ old('due_at', optional($task->due_at)->format('Y-m-d\\TH:i')) }}">
        @error('due_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label">{{ __('admin.crm_related_lead') }}</label>
        <select name="inquiry_id" class="form-select @error('inquiry_id') is-invalid @enderror">
            <option value="">{{ __('admin.crm_task_unlinked') }}</option>
            @foreach($leads as $leadOption)
                <option value="{{ $leadOption->id }}" @selected((int) old('inquiry_id', $task->inquiry_id) === (int) $leadOption->id)>{{ $leadOption->full_name }}{{ $leadOption->phone ? ' - ' . $leadOption->phone : '' }}</option>
            @endforeach
        </select>
        @error('inquiry_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.notes') }}</label>
        <input name="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes', $task->notes) }}">
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('admin.description') }}</label>
        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $task->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('admin.crm_task_close_note') }}</label>
        <textarea name="closed_note" rows="2" class="form-control @error('closed_note') is-invalid @enderror">{{ old('closed_note', $task->closed_note) }}</textarea>
        @error('closed_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 d-flex gap-2 justify-content-end">
        @if(!empty($lead))
            <input type="hidden" name="return_to_lead" value="1">
        @else
            <input type="hidden" name="redirect_to" value="{{ url()->previous() }}">
        @endif
        <a href="{{ !empty($lead) ? route('admin.crm.leads.show', $lead) : route('admin.crm.tasks.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
        <button class="btn btn-primary">{{ $submitLabel ?? __('admin.save') }}</button>
    </div>
</div>
