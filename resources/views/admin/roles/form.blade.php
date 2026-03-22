@extends('layouts.admin')

@section('title', $isEdit ? __('admin.edit_role') : __('admin.create_role'))
@section('page_title', $isEdit ? __('admin.edit_role') : __('admin.create_role'))
@section('page_description', __('admin.roles_form_desc'))

@section('content')
    <form method="post" action="{{ $isEdit ? route('admin.roles.update', $item) : route('admin.roles.store') }}">
        @csrf
        @if($isEdit)
            @method('put')
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.role_name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.slug_key') }}</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug', $item->slug) }}" {{ $item->is_system ? 'readonly' : '' }}>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.description') }}</label>
                            <textarea name="description" class="form-control" rows="5">{{ old('description', $item->description) }}</textarea>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary">{{ $isEdit ? __('admin.update_role') : __('admin.create_role') }}</button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h5 mb-3">{{ __('admin.role_permissions') }}</h2>
                        @foreach($permissionGroups as $module => $permissions)
                            <div class="border rounded-4 p-3 mb-3">
                                <h3 class="h6 mb-3 text-capitalize">{{ str_replace('_', ' ', $module) }}</h3>
                                <div class="row g-3">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-6">
                                            <label class="d-flex gap-2 border rounded-3 p-3 h-100">
                                                <input class="form-check-input mt-1" type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, old('permissions', $selectedPermissionIds), true))>
                                                <span>
                                                    <span class="fw-semibold d-block">{{ $permission->name }}</span>
                                                    <span class="small text-muted d-block">{{ $permission->slug }}</span>
                                                    @if($permission->description)
                                                        <span class="small">{{ $permission->description }}</span>
                                                    @endif
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
