@extends('layouts.admin')

@section('title', $isEdit ? __('admin.edit_user') : __('admin.create_user'))
@section('page_title', $isEdit ? __('admin.edit_user') : __('admin.create_user'))
@section('page_description', __('admin.users_form_desc'))

@section('content')
    <form method="post" enctype="multipart/form-data" action="{{ $isEdit ? route('admin.users.update', $item) : route('admin.users.store') }}">
        @csrf
        @if($isEdit)
            @method('put')
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h2 class="h5 mb-3">{{ __('admin.user_information') }}</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('admin.full_name') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('admin.email') }}</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $item->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('admin.phone') }}</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $item->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('admin.preferred_language') }}</label>
                                <select name="preferred_language" class="form-select">
                                    <option value="ar" @selected(old('preferred_language', $item->preferred_language) === 'ar')>{{ __('admin.arabic') }}</option>
                                    <option value="en" @selected(old('preferred_language', $item->preferred_language) === 'en')>{{ __('admin.english') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('admin.password') }}</label>
                                <input type="password" name="password" class="form-control" {{ $isEdit ? '' : 'required' }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('admin.password_confirmation') }}</label>
                                <input type="password" name="password_confirmation" class="form-control" {{ $isEdit ? '' : 'required' }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('admin.profile_image') }}</label>
                                <input type="file" name="profile_image" class="form-control">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="user-active" @checked(old('is_active', $item->is_active))>
                                    <label class="form-check-label" for="user-active">{{ __('admin.active_user') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h5 mb-3">{{ __('admin.permission_overrides') }}</h2>
                        @foreach($permissionGroups as $module => $permissions)
                            <div class="border rounded-4 p-3 mb-3">
                                <h3 class="h6 mb-3 text-capitalize">{{ str_replace('_', ' ', $module) }}</h3>
                                <div class="row g-3">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-6">
                                            <div class="border rounded-3 p-3 h-100">
                                                <div class="fw-semibold">{{ $permission->name }}</div>
                                                <div class="text-muted small mb-2">{{ $permission->slug }}</div>
                                                @if($permission->description)
                                                    <div class="small mb-3">{{ $permission->description }}</div>
                                                @endif
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="allowed_permissions[]" value="{{ $permission->id }}" id="allow-{{ $permission->id }}" @checked(in_array($permission->id, old('allowed_permissions', $allowedPermissionIds), true))>
                                                        <label class="form-check-label" for="allow-{{ $permission->id }}">{{ __('admin.allow') }}</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="denied_permissions[]" value="{{ $permission->id }}" id="deny-{{ $permission->id }}" @checked(in_array($permission->id, old('denied_permissions', $deniedPermissionIds), true))>
                                                        <label class="form-check-label" for="deny-{{ $permission->id }}">{{ __('admin.deny') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h2 class="h5 mb-3">{{ __('admin.assign_roles') }}</h2>
                        @foreach($roles as $role)
                            <label class="d-flex align-items-start gap-2 border rounded-3 p-3 mb-2">
                                <input class="form-check-input mt-1" type="checkbox" name="roles[]" value="{{ $role->id }}" @checked(in_array($role->id, old('roles', $selectedRoleIds), true))>
                                <span>
                                    <span class="fw-semibold d-block">{{ $role->name }}</span>
                                    <span class="small text-muted d-block">{{ $role->description }}</span>
                                    <span class="small text-muted">{{ trans_choice('admin.permissions_count', $role->permissions_count, ['count' => $role->permissions_count]) }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body d-grid gap-2">
                        <button class="btn btn-primary">{{ $isEdit ? __('admin.update_user') : __('admin.create_user') }}</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
