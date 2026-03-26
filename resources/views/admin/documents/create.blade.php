@extends('layouts.admin')

@section('page_title', __('admin.upload_document'))
@section('page_description', __('admin.documents_upload_desc'))

@section('content')
<div class="card admin-card p-4">
    <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.related_entity') }}</label>
                <select class="form-select" name="documentable_type" data-document-type-select required>
                    <option value="">{{ __('admin.select_option') }}</option>
                    @foreach($entityTypes as $entityType)
                        <option value="{{ $entityType }}" @selected(old('documentable_type', $documentableType) === $entityType)>{{ \App\Support\CrmDocumentService::localizedEntityLabel($entityType) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5 d-none" data-documentable-group="inquiry">
                <label class="form-label">{{ __('admin.source_lead') }}</label>
                <select class="form-select" name="documentable_id_inquiry">
                    <option value="">{{ __('admin.select_lead') }}</option>
                    @foreach($leadOptions as $lead)
                        <option value="{{ $lead->id }}" @selected(old('documentable_type', $documentableType) === 'inquiry' && (int) old('documentable_id_inquiry', $selectedDocumentable?->id) === (int) $lead->id)>{{ $lead->full_name }}{{ $lead->phone ? ' - ' . $lead->phone : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5 d-none" data-documentable-group="customer">
                <label class="form-label">{{ __('admin.customer') }}</label>
                <select class="form-select" name="documentable_id_customer">
                    <option value="">{{ __('admin.select_customer') }}</option>
                    @foreach($customerOptions as $customer)
                        <option value="{{ $customer->id }}" @selected(old('documentable_type', $documentableType) === 'customer' && (int) old('documentable_id_customer', $selectedDocumentable?->id) === (int) $customer->id)>{{ ($customer->customer_code ? $customer->customer_code . ' - ' : '') . $customer->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="documentable_id" value="">
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.document_category') }}</label>
                <select class="form-select" name="crm_document_category_id" required>
                    <option value="">{{ __('admin.select_option') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('crm_document_category_id') === (int) $category->id)>{{ $category->localizedName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.document_title') }}</label>
                <input class="form-control" name="title" value="{{ old('title') }}" placeholder="{{ __('admin.document_title_placeholder') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.file') }}</label>
                <input type="file" class="form-control" name="file" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.issue_date') }}</label>
                <input type="date" class="form-control" name="issue_date" value="{{ old('issue_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.expiry_date') }}</label>
                <input type="date" class="form-control" name="expiry_date" value="{{ old('expiry_date') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1" @checked(old('is_required'))>
                    <label class="form-check-label" for="is_required">{{ __('admin.document_required') }}</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('admin.notes') }}</label>
                <textarea class="form-control" name="note" rows="4">{{ old('note') }}</textarea>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
            <button class="btn btn-primary">{{ __('admin.upload_document') }}</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.querySelector('[data-document-type-select]');
    const hiddenTarget = document.querySelector('input[name="documentable_id"]');
    const groups = document.querySelectorAll('[data-documentable-group]');

    const syncGroups = () => {
        const selectedType = typeSelect?.value || '';

        groups.forEach((group) => {
            const active = group.dataset.documentableGroup === selectedType;
            group.classList.toggle('d-none', !active);
            group.querySelector('select')?.toggleAttribute('required', active);
            if (active) {
                hiddenTarget.value = group.querySelector('select')?.value || '';
            }
        });
    };

    groups.forEach((group) => {
        group.querySelector('select')?.addEventListener('change', syncGroups);
    });

    typeSelect?.addEventListener('change', syncGroups);
    syncGroups();
});
</script>
@endsection
