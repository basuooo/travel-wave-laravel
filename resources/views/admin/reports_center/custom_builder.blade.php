@extends('layouts.admin')

@section('page_title', '🛠️ منشئ التقارير المخصصة (Custom Report Builder)')
@section('page_description', 'تصميم وتخصيص وحفظ التقارير الحرة واختيار الأعمدة والشروط بنفسك.')

@section('content')
<style>
.column-badge {
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
}
.column-badge:hover {
    transform: scale(1.03);
}
.filter-row {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 12px;
}
</style>

<!-- Top Header Banner -->
<div class="card admin-card border-0 bg-primary text-white p-4 mb-4 shadow-sm rounded-4 position-relative overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative" style="z-index: 2;">
        <div>
            <h2 class="h3 fw-extrabold mb-1 text-white d-flex align-items-center gap-2">
                <span>🛠️</span> <span>منشئ التقارير المخصصة (Custom Report Builder)</span>
            </h2>
            <p class="mb-0 text-white-50 fs-6">
                صمّم تقريرك بنفسك: حدد الأعمدة، اختر الشروط التراكمية، تجمّع البيانات، واحفظ التقرير لفتحه بضغطة زر.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.crm.reports-center.index') }}" class="btn btn-light text-primary fw-bold px-4 py-2 shadow-sm rounded-3">
                ⬅️ العودة لمركز التقارير الرئيسي
            </a>
        </div>
    </div>
</div>

<!-- Saved Templates Bar -->
@if(count($savedTemplates) > 0)
    <div class="card admin-card p-3 mb-4 shadow-sm rounded-4 bg-white border">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="fs-5">📁</span>
                <span class="fw-bold text-dark">قوالب التقارير المخصصة المحفوظة:</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($savedTemplates as $tmpl)
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('admin.crm.reports-center.custom-builder', ['run' => 1, 'entity' => $tmpl->entity_type, 'columns' => $tmpl->selected_columns, 'filters' => $tmpl->filters, 'group_by' => $tmpl->group_by]) }}" class="btn btn-outline-primary fw-bold">
                            {{ $tmpl->title }}
                        </a>
                        <form method="post" action="{{ route('admin.crm.reports-center.custom-builder.delete', $tmpl->id) }}" onsubmit="return confirm('حذف هذا القالب؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">×</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- Builder Form Card -->
