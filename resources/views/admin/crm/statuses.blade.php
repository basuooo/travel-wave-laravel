@extends('layouts.admin')

@section('page_title', __('admin.crm_statuses'))
@section('page_description', __('admin.crm_statuses_desc'))

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.create') }} {{ __('admin.status') }}</h2>
            <form method="post" action="{{ route('admin.crm.statuses.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6"><label class="form-label">EN</label><input class="form-control" name="name_en"></div>
                <div class="col-md-6"><label class="form-label">AR</label><input class="form-control text-end" dir="rtl" name="name_ar"></div>
                <div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug"></div>
                <div class="col-md-3"><label class="form-label">{{ __('admin.sort_order') }}</label><input type="number" class="form-control" name="sort_order" value="0"></div>
                <div class="col-md-3"><label class="form-label">{{ __('admin.color') }}</label><input class="form-control" name="color" placeholder="warning"></div>
                <div class="col-12"><button class="btn btn-primary">{{ __('admin.create') }}</button></div>
            </form>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.crm_status_map') }}</h2>
            <div class="d-grid gap-2">
                @foreach($statusMap as $row)
                    <div class="d-flex justify-content-between align-items-center border rounded-4 px-3 py-2">
                        <span>{{ $row['status']->localizedName() }}</span>
                        <strong>{{ $row['count'] }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.crm_statuses') }}</h2>
            <div class="d-grid gap-3">
                @foreach($statuses as $status)
                    <form method="post" action="{{ route('admin.crm.statuses.update', $status) }}" class="row g-2 align-items-end border rounded-4 p-3">
                        @csrf @method('PUT')
                        <div class="col-md-4"><input class="form-control" name="name_en" value="{{ $status->name_en }}"></div>
                        <div class="col-md-4"><input class="form-control text-end" dir="rtl" name="name_ar" value="{{ $status->name_ar }}"></div>
                        <div class="col-md-2"><input type="number" class="form-control" name="sort_order" value="{{ $status->sort_order }}"></div>
                        <div class="col-md-2"><button class="btn btn-outline-secondary w-100">{{ __('admin.update') }}</button></div>
                        <div class="col-md-4"><input class="form-control" name="color" value="{{ $status->color }}"></div>
                        <div class="col-md-4">
                            <div class="form-check mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="status-{{ $status->id }}" @checked($status->is_active)>
                                <label class="form-check-label" for="status-{{ $status->id }}">{{ __('admin.active') }}</label>
                            </div>
                        </div>
                        <div class="col-md-4 text-muted small">{{ $status->slug }}</div>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
