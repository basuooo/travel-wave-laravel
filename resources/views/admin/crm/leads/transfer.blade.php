@extends('layouts.admin')

@section('page_title', __('admin.crm_import_export'))
@section('page_description', __('admin.crm_import_export_desc'))

@section('content')
<div class="row g-4">
    @if(auth()->user()?->hasPermission('leads.create'))
    <div class="col-lg-7">
        <div class="card admin-card p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1">{{ __('admin.crm_import_leads') }}</h2>
                    <p class="text-muted mb-0">{{ __('admin.crm_import_leads_desc') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.crm.leads.import.template', ['format' => 'csv']) }}">{{ __('admin.crm_download_csv_template') }}</a>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.crm.leads.import.template', ['format' => 'xlsx']) }}">{{ __('admin.crm_download_excel_template') }}</a>
                </div>
            </div>

            <form method="post" action="{{ route('admin.crm.leads.import.preview') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                @php
                    $selectedDuplicateMode = old('duplicate_mode', $preview['duplicate_mode'] ?? 'none');
                    $selectedDuplicateDetector = old('duplicate_detector', $preview['duplicate_detector'] ?? 'phone');
                @endphp
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.crm_import_file') }}</label>
                    <input type="file" class="form-control" name="import_file" accept=".csv,.xlsx,.xls">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.crm_google_sheet_url') }}</label>
                    <input type="url" class="form-control" name="google_sheet_url" value="{{ old('google_sheet_url') }}" placeholder="https://docs.google.com/spreadsheets/...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('admin.crm_duplicate_handling') }}</label>
                    <select class="form-select" name="duplicate_mode" data-crm-duplicate-mode>
                        @foreach($duplicateHandlingOptions as $modeKey => $mode)
                            <option value="{{ $modeKey }}" @selected($selectedDuplicateMode === $modeKey)>{{ $mode['label_ar'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4" data-crm-duplicate-detector-wrapper @if($selectedDuplicateMode === 'none') style="display:none;" @endif>
                    <label class="form-label">{{ __('admin.crm_duplicate_detector') }}</label>
                    <select class="form-select" name="duplicate_detector" data-crm-duplicate-detector @if($selectedDuplicateMode === 'none') disabled @endif>
                        @foreach($duplicateDetectorOptions as $detectorKey => $detector)
                            <option value="{{ $detectorKey }}" @selected($selectedDuplicateDetector === $detectorKey)>{{ $detector['label_ar'] }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">{{ __('admin.crm_duplicate_detector_help') }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('admin.crm_google_sheet_help_title') }}</label>
                    <div class="text-muted small pt-2">{{ __('admin.crm_google_sheet_help') }} {{ __('admin.crm_duplicate_export_help') }}</div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">{{ __('admin.crm_generate_import_preview') }}</button>
                </div>
            </form>
        </div>

        @if(!empty($importResult))
            <div class="card admin-card p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <h2 class="h5 mb-1">{{ __('admin.crm_import_result_summary') }}</h2>
                        <p class="text-muted mb-0">{{ __('admin.crm_import_result_summary_desc') }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if(!empty($importResult['has_duplicates']))
                            <a class="btn btn-outline-warning btn-sm" href="{{ route('admin.crm.leads.import.report', ['report' => 'duplicates', 'format' => 'xlsx']) }}">{{ __('admin.crm_download_duplicate_file') }}</a>
                        @endif
                        @if(!empty($importResult['has_merged_rows']))
                            <a class="btn btn-outline-info btn-sm" href="{{ route('admin.crm.leads.import.report', ['report' => 'merged', 'format' => 'xlsx']) }}">{{ __('admin.crm_download_merged_file') }}</a>
                        @endif
                        @if(!empty($importResult['has_invalid_rows']))
                            <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.crm.leads.import.report', ['report' => 'invalid', 'format' => 'xlsx']) }}">{{ __('admin.crm_download_invalid_file') }}</a>
                        @endif
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-3"><div class="admin-stat-card"><div class="admin-stat-card__label">{{ __('admin.total') }}</div><div class="admin-stat-card__value">{{ $importResult['summary']['total_rows'] ?? 0 }}</div></div></div>
                    <div class="col-md-3"><div class="admin-stat-card admin-stat-card--success"><div class="admin-stat-card__label">{{ __('admin.crm_imported_rows') }}</div><div class="admin-stat-card__value">{{ $importResult['summary']['imported_rows'] ?? 0 }}</div></div></div>
                    <div class="col-md-3"><div class="admin-stat-card admin-stat-card--warning"><div class="admin-stat-card__label">{{ __('admin.crm_duplicate_rows') }}</div><div class="admin-stat-card__value">{{ $importResult['summary']['duplicate_rows'] ?? 0 }}</div></div></div>
                    <div class="col-md-3"><div class="admin-stat-card admin-stat-card--info"><div class="admin-stat-card__label">{{ __('admin.crm_merged_rows') }}</div><div class="admin-stat-card__value">{{ $importResult['summary']['merged_rows'] ?? 0 }}</div></div></div>
                    <div class="col-md-3"><div class="admin-stat-card admin-stat-card--danger"><div class="admin-stat-card__label">{{ __('admin.crm_invalid_rows') }}</div><div class="admin-stat-card__value">{{ $importResult['summary']['invalid_rows'] ?? 0 }}</div></div></div>
                </div>
            </div>
        @endif

        @if(!empty($preview))
            @php
                $isStatusUpdateMode = ($preview['duplicate_mode'] ?? '') === \App\Support\CrmLeadTransferService::DUPLICATE_MODE_STATUS_UPDATE;
            @endphp
            <div class="card admin-card p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <h2 class="h5 mb-1">
                            @if($isStatusUpdateMode)
                                🔄 {{ __('admin.crm_import_preview') }} (تحديث حالة الليدات بالنظام)
                            @else
                                {{ __('admin.crm_import_preview') }}
                            @endif
                        </h2>
                        <p class="text-muted mb-0">
                            @if($isStatusUpdateMode)
                                تم مطابقة الليدات الموجودة بالنظام وتصفية الليدات غير الموجودة تلقائياً. حدد الحالة الجديدة للتحديث دفعة واحدة.
                            @else
                                {{ __('admin.crm_import_preview_desc') }}
                            @endif
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <form method="post" action="{{ route('admin.crm.leads.import.cancel') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger fw-bold d-flex align-items-center gap-1" onclick="return confirm('هل أنت متأكد من إلغاء عملية الاستيراد كلياً؟');">
                                ❌ إلغاء / كنسل الاستيراد
                            </button>
                        </form>
                    </div>
                </div>

                @if($isStatusUpdateMode)
                    <form method="post" action="{{ route('admin.crm.leads.import') }}" id="statusUpdateConfirmForm" class="mb-4 p-3 bg-light border border-primary rounded-3">
                        @csrf
                        <div class="row align-items-end g-3">
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-primary fs-6">
                                    🎯 اختر الحالة الجديدة لتطبيقها على جميع الليدات المطابقة:
                                </label>
                                <select name="target_crm_status_id" class="form-select form-select-lg fw-bold border-primary" id="globalTargetStatusSelect" required>
                                    <option value="">-- اختر الحالة الجديدة التي ترغب بالتحويل إليها --</option>
                                    @foreach($statuses as $st)
                                        <option value="{{ $st->id }}">{{ $st->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 text-end">
                                <button type="submit" class="btn btn-success btn-lg fw-bold px-4 w-100">
                                    ✅ تأكيد تحديث حالة الليدات الآن
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="d-flex justify-content-end mb-3">
                        <form method="post" action="{{ route('admin.crm.leads.import') }}">
                            @csrf
                            <button class="btn btn-success fw-bold d-flex align-items-center gap-1">
                                ✅ {{ __('admin.crm_confirm_import') }}
                            </button>
                        </form>
                    </div>
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-3"><div class="admin-stat-card"><div class="admin-stat-card__label">{{ __('admin.total') }}</div><div class="admin-stat-card__value">{{ $preview['summary']['total_rows'] ?? 0 }}</div></div></div>
                    @if($isStatusUpdateMode)
                        <div class="col-md-3"><div class="admin-stat-card admin-stat-card--success"><div class="admin-stat-card__label">ليدات مطابقة بالسيستم</div><div class="admin-stat-card__value">{{ $preview['summary']['importable_rows'] ?? 0 }}</div></div></div>
                        <div class="col-md-3"><div class="admin-stat-card admin-stat-card--warning"><div class="admin-stat-card__label">ليدات غير موجودة (مستبعدة)</div><div class="admin-stat-card__value">{{ $preview['summary']['omitted_rows'] ?? 0 }}</div></div></div>
                    @else
                        <div class="col-md-3"><div class="admin-stat-card admin-stat-card--success"><div class="admin-stat-card__label">{{ __('admin.crm_ready_to_import_count') }}</div><div class="admin-stat-card__value">{{ $preview['summary']['importable_rows'] ?? 0 }}</div></div></div>
                        <div class="col-md-3"><div class="admin-stat-card admin-stat-card--warning"><div class="admin-stat-card__label">{{ __('admin.crm_duplicate_rows') }}</div><div class="admin-stat-card__value">{{ $preview['summary']['duplicate_rows'] ?? 0 }}</div></div></div>
                        <div class="col-md-3"><div class="admin-stat-card admin-stat-card--info"><div class="admin-stat-card__label">{{ __('admin.crm_merged_rows') }}</div><div class="admin-stat-card__value">{{ $preview['summary']['merged_rows'] ?? 0 }}</div></div></div>
                        <div class="col-md-3"><div class="admin-stat-card admin-stat-card--danger"><div class="admin-stat-card__label">{{ __('admin.crm_error_rows') }}</div><div class="admin-stat-card__value">{{ $preview['summary']['error_rows'] ?? 0 }}</div></div></div>
                    @endif
                </div>

                @if(! $isStatusUpdateMode)
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if(($preview['summary']['duplicate_rows'] ?? 0) > 0)
                            <a class="btn btn-outline-warning btn-sm" href="{{ route('admin.crm.leads.import.report', ['report' => 'duplicates', 'format' => 'xlsx']) }}">{{ __('admin.crm_download_duplicate_file') }}</a>
                        @endif
                        @if(($preview['summary']['merged_rows'] ?? 0) > 0)
                            <a class="btn btn-outline-info btn-sm" href="{{ route('admin.crm.leads.import.report', ['report' => 'merged', 'format' => 'xlsx']) }}">{{ __('admin.crm_download_merged_file') }}</a>
                        @endif
                        @if(($preview['summary']['error_rows'] ?? 0) > 0)
                            <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.crm.leads.import.report', ['report' => 'invalid', 'format' => 'xlsx']) }}">{{ __('admin.crm_download_invalid_file') }}</a>
                        @endif
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('admin.full_name') }}</th>
                                <th>{{ __('admin.phone') }}</th>
                                @if($isStatusUpdateMode)
                                    <th>الحالة الحالية بالنظام</th>
                                    <th>البائع / المسؤول</th>
                                    <th>الحالة الجديدة المستهدفة</th>
                                    <th>نتيجة المطابقة</th>
                                @else
                                    <th>{{ __('admin.status') }}</th>
                                    <th>{{ __('admin.assigned_user') }}</th>
                                    <th>{{ __('admin.crm_import_result') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($preview['rows'] ?? []) as $row)
                                <tr>
                                    <td>{{ $row['row_number'] }}</td>
                                    <td>{{ $row['duplicate_name'] ?: ($row['mapped']['full_name'] ?? '-') }}</td>
                                    <td>{{ $row['mapped']['phone'] ?? '-' }}</td>
                                    @if($isStatusUpdateMode)
                                        <td>
                                            <span class="badge px-3 py-2 fw-bold text-dark border shadow-sm" style="background-color: {{ $row['current_crm_status_color'] ?? '#f8f9fa' }}; color: #111827 !important; font-size: 0.85rem;">
                                                {{ $row['current_crm_status_name'] ?: 'غير محدد' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                                <i class="bi bi-person me-1"></i>{{ $row['assigned_user_name'] ?: 'غير معين' }}
                                            </span>
                                        </td>
                                        <td>
                                            <select name="row_status_ids[{{ $row['row_number'] }}]" form="statusUpdateConfirmForm" class="form-select form-select-sm row-target-status fw-bold">
                                                <option value="">-- كالحالة الجماعية --</option>
                                                @foreach($statuses as $st)
                                                    <option value="{{ $st->id }}">{{ $st->name_ar }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <span class="badge text-bg-success">🎯 ليد موجود بالسيستم - جاهز لتحديث الحالة</span>
                                        </td>
                                    @else
                                        <td>{{ $row['mapped']['crm_status'] ?? '-' }}</td>
                                        <td>{{ $row['mapped']['assigned_user'] ?? '-' }}</td>
                                        <td>
                                            @if(!empty($row['errors']))
                                                <span class="badge text-bg-danger">{{ implode(' | ', $row['errors']) }}</span>
                                            @elseif(($row['action'] ?? null) === 'merge')
                                                <span class="badge text-bg-info">
                                                    {{ __('admin.crm_duplicate_will_be_merged') }}
                                                    @if(!empty($row['duplicate_value']))
                                                        - {{ $row['duplicate_value'] }}
                                                    @endif
                                                </span>
                                            @elseif(!empty($row['duplicate_reason']))
                                                <span class="badge text-bg-warning">
                                                    {{ __('admin.crm_duplicate_will_be_skipped') }}
                                                    @if(!empty($row['duplicate_value']))
                                                        - {{ $row['duplicate_value'] }}
                                                    @endif
                                                </span>
                                            @else
                                                <span class="badge text-bg-success">{{ __('admin.crm_ready_to_import') }}</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($isStatusUpdateMode)
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const globalSelect = document.getElementById('globalTargetStatusSelect');
                        if (globalSelect) {
                            globalSelect.addEventListener('change', function() {
                                const val = this.value;
                                document.querySelectorAll('.row-target-status').forEach(select => {
                                    select.value = val;
                                });
                            });
                        }
                    });
                </script>
            @endif
        @endif
    </div>
    @endif

    <div class="col-lg-5">
        @if($canExportLeads)
            <div class="card admin-card p-4 mb-4">
                <h2 class="h5 mb-1">{{ __('admin.crm_export_leads') }}</h2>
                <p class="text-muted mb-3">{{ __('admin.crm_export_leads_desc') }}</p>

                <form method="post" action="{{ route('admin.crm.leads.export', $currentFilters) }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">{{ __('admin.type') }}</label>
                        <select class="form-select" name="format">
                            <option value="csv">CSV</option>
                            <option value="xlsx">XLSX</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('admin.crm_export_fields') }}</label>
                        <div class="row g-2">
                            @foreach($fieldOptions as $key => $field)
                                <div class="col-md-6">
                                    <label class="form-check border rounded-4 px-3 py-2 h-100">
                                        <input class="form-check-input me-2" type="checkbox" name="fields[]" value="{{ $key }}" checked>
                                        <span>{{ $field['label_ar'] }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @foreach($currentFilters as $filterKey => $filterValue)
                        @if(filled($filterValue))
                            <input type="hidden" name="{{ $filterKey }}" value="{{ $filterValue }}">
                        @endif
                    @endforeach
                    <div class="col-12">
                        <button class="btn btn-primary">{{ __('admin.crm_export_now') }}</button>
                    </div>
                </form>
            </div>
        @endif

        <div class="card admin-card p-4">
            <h2 class="h5 mb-1">{{ __('admin.crm_template_structure') }}</h2>
            <p class="text-muted mb-3">{{ __('admin.crm_template_structure_desc') }}</p>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            @foreach($templateHeaders as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sampleRows as $row)
                            <tr>
                                @foreach($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modeField = document.querySelector('[data-crm-duplicate-mode]');
    const detectorWrapper = document.querySelector('[data-crm-duplicate-detector-wrapper]');
    const detectorField = document.querySelector('[data-crm-duplicate-detector]');

    if (!modeField || !detectorWrapper || !detectorField) {
        return;
    }

    const syncDuplicateFields = () => {
        const needsDetector = modeField.value !== 'none';
        detectorWrapper.style.display = needsDetector ? '' : 'none';
        detectorField.disabled = !needsDetector;
        detectorField.required = needsDetector;
    };

    modeField.addEventListener('change', syncDuplicateFields);
    syncDuplicateFields();
});
</script>
@endpush
