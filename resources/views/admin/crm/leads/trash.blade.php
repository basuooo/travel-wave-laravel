@extends('layouts.admin')

@section('page_title', __('admin.crm_deleted_leads'))
@section('page_description', __('admin.crm_deleted_leads_desc'))

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form class="card admin-card p-4 mb-4" method="GET" action="{{ route('admin.crm.leads.trash') }}">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.search') }}</label>
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="ابحث بالاسم، الموبايل...">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select class="form-select" name="crm_status_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" @selected((string) request('crm_status_id') === (string) $status->id)>{{ $status->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.source') }}</label>
            <select class="form-select" name="crm_source_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @foreach($sources as $source)
                    <option value="{{ $source->id }}" @selected((string) request('crm_source_id') === (string) $source->id)>{{ $source->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.assigned_to') }}</label>
            <select class="form-select" name="assigned_user_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @if($canViewAllLeads)
                    <option value="unassigned" @selected(request('assigned_user_id') === 'unassigned')>{{ __('admin.crm_unassigned') }}</option>
                @endif
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) request('assigned_user_id') === (string) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> {{ __('admin.search') }}</button>
        </div>
    </div>
</form>

<div class="card admin-card p-4">
    <!-- Bulk Operations Toolbar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3 p-3 bg-light border rounded-3">
        <div class="d-flex align-items-center gap-3">
            <div class="form-check m-0">
                <input type="checkbox" id="selectAllHeader" class="form-check-input">
                <label for="selectAllHeader" class="form-check-label fw-bold text-dark cursor-pointer">
                    تحديد الكل بهذه الصفحة
                </label>
            </div>
            <span id="selectedCountBadge" class="badge bg-primary fs-7" style="display: none;">محدد (0)</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" id="bulkRestoreBtn" class="btn btn-outline-success btn-sm fw-bold disabled" disabled>
                <i class="bi bi-arrow-counterclockwise me-1"></i> استعادة العملاء المحددين
            </button>
            <button type="button" id="bulkForceDeleteBtn" class="btn btn-danger btn-sm fw-bold disabled" disabled>
                <i class="bi bi-trash-fill me-1"></i> حذف نهائي للمحدد
            </button>
        </div>
    </div>

    <!-- Main Table Form -->
    <form id="bulkTrashForm" method="POST" action="">
        @csrf
        <input type="hidden" name="_method" id="bulkFormMethod" value="POST">

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAllCheckboxInHeader" class="form-check-input">
                        </th>
                        <th>{{ __('admin.full_name') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th>{{ __('admin.deleted_at') }}</th>
                        <th>{{ __('admin.deleted_by') }}</th>
                        <th class="text-end">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="trash-lead-checkbox form-check-input">
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->full_name }}</div>
                                <div class="text-muted small">{{ $item->phone ?: '—' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    {{ $item->localizedStatus() }}
                                </span>
                            </td>
                            <td>{{ optional($item->deleted_at)->format('Y-m-d H:i') ?: '—' }}</td>
                            <td>{{ $item->deletedBy?->name ?: '—' }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="restoreSingleLead('{{ route('admin.crm.leads.restore', $item->id) }}')">
                                        {{ __('admin.restore') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSingleLead('{{ route('admin.crm.leads.force-destroy', $item->id) }}')">
                                        {{ __('admin.delete_permanently') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-trash display-6 d-block mb-2"></i>
                                {{ __('admin.no_search_results') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-3">
        {{ $items->links() }}
    </div>
</div>

<!-- Helper forms for single action buttons -->
<form id="singleActionForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="_method" id="singleActionMethod" value="POST">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllHeader = document.getElementById('selectAllHeader');
        const selectAllTable = document.getElementById('selectAllCheckboxInHeader');
        const checkboxes = document.querySelectorAll('.trash-lead-checkbox');
        const selectedCountBadge = document.getElementById('selectedCountBadge');
        const bulkForceDeleteBtn = document.getElementById('bulkForceDeleteBtn');
        const bulkRestoreBtn = document.getElementById('bulkRestoreBtn');
        const bulkForm = document.getElementById('bulkTrashForm');
        const methodInput = document.getElementById('bulkFormMethod');

        function updateSelectionState() {
            const checked = document.querySelectorAll('.trash-lead-checkbox:checked');
            const count = checked.length;

            if (selectedCountBadge) {
                selectedCountBadge.textContent = 'محدد (' + count + ')';
                selectedCountBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }

            if (bulkForceDeleteBtn) {
                bulkForceDeleteBtn.classList.toggle('disabled', count === 0);
                bulkForceDeleteBtn.disabled = count === 0;
            }

            if (bulkRestoreBtn) {
                bulkRestoreBtn.classList.toggle('disabled', count === 0);
                bulkRestoreBtn.disabled = count === 0;
            }

            const allChecked = checkboxes.length > 0 && count === checkboxes.length;
            if (selectAllHeader) selectAllHeader.checked = allChecked;
            if (selectAllTable) selectAllTable.checked = allChecked;
        }

        function toggleAll(checked) {
            checkboxes.forEach(cb => {
                cb.checked = checked;
            });
            updateSelectionState();
        }

        if (selectAllHeader) {
            selectAllHeader.addEventListener('change', function() {
                toggleAll(this.checked);
            });
        }

        if (selectAllTable) {
            selectAllTable.addEventListener('change', function() {
                toggleAll(this.checked);
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectionState);
        });

        if (bulkForceDeleteBtn) {
            bulkForceDeleteBtn.addEventListener('click', function(e) {
                if (this.disabled) return;
                const count = document.querySelectorAll('.trash-lead-checkbox:checked').length;
                if (confirm('هل أنت متأكد من حذف (' + count + ') عميل نهائياً من قاعدة البيانات؟ لا يمكن التراجع عن هذه الخطوة كلياً!')) {
                    bulkForm.action = '{{ route("admin.crm.leads.trash.bulk-force-destroy") }}';
                    methodInput.value = 'DELETE';
                    bulkForm.submit();
                }
            });
        }

        if (bulkRestoreBtn) {
            bulkRestoreBtn.addEventListener('click', function(e) {
                if (this.disabled) return;
                const count = document.querySelectorAll('.trash-lead-checkbox:checked').length;
                if (confirm('هل أنت متأكد من استعادة (' + count + ') عميل محذوف إلى قائمة العملاء بالسيستم؟')) {
                    bulkForm.action = '{{ route("admin.crm.leads.trash.bulk-restore") }}';
                    methodInput.value = 'POST';
                    bulkForm.submit();
                }
            });
        }
    });

    function restoreSingleLead(url) {
        if (confirm('هل ترغب في استعادة هذا العميل؟')) {
            const form = document.getElementById('singleActionForm');
            const method = document.getElementById('singleActionMethod');
            form.action = url;
            method.value = 'POST';
            form.submit();
        }
    }

    function deleteSingleLead(url) {
        if (confirm('{{ __("admin.confirm_delete") }}')) {
            const form = document.getElementById('singleActionForm');
            const method = document.getElementById('singleActionMethod');
            form.action = url;
            method.value = 'DELETE';
            form.submit();
        }
    }
</script>
@endsection
