@extends('layouts.admin')

@section('page_title', __('admin.document_categories'))
@section('page_description', __('admin.document_categories_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-3">{{ __('admin.add_document_category') }}</h2>
    <form method="POST" action="{{ route('admin.documents.categories.store') }}" class="row g-3 align-items-end">
        @csrf
        <div class="col-md-4"><label class="form-label">{{ __('admin.name_ar') }}</label><input class="form-control" name="name_ar" required></div>
        <div class="col-md-4"><label class="form-label">{{ __('admin.name_en') }}</label><input class="form-control" name="name_en"></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.sort_order') }}</label><input type="number" min="0" class="form-control" name="sort_order"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary">{{ __('admin.create') }}</button></div>
    </form>
</div>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.document_category') }}</th>
                    <th>{{ __('admin.documents') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.sort_order') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $category->localizedName() }}</div>
                            <div class="small text-muted">{{ $category->slug }}</div>
                        </td>
                        <td>{{ $category->documents_count }}</td>
                        <td><span class="badge {{ $category->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $category->is_active ? __('admin.active') : __('admin.inactive') }}</span></td>
                        <td>{{ $category->sort_order }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#category-edit-{{ $category->id }}">{{ __('admin.edit') }}</button>
                            <form method="POST" action="{{ route('admin.documents.categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    <tr class="collapse" id="category-edit-{{ $category->id }}">
                        <td colspan="5">
                            <form method="POST" action="{{ route('admin.documents.categories.update', $category) }}" class="row g-3 align-items-end">
                                @csrf
                                @method('PUT')
                                <div class="col-md-4"><label class="form-label">{{ __('admin.name_ar') }}</label><input class="form-control" name="name_ar" value="{{ $category->name_ar }}" required></div>
                                <div class="col-md-3"><label class="form-label">{{ __('admin.name_en') }}</label><input class="form-control" name="name_en" value="{{ $category->name_en }}"></div>
                                <div class="col-md-2"><label class="form-label">{{ __('admin.sort_order') }}</label><input type="number" min="0" class="form-control" name="sort_order" value="{{ $category->sort_order }}"></div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="category_active_{{ $category->id }}" name="is_active" value="1" @checked($category->is_active)>
                                        <label class="form-check-label" for="category_active_{{ $category->id }}">{{ __('admin.active') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-1 d-grid"><button class="btn btn-primary">{{ __('admin.update') }}</button></div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
