@extends('layouts.admin')

@section('page_title', $isEdit ? __('admin.goals_targets_ui_edit_title') : __('admin.goals_targets_ui_create_title'))
@section('page_description', __('admin.goals_targets_ui_page_desc'))

@section('content')
<div class="card admin-card p-4">
    <form method="post" action="{{ $isEdit ? route('admin.goals-commissions.targets.update', $goal) : route('admin.goals-commissions.targets.store') }}" class="row g-3">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="col-md-4">
            <label class="form-label">{{ __('admin.goals_targets_ui_employee') }}</label>
            <select name="user_id" class="form-select" required>
                <option value="">{{ __('admin.goals_targets_ui_all') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((int) old('user_id', $goal->user_id) === (int) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.goals_targets_ui_goal_type') }}</label>
            <select name="target_type" class="form-select" required>
                @foreach($targetTypes as $value => $label)
                    <option value="{{ $value }}" @selected(old('target_type', $goal->target_type) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.goals_targets_ui_goal_value') }}</label>
            <input type="number" step="0.01" min="0.01" class="form-control" name="target_value" value="{{ old('target_value', $goal->target_value) }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">{{ __('admin.goals_targets_ui_period_type') }}</label>
            <select name="period_type" class="form-select" required>
                @foreach($periodTypes as $value => $label)
                    <option value="{{ $value }}" @selected(old('period_type', $goal->period_type) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.goals_targets_ui_from_date') }}</label>
            <input type="date" class="form-control" name="period_start" value="{{ old('period_start', optional($goal->period_start)->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.goals_targets_ui_to_date') }}</label>
            <input type="date" class="form-control" name="period_end" value="{{ old('period_end', optional($goal->period_end)->format('Y-m-d')) }}" required>
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('admin.goals_targets_ui_notes') }}</label>
            <textarea class="form-control" rows="4" name="note">{{ old('note', $goal->note) }}</textarea>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="goal-active" @checked(old('is_active', $goal->is_active))>
                <label class="form-check-label" for="goal-active">{{ __('admin.goals_targets_ui_active') }}</label>
            </div>
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">{{ $isEdit ? __('admin.goals_targets_ui_save_changes') : __('admin.goals_targets_ui_save') }}</button>
            <a href="{{ route('admin.goals-commissions.targets.index') }}" class="btn btn-outline-secondary">{{ __('admin.goals_targets_ui_cancel') }}</a>
        </div>
    </form>
</div>
@endsection
