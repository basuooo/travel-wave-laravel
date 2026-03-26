@extends('layouts.admin')

@section('page_title', $document->title)
@section('page_description', __('admin.document_details'))

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card admin-card p-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1">{{ $document->title }}</h2>
                    <div class="text-muted">{{ $document->original_file_name }}</div>
                </div>
                <span class="badge text-bg-light">{{ $document->category?->localizedName() ?: '-' }}</span>
            </div>
            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('admin.document_category') }}</dt><dd class="col-sm-8">{{ $document->category?->localizedName() ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.related_entity') }}</dt><dd class="col-sm-8">{{ $document->localizedEntityType() }}</dd>
                <dt class="col-sm-4">{{ __('admin.related_record') }}</dt><dd class="col-sm-8">{{ $documentableLabel }}</dd>
                <dt class="col-sm-4">{{ __('admin.uploaded_by') }}</dt><dd class="col-sm-8">{{ $document->uploader?->name ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.uploaded_at') }}</dt><dd class="col-sm-8">{{ optional($document->uploaded_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.file_type') }}</dt><dd class="col-sm-8">{{ $document->mime_type ?: ($document->extension ?: '-') }}</dd>
                <dt class="col-sm-4">{{ __('admin.file_size') }}</dt><dd class="col-sm-8">{{ $document->formattedFileSize() }}</dd>
                <dt class="col-sm-4">{{ __('admin.issue_date') }}</dt><dd class="col-sm-8">{{ optional($document->issue_date)->format('Y-m-d') ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.expiry_date') }}</dt><dd class="col-sm-8">{{ optional($document->expiry_date)->format('Y-m-d') ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.notes') }}</dt><dd class="col-sm-8">{{ $document->note ?: '-' }}</dd>
            </dl>
            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('admin.documents.preview', $document) }}" class="btn btn-primary" target="_blank" rel="noopener">{{ __('admin.view') }}</a>
                <a href="{{ route('admin.documents.download', $document) }}" class="btn btn-outline-secondary">{{ __('admin.download') }}</a>
                @if(auth()->user()?->hasPermission('documents.manage'))
                    <form method="POST" action="{{ route('admin.documents.destroy', $document) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger">{{ __('admin.delete') }}</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.quick_actions') }}</h2>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">{{ __('admin.documents') }}</a>
                @if($document->documentable instanceof \App\Models\Inquiry)
                    <a href="{{ route('admin.crm.leads.show', $document->documentable) }}" class="btn btn-outline-secondary">{{ __('admin.source_lead') }}</a>
                @elseif($document->documentable instanceof \App\Models\CrmCustomer)
                    <a href="{{ route('admin.crm.customers.show', $document->documentable) }}" class="btn btn-outline-secondary">{{ __('admin.customer') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
