@extends('layouts.admin')

@section('page_title', __('admin.documents'))
@section('page_description', __('admin.documents_desc'))

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.total_documents') }}</div><div class="fs-4 fw-semibold">{{ $summary['total'] }}</div></div></div>
    <div class="col-md-4"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.documents_uploaded_this_week') }}</div><div class="fs-4 fw-semibold text-primary">{{ $summary['uploaded_this_week'] }}</div></div></div>
    <div class="col-md-4"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.documents_uploaded_today') }}</div><div class="fs-4 fw-semibold text-success">{{ $summary['recent'] }}</div></div></div>
</div>

<form method="GET" class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3"><label class="form-label">{{ __('admin.search') }}</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.document_search_placeholder') }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.document_category') }}</label><select class="form-select" name="crm_document_category_id"><option value="">{{ __('admin.all') }}</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((int) request('crm_document_category_id') === (int) $category->id)>{{ $category->localizedName() }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.related_entity') }}</label><select class="form-select" name="documentable_type"><option value="">{{ __('admin.all') }}</option>@foreach($entityTypes as $entityType)<option value="{{ $entityType }}" @selected(request('documentable_type') === $entityType)>{{ \App\Support\CrmDocumentService::localizedEntityLabel($entityType) }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.uploaded_by') }}</label><select class="form-select" name="uploaded_by"><option value="">{{ __('admin.all') }}</option>@foreach($uploaders as $uploader)<option value="{{ $uploader->id }}" @selected((int) request('uploaded_by') === (int) $uploader->id)>{{ $uploader->name }}</option>@endforeach</select></div>
        <div class="col-md-1"><label class="form-label">{{ __('admin.from') }}</label><input type="date" class="form-control" name="from" value="{{ request('from') }}"></div>
        <div class="col-md-1"><label class="form-label">{{ __('admin.to') }}</label><input type="date" class="form-control" name="to" value="{{ request('to') }}"></div>
        <div class="col-md-1 d-grid"><button class="btn btn-primary">{{ __('admin.filter') }}</button></div>
        <div class="col-12 d-flex gap-2">
            <a href="{{ route('admin.documents.create') }}" class="btn btn-success">{{ __('admin.upload_document') }}</a>
            @if(auth()->user()?->hasPermission('documents.categories.manage'))
                <a href="{{ route('admin.documents.categories.index') }}" class="btn btn-outline-dark">{{ __('admin.document_categories') }}</a>
            @endif
            <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
        </div>
    </div>
</form>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.document_title') }}</th>
                    <th>{{ __('admin.document_category') }}</th>
                    <th>{{ __('admin.related_entity') }}</th>
                    <th>{{ __('admin.related_record') }}</th>
                    <th>{{ __('admin.uploaded_by') }}</th>
                    <th>{{ __('admin.uploaded_at') }}</th>
                    <th>{{ __('admin.file_size') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->title }}</div>
                            <div class="small text-muted">{{ $item->original_file_name }}</div>
                        </td>
                        <td>{{ $item->category?->localizedName() ?: '-' }}</td>
                        <td>{{ $item->localizedEntityType() }}</td>
                        <td>{{ $item->linkedRecordLabel() }}</td>
                        <td>{{ $item->uploader?->name ?: '-' }}</td>
                        <td>{{ optional($item->uploaded_at)->format('Y-m-d H:i') ?: '-' }}</td>
                        <td>{{ $item->formattedFileSize() }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.documents.show', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                                <a href="{{ route('admin.documents.download', $item) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.download') }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">{{ __('admin.no_documents') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