<div class="card admin-card p-4 mb-4 shadow-sm rounded-4 bg-white border">
    <form method="get" action="{{ route('admin.crm.reports-center.custom-builder') }}" id="customReportForm">
        <input type="hidden" name="run" value="1">

        <!-- Step 1: Select Entity -->
        <div class="mb-4 pb-3 border-bottom">
            <label class="form-label fw-bold fs-6 text-dark d-flex align-items-center gap-2">
                <span class="badge text-bg-primary rounded-circle px-2">1</span>
                <span>اختر مصدر البيانات الأساسي (Data Entity):</span>
            </label>
            <div class="row g-3">
                @foreach($entities as $entKey => $entMeta)
                    <div class="col-md-6">
                        <div class="form-check form-check-inline p-3 border rounded-3 bg-light w-100 me-0">
                            <input class="form-check-input ms-0 me-2" type="radio" name="entity" id="ent_{{ $entKey }}" value="{{ $entKey }}" @checked($selectedEntity === $entKey) onchange="this.form.submit()">
                            <label class="form-check-label fw-bold text-dark cursor-pointer" for="ent_{{ $entKey }}">
                                {{ $entMeta['label_ar'] }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Step 2: Columns Selection -->
        <div class="mb-4 pb-3 border-bottom">
            <label class="form-label fw-bold fs-6 text-dark d-flex align-items-center gap-2">
                <span class="badge text-bg-primary rounded-circle px-2">2</span>
                <span>حدد الأعمدة والبيانات المراد إظهارها فقط:</span>
            </label>
            <div class="row g-2">
                @php $availColumns = $entities[$selectedEntity]['columns'] ?? []; @endphp
                @foreach($availColumns as $colKey => $colMeta)
                    <div class="col-md-4 col-lg-3">
                        <div class="form-check p-2 border rounded-3 bg-light">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="columns[]" value="{{ $colKey }}" id="col_{{ $colKey }}" @checked(empty($selectedColumns) || in_array($colKey, $selectedColumns))>
                            <label class="form-check-label small fw-bold text-dark cursor-pointer" for="col_{{ $colKey }}">
                                {{ $colMeta['label_ar'] }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Step 3: Dynamic Filters Builder -->
        <div class="mb-4 pb-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="form-label fw-bold fs-6 text-dark mb-0 d-flex align-items-center gap-2">
                    <span class="badge text-bg-primary rounded-circle px-2">3</span>
                    <span>الشروط والفلاتر التراكمية (Dynamic Filters):</span>
                </label>
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="addFilterRow()">+ إضافة شرط جديد</button>
            </div>

            <div id="filterRowsContainer" class="d-flex flex-column gap-2">
                @if(!empty($filterConditions))
                    @foreach($filterConditions as $fIdx => $fCond)
                        <div class="filter-row d-flex align-items-center gap-2">
                            <select name="filters[{{ $fIdx }}][field]" class="form-select form-select-sm">
                                <option value="">اختر الحقل...</option>
                                @foreach($availColumns as $colKey => $colMeta)
                                    <option value="{{ $colKey }}" @selected(($fCond['field'] ?? '') === $colKey)>{{ $colMeta['label_ar'] }}</option>
                                @endforeach
                            </select>
                            <select name="filters[{{ $fIdx }}][operator]" class="form-select form-select-sm">
                                @foreach($operators as $opKey => $opLabel)
                                    <option value="{{ $opKey }}" @selected(($fCond['operator'] ?? '') === $opKey)>{{ $opLabel }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="filters[{{ $fIdx }}][value]" class="form-control form-control-sm" value="{{ $fCond['value'] ?? '' }}" placeholder="القيمة المطلوب المقارنة بها...">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">×</button>
                        </div>
                    @endforeach
                @else
                    <div class="filter-row d-flex align-items-center gap-2">
                        <select name="filters[0][field]" class="form-select form-select-sm">
                            <option value="">اختر الحقل للمقارنة...</option>
                            @foreach($availColumns as $colKey => $colMeta)
                                <option value="{{ $colKey }}">{{ $colMeta['label_ar'] }}</option>
                            @endforeach
                        </select>
                        <select name="filters[0][operator]" class="form-select form-select-sm">
                            @foreach($operators as $opKey => $opLabel)
                                <option value="{{ $opKey }}">{{ $opLabel }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="filters[0][value]" class="form-control form-control-sm" placeholder="القيمة المطلوب تصفيتها...">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">×</button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Step 4: Grouping & Actions -->
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">📊 تجميع النتائج حسب (Group By):</label>
                <select name="group_by" class="form-select form-select-sm">
                    <option value="">بدون تجميع (سجلات تفصيلية مباشرة)</option>
                    @foreach($availColumns as $colKey => $colMeta)
                        <option value="{{ $colKey }}" @selected($groupBy === $colKey)>تجميع حسب: {{ $colMeta['label_ar'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline-success fw-bold px-4" data-bs-toggle="modal" data-bs-target="#saveTemplateModal">
                    💾 حفظ القالب للاستخدام لاحقاً
                </button>
                <button type="submit" class="btn btn-primary fw-bold px-5">
                    🚀 تشغيل ومعاينة التقرير المخصص
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Output Report View Section -->
@if(!empty($reportData))
    <div class="card admin-card p-4 shadow-sm rounded-4 bg-white border">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="h4 fw-bold text-dark mb-1">📊 نتائج التقرير المخصص</h3>
                <span class="badge text-bg-primary fs-6">إجمالي النتائج: {{ $reportData['total_count'] }} سجل</span>
            </div>
            <a href="{{ route('admin.crm.reports-center.custom-builder.export', request()->query()) }}" class="btn btn-success fw-bold px-4">
                📥 تصدير النتيجة لـ Excel (.xlsx)
            </a>
        </div>

        <!-- Grouped Summary if active -->
        @if(!empty($reportData['grouped_data']) && count($reportData['grouped_data']) > 0)
            <div class="mb-4 p-3 bg-light rounded-3 border">
                <h4 class="h6 fw-bold text-primary mb-3">📊 تجميع الإحصائيات (Group By Summary):</h4>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered bg-white mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>المجموعة</th>
                                <th>العدد</th>
                                <th>إجمالي المبيعات</th>
                                <th>المبلغ المدفوع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData['grouped_data'] as $grp)
                                <tr>
                                    <td class="fw-bold">{{ $grp['group_name'] }}</td>
                                    <td class="fw-bold text-primary">{{ $grp['count'] }}</td>
                                    <td>{{ number_format($grp['sum_total_amount'], 2) }} EGP</td>
                                    <td class="text-success fw-bold">{{ number_format($grp['sum_paid_amount'], 2) }} EGP</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Detailed Custom Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead class="table-dark">
                    <tr>
                        @foreach($reportData['headers'] as $colLabel)
                            <th>{{ $colLabel }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['rows'] as $r)
                        <tr>
                            @foreach($reportData['headers'] as $colKey => $colLabel)
                                <td>{{ $r[$colKey] ?? '-' }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($reportData['headers']) }}" class="text-center py-4 text-muted fw-bold">
                                لا توجد سجلات تطابق هذه الأعمدة والشروط المحددة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Save Template Modal -->
<div class="modal fade" id="saveTemplateModal" tabindex="-1" aria-labelledby="saveTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="{{ route('admin.crm.reports-center.custom-builder.save') }}" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <input type="hidden" name="entity_type" value="{{ $selectedEntity }}">
            @foreach($selectedColumns as $sc)
                <input type="hidden" name="columns[]" value="{{ $sc }}">
            @endforeach
            <input type="hidden" name="group_by" value="{{ $groupBy }}">

            <div class="modal-header bg-primary text-white p-4">
                <h5 class="modal-title fw-bold" id="saveTemplateModalLabel">💾 حفظ قالب التقرير المخصص</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">اسم التقرير المخصص:</label>
                    <input type="text" name="title" class="form-control" placeholder="مثال: تقرير عملاء دبي الأسبوعي لأحمد..." required>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary fw-bold px-4">حفظ التقرير الآن 💾</button>
            </div>
        </form>
    </div>
</div>

<script>
let filterRowIndex = {{ count($filterConditions) ?: 1 }};
function addFilterRow() {
    const container = document.getElementById('filterRowsContainer');
    const firstRow = container.firstElementChild;
    if (!firstRow) return;

    const newRow = firstRow.cloneNode(true);
    newRow.querySelectorAll('input, select').forEach(el => {
        const name = el.getAttribute('name');
        if (name) {
            el.setAttribute('name', name.replace(/\[\d+\]/, `[${filterRowIndex}]`));
        }
        if (el.tagName === 'INPUT') el.value = '';
    });

    container.appendChild(newRow);
    filterRowIndex++;
}
</script>
@endsection
