@extends('layouts.admin')

@section('page_title', __('admin.crm_information_create'))
@section('page_description', __('admin.crm_information_create_desc'))

@section('content')
<div class="card admin-card p-4">
    <form method="POST" action="{{ route('admin.crm.information.store') }}" class="row g-4" data-information-create-form>
        @csrf
        <div class="col-md-8">
            <label class="form-label">{{ __('admin.crm_information_title') }}</label>
            <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.crm_information_priority') }}</label>
            <select name="priority" class="form-select">
                <option value="">{{ __('admin.none_option') }}</option>
                @foreach($priorityOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('priority', 'normal') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin.crm_information_category') }}</label>
            <select name="category" class="form-select">
                <option value="">{{ __('admin.none_option') }}</option>
                @foreach($categoryOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_information_date') }}</label>
            <input type="date" name="event_date" value="{{ old('event_date') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_information_expires_at') }}</label>
            <input type="date" name="expires_at" value="{{ old('expires_at') }}" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('admin.crm_information_content') }}</label>
            <textarea name="content" rows="8" class="form-control" required>{{ old('content') }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin.crm_information_audience_type') }}</label>
            <select name="audience_type" class="form-select" data-information-audience-select required>
                @foreach($audienceOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('audience_type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 d-none" data-information-selected-users-wrap>
            <label class="form-label">{{ __('admin.crm_information_selected_users') }}</label>
            <select name="selected_users[]" class="form-select" multiple size="8" data-information-selected-users>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(collect(old('selected_users', []))->contains($user->id))>{{ $user->name }} - {{ $user->email }}</option>
                @endforeach
            </select>
            <div class="form-text">{{ __('admin.crm_information_selected_users_help') }}</div>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', '1') === '1')>
                <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
            </div>
        </div>
        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ __('admin.create') }}</button>
            <a href="{{ route('admin.crm.information.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const audienceSelect = document.querySelector('[data-information-audience-select]');
    const selectedUsersWrap = document.querySelector('[data-information-selected-users-wrap]');
    const selectedUsersInput = document.querySelector('[data-information-selected-users]');

    if (! audienceSelect || ! selectedUsersWrap || ! selectedUsersInput) {
        return;
    }

    const toggleSelectedUsers = () => {
        const isSelectedUsers = audienceSelect.value === 'selected_users';
        selectedUsersWrap.classList.toggle('d-none', !isSelectedUsers);
        selectedUsersInput.required = isSelectedUsers;
    };

    audienceSelect.addEventListener('change', toggleSelectedUsers);
    toggleSelectedUsers();
});
</script>
@endsection
