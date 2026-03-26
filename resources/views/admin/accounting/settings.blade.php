@extends('layouts.admin')

@section('page_title', __('admin.accounting_settings'))
@section('page_description', __('admin.accounting_settings_desc'))

@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.accounting_customer_expense_categories') }}</h2>
            <form method="POST" action="{{ route('admin.accounting.settings.customer-categories.store') }}" class="row g-3 mb-4">
                @csrf
                <div class="col-md-5"><input class="form-control" name="name_ar" placeholder="{{ __('admin.name_ar') }}" required></div>
                <div class="col-md-5"><input class="form-control" name="name_en" placeholder="{{ __('admin.name_en') }}"></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">{{ __('admin.add') }}</button></div>
            </form>
            @foreach($customerCategories as $category)
                <div class="border rounded-4 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                        <div class="fw-semibold">{{ $category->localizedName() }}</div>
                        <form method="POST" action="{{ route('admin.accounting.settings.customer-categories.destroy', $category) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                        </form>
                    </div>
                    <div class="small text-muted mb-3">{{ __('admin.subcategory') }}: {{ $category->subcategories->count() }}</div>
                    <form method="POST" action="{{ route('admin.accounting.settings.customer-subcategories.store') }}" class="row g-2">
                        @csrf
                        <input type="hidden" name="accounting_expense_category_id" value="{{ $category->id }}">
                        <div class="col-md-5"><input class="form-control" name="name_ar" placeholder="{{ __('admin.name_ar') }}" required></div>
                        <div class="col-md-5"><input class="form-control" name="name_en" placeholder="{{ __('admin.name_en') }}"></div>
                        <div class="col-md-2"><button class="btn btn-outline-primary w-100">{{ __('admin.add') }}</button></div>
                    </form>
                    @if($category->subcategories->isNotEmpty())
                        <ul class="mt-3 mb-0 small">
                            @foreach($category->subcategories as $subcategory)
                                <li class="d-flex justify-content-between align-items-center gap-2">
                                    <span>{{ $subcategory->localizedName() }}</span>
                                    <form method="POST" action="{{ route('admin.accounting.settings.customer-subcategories.destroy', $subcategory) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.accounting_general_expense_categories') }}</h2>
            <form method="POST" action="{{ route('admin.accounting.settings.general-categories.store') }}" class="row g-3 mb-4">
                @csrf
                <div class="col-md-5"><input class="form-control" name="name_ar" placeholder="{{ __('admin.name_ar') }}" required></div>
                <div class="col-md-5"><input class="form-control" name="name_en" placeholder="{{ __('admin.name_en') }}"></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">{{ __('admin.add') }}</button></div>
            </form>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.category') }}</th><th>{{ __('admin.actions') }}</th></tr></thead>
                    <tbody>
                        @forelse($generalCategories as $category)
                            <tr>
                                <td>{{ $category->localizedName() }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.accounting.settings.general-categories.destroy', $category) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
