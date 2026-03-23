@extends('layouts.admin')

@section('page_title', __('admin.crm_service_types'))
@section('page_description', __('admin.crm_service_types_desc'))

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.create') }} {{ __('admin.crm_service_type') }}</h2>
            <form method="post" action="{{ route('admin.crm.service-types.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6"><label class="form-label">EN</label><input class="form-control" name="name_en"></div>
                <div class="col-md-6"><label class="form-label">AR</label><input class="form-control text-end" dir="rtl" name="name_ar"></div>
                <div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.sort_order') }}</label><input type="number" class="form-control" name="sort_order" value="0"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.crm_destination_label_en') }}</label><input class="form-control" name="destination_label_en"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.crm_destination_label_ar') }}</label><input class="form-control text-end" dir="rtl" name="destination_label_ar"></div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requires_subtype" value="1" id="requires-subtype">
                        <label class="form-check-label" for="requires-subtype">{{ __('admin.crm_requires_subtype') }}</label>
                    </div>
                </div>
                <div class="col-12"><label class="form-label">{{ __('admin.notes') }}</label><textarea class="form-control" name="notes" rows="2"></textarea></div>
                <div class="col-12"><button class="btn btn-primary">{{ __('admin.create') }}</button></div>
            </form>
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.create') }} {{ __('admin.crm_service_subtype') }}</h2>
            <form method="post" action="{{ route('admin.crm.service-subtypes.store') }}" class="row g-3">
                @csrf
                <div class="col-12">
                    <label class="form-label">{{ __('admin.crm_service_type') }}</label>
                    <select class="form-select" name="crm_service_type_id">
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">{{ $type->localizedName() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">EN</label><input class="form-control" name="name_en"></div>
                <div class="col-md-6"><label class="form-label">AR</label><input class="form-control text-end" dir="rtl" name="name_ar"></div>
                <div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.sort_order') }}</label><input type="number" class="form-control" name="sort_order" value="0"></div>
                <div class="col-12"><label class="form-label">{{ __('admin.notes') }}</label><textarea class="form-control" name="notes" rows="2"></textarea></div>
                <div class="col-12"><button class="btn btn-primary">{{ __('admin.create') }}</button></div>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.crm_service_types') }}</h2>
            <div class="d-grid gap-2">
                @foreach($typeMap as $row)
                    <div class="d-flex justify-content-between align-items-center border rounded-4 px-3 py-2">
                        <span>{{ $row['type']->localizedName() }}</span>
                        <strong>{{ $row['count'] }}</strong>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card admin-card p-4">
            <div class="d-grid gap-4">
                @foreach($types as $type)
                    <div class="border rounded-4 p-3">
                        <form method="post" action="{{ route('admin.crm.service-types.update', $type) }}" class="row g-2 align-items-end mb-3">
                            @csrf @method('PUT')
                            <div class="col-md-4"><input class="form-control" name="name_en" value="{{ $type->name_en }}"></div>
                            <div class="col-md-4"><input class="form-control text-end" dir="rtl" name="name_ar" value="{{ $type->name_ar }}"></div>
                            <div class="col-md-2"><input type="number" class="form-control" name="sort_order" value="{{ $type->sort_order }}"></div>
                            <div class="col-md-2"><button class="btn btn-outline-secondary w-100">{{ __('admin.update') }}</button></div>
                            <div class="col-md-6"><input class="form-control" name="destination_label_en" value="{{ $type->destination_label_en }}" placeholder="{{ __('admin.crm_destination_label_en') }}"></div>
                            <div class="col-md-6"><input class="form-control text-end" dir="rtl" name="destination_label_ar" value="{{ $type->destination_label_ar }}" placeholder="{{ __('admin.crm_destination_label_ar') }}"></div>
                            <div class="col-md-8"><textarea class="form-control" name="notes" rows="2">{{ $type->notes }}</textarea></div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="hidden" name="requires_subtype" value="0">
                                    <input class="form-check-input" type="checkbox" name="requires_subtype" value="1" id="type-requires-{{ $type->id }}" @checked($type->requires_subtype)>
                                    <label class="form-check-label" for="type-requires-{{ $type->id }}">{{ __('admin.crm_requires_subtype') }}</label>
                                </div>
                                <div class="form-check">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="type-active-{{ $type->id }}" @checked($type->is_active)>
                                    <label class="form-check-label" for="type-active-{{ $type->id }}">{{ __('admin.active') }}</label>
                                </div>
                                <div class="small text-muted mt-2">{{ $type->slug }}</div>
                            </div>
                        </form>
                        <div class="d-flex justify-content-end mb-3">
                            <form method="post" action="{{ route('admin.crm.service-types.destroy', $type) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                            </form>
                        </div>

                        <div class="d-grid gap-3">
                            @foreach($type->subtypes as $subtype)
                                <div class="row g-2 align-items-end border rounded-4 p-3">
                                    <form method="post" action="{{ route('admin.crm.service-subtypes.update', $subtype) }}" class="row g-2 align-items-end">
                                        @csrf @method('PUT')
                                        <div class="col-md-4"><input class="form-control" name="name_en" value="{{ $subtype->name_en }}"></div>
                                        <div class="col-md-4"><input class="form-control text-end" dir="rtl" name="name_ar" value="{{ $subtype->name_ar }}"></div>
                                        <div class="col-md-2"><input type="number" class="form-control" name="sort_order" value="{{ $subtype->sort_order }}"></div>
                                        <div class="col-md-2"><button class="btn btn-outline-secondary w-100">{{ __('admin.update') }}</button></div>
                                        <div class="col-md-8"><textarea class="form-control" name="notes" rows="2">{{ $subtype->notes }}</textarea></div>
                                        <div class="col-md-4">
                                            <div class="form-check mt-2">
                                                <input type="hidden" name="is_active" value="0">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="subtype-active-{{ $subtype->id }}" @checked($subtype->is_active)>
                                                <label class="form-check-label" for="subtype-active-{{ $subtype->id }}">{{ __('admin.active') }}</label>
                                            </div>
                                            <div class="small text-muted mt-2">{{ $subtype->slug }}</div>
                                        </div>
                                    </form>
                                    <div class="col-12 d-flex justify-content-end">
                                        <form method="post" action="{{ route('admin.crm.service-subtypes.destroy', $subtype) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
