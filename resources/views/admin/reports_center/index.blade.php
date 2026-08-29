@extends('layouts.admin')

@section('page_title', '📊 مركز التقارير والتحليل الإداري (Reports Control Center)')
@section('page_description', 'شاشة المراقبة والتحليل الإداري لمتابعة الأداء، المبيعات، التسويق، الـ SLA، والإيرادات.')

@section('content')
<style>
.reports-kpi-card {
    transition: all 0.25s ease-in-out;
    border-radius: 14px;
    border: 1px solid rgba(0,0,0,0.08);
}
.reports-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}
.alert-card-clickable {
    cursor: pointer;
    transition: transform 0.2s ease;
}
.alert-card-clickable:hover {
    transform: scale(1.02);
}
.metric-clickable {
    cursor: pointer;
    text-decoration: underline;
    text-decoration-style: dashed;
}
.nav-pills .nav-link.active {
    background-color: #0d6efd !important;
    font-weight: 700;
}
</style>

<!-- Top Executive Header Banner -->
<div class="card admin-card border-0 bg-dark text-white p-4 mb-4 shadow-sm rounded-4 position-relative overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative" style="z-index: 2;">
        <div>
            <span class="badge text-bg-warning text-dark fw-bold px-3 py-1 mb-2">⚡ Executive Control Center</span>
            <h2 class="h3 fw-extrabold mb-1 text-white d-flex align-items-center gap-2">
                <span>📊</span> <span>مركز التقارير والتحليل الإداري لمبيعات الـ CRM</span>
            </h2>
            <p class="mb-0 text-white-50 fs-6">
                مراقبة لحظية وشاملة لأداء الشركة، تحليلات التحويل، سرعة الاستجابة، وأداء المبيعات والتسويق.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.crm.reports-center.custom-builder') }}" class="btn btn-warning text-dark fw-bold px-4 py-2 shadow-sm rounded-3">
                🛠️ منشئ التقارير المخصصة (Custom Builder)
            </a>
            <a href="{{ route('admin.crm.reports-center.export', array_merge(request()->all(), ['metric_key' => 'total_leads'])) }}" class="btn btn-outline-light fw-bold px-4 py-2 shadow-sm rounded-3">
                📥 تصدير التقرير الفعلي (Excel)
            </a>
        </div>
    </div>
</div>

<!-- Unified Filters Bar -->
<div class="card admin-card p-3 mb-4 shadow-sm rounded-4 bg-white border">
    <form method="get" action="{{ route('admin.crm.reports-center.index') }}" class="row g-3 align-items-end">
        <input type="hidden" name="tab" value="{{ $activeTab }}">

        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted">🗓️ نطاق التاريخ (Date Range):</label>
            <select name="date_range" class="form-select form-select-sm fw-bold">
                <option value="today" @selected(($filters['date_range'] ?? '') === 'today')>اليوم (Today)</option>
                <option value="yesterday" @selected(($filters['date_range'] ?? '') === 'yesterday')>الأمس (Yesterday)</option>
                <option value="this_week" @selected(($filters['date_range'] ?? '') === 'this_week')>هذا الأسبوع (This Week)</option>
                <option value="last_week" @selected(($filters['date_range'] ?? '') === 'last_week')>الأسبوع الماضي (Last Week)</option>
                <option value="this_month" @selected(($filters['date_range'] ?? '') === 'this_month')>هذا الشهر (This Month)</option>
                <option value="last_month" @selected(($filters['date_range'] ?? '') === 'last_month')>الشهر الماضي (Last Month)</option>
                <option value="this_quarter" @selected(($filters['date_range'] ?? '') === 'this_quarter')>الربع الحالي (This Quarter)</option>
                <option value="this_year" @selected(($filters['date_range'] ?? '') === 'this_year')>السنة الحالية (This Year)</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted">👤 موظف المبيعات (Sales Rep):</label>
            <select name="sales_rep_id" class="form-select form-select-sm">
                <option value="">جميع الموظفين (All Sales)</option>
                @foreach($salesReps as $rep)
                    <option value="{{ $rep->id }}" @selected(($filters['sales_rep_id'] ?? null) == $rep->id)>{{ $rep->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label fw-bold small text-muted">🏷️ حالة الليد (Status):</label>
            <select name="status_id" class="form-select form-select-sm">
                <option value="">جميع الحالات</option>
                @foreach($statuses as $st)
                    <option value="{{ $st->id }}" @selected(($filters['status_id'] ?? null) == $st->id)>{{ $st->name_ar }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label fw-bold small text-muted">📢 المصدر (Source):</label>
            <select name="source_id" class="form-select form-select-sm">
                <option value="">جميع المصادر</option>
                @foreach($sources as $src)
                    <option value="{{ $src->id }}" @selected(($filters['source_id'] ?? null) == $src->id)>{{ $src->name_ar }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">🔍 تطبيق الفلاتر</button>
            <a href="{{ route('admin.crm.reports-center.index') }}" class="btn btn-sm btn-outline-secondary">إعادة</a>
        </div>
    </form>
</div>

<!-- 6 Navigation Tabs Header -->
<ul class="nav nav-pills nav-fill bg-white p-2 rounded-4 shadow-sm border mb-4 gap-2">
    <li class="nav-item">
        <a class="nav-link text-dark py-2 px-3 {{ $activeTab === 'overview' ? 'active' : '' }}" href="{{ route('admin.crm.reports-center.index', array_merge(request()->query(), ['tab' => 'overview'])) }}">
            01. Overview 📊
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark py-2 px-3 {{ $activeTab === 'sales' ? 'active' : '' }}" href="{{ route('admin.crm.reports-center.index', array_merge(request()->query(), ['tab' => 'sales'])) }}">
            02. Sales Performance 🏆
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark py-2 px-3 {{ $activeTab === 'leads' ? 'active' : '' }}" href="{{ route('admin.crm.reports-center.index', array_merge(request()->query(), ['tab' => 'leads'])) }}">
            03. Leads & Funnel 🔄
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark py-2 px-3 {{ $activeTab === 'marketing' ? 'active' : '' }}" href="{{ route('admin.crm.reports-center.index', array_merge(request()->query(), ['tab' => 'marketing'])) }}">
            04. Marketing 🎯
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark py-2 px-3 {{ $activeTab === 'revenue' ? 'active' : '' }}" href="{{ route('admin.crm.reports-center.index', array_merge(request()->query(), ['tab' => 'revenue'])) }}">
            05. Revenue & Operations 💰
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark py-2 px-3 {{ $activeTab === 'advanced' ? 'active' : '' }}" href="{{ route('admin.crm.reports-center.index', array_merge(request()->query(), ['tab' => 'advanced'])) }}">
            06. Advanced Audit 🛡️
        </a>
    </li>
</ul>

<!-- Management Alerts Section (Clickable) -->
@if(!empty($data['executive']['alerts']) && count($data['executive']['alerts']) > 0)
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="fs-4">🚨</span>
            <h3 class="h5 fw-bold text-dark mb-0">تنبيهات التدخل الإداري (Management Alerts):</h3>
            <span class="badge text-bg-danger rounded-pill px-3">{{ count($data['executive']['alerts']) }} تنبيه عاجل</span>
        </div>
        <div class="row g-3">
            @foreach($data['executive']['alerts'] as $alert)
                <div class="col-md-6 col-lg-4">
                    <div class="alert alert-{{ $alert['type'] }} alert-card-clickable border-2 shadow-sm p-3 rounded-4 mb-0" onclick="openDrilldownModal('{{ $alert['key'] }}', '{{ $alert['title'] }}')">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-3">{{ $alert['icon'] }}</span>
                            <span class="badge text-bg-{{ $alert['type'] }} fs-6 px-3 py-1">{{ $alert['count'] }} حالة</span>
                        </div>
                        <h4 class="h6 fw-bold mb-1">{{ $alert['title'] }}</h4>
                        <p class="small text-muted mb-0">{{ $alert['description'] }}</p>
                        <div class="mt-2 text-end">
                            <span class="small fw-bold text-{{ $alert['type'] }}">اضغط لعرض القائمة والتفاصيل 🔍</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Tab Content Views -->
@if($activeTab === 'overview')
    <!-- Main KPIs Grid -->
    @if(!empty($data['executive']['kpis']))
        <div class="row g-3 mb-4">
            @foreach($data['executive']['kpis'] as $kKey => $kpi)
                <div class="col-md-6 col-lg-4">
                    <div class="card admin-card reports-kpi-card h-100 p-4 bg-white shadow-sm">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <span class="fs-2">{{ $kpi['icon'] }}</span>
                            @if(isset($kpi['change_pct']))
                                <span class="badge text-bg-{{ $kpi['change_pct'] >= 0 ? 'success' : 'danger' }} fs-6">
                                    {{ $kpi['change_pct'] >= 0 ? '▲ +' : '▼ ' }}{{ $kpi['change_pct'] }}%
                                </span>
                            @endif
                        </div>
                        <div class="text-muted small mb-1">{{ $kpi['label'] }}</div>
                        <div class="h3 fw-bold text-dark mb-0 metric-clickable" onclick="openDrilldownModal('{{ $kKey }}', '{{ $kpi['label'] }}')">
                            {{ $kpi['value'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Lead Funnel Overview -->
    @if(!empty($data['lead_funnel']))
        <div class="card admin-card p-4 shadow-sm bg-white rounded-4 mb-4">
            <h3 class="h5 fw-bold text-dark mb-3">🔄 مسار وتحويل الليدز (Lead Funnel Overview):</h3>
            <div class="row g-3">
                @foreach($data['lead_funnel']['stages'] as $stg)
                    <div class="col-md-4 col-lg-2">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <div class="small text-muted mb-1">{{ $stg['name'] }}</div>
                            <div class="h4 fw-bold text-primary mb-1">{{ $stg['count'] }}</div>
                            <span class="badge text-bg-secondary">{{ $stg['pct'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@elseif($activeTab === 'sales')
    <!-- Sales Performance Ranking Table -->
    @if(!empty($data['sales_performance']))
        <div class="card admin-card p-4 shadow-sm bg-white rounded-4">
            <h3 class="h5 fw-bold text-dark mb-3">🏆 تقرير أداء موظفي المبيعات والترتيب (Sales Performance Ranking):</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th># الترتيب</th>
                            <th>الموظف</th>
                            <th>إجمالي الليدز</th>
                            <th>تم التواصل</th>
                            <th>الحجوزات</th>
                            <th>المبيعات (Revenue)</th>
                            <th>المكالمات</th>
                            <th>الواتساب</th>
                            <th>الملاحظات</th>
                            <th>معدل التحويل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['sales_performance'] as $idx => $sRow)
                            <tr>
                                <td class="fw-bold fs-5">#{{ $idx + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $sRow['user_name'] }}</td>
                                <td>{{ $sRow['total_leads'] }}</td>
                                <td>{{ $sRow['contacted_leads'] }}</td>
                                <td class="fw-bold text-success">{{ $sRow['bookings'] }}</td>
                                <td class="fw-bold text-dark">{{ number_format($sRow['revenue'], 2) }} ج.م</td>
                                <td>{{ $sRow['calls_count'] }}</td>
                                <td>{{ $sRow['whatsapp_count'] }}</td>
                                <td>{{ $sRow['notes_count'] }}</td>
                                <td><span class="badge text-bg-info fs-6">{{ $sRow['conversion_rate'] }}%</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@elseif($activeTab === 'leads')
    <!-- Lead Aging Report -->
    @if(!empty($data['lead_aging']))
        <div class="card admin-card p-4 shadow-sm bg-white rounded-4 mb-4">
            <h3 class="h5 fw-bold text-dark mb-3">⏳ تقرير أعمار وتأخر الليدز (Lead Aging Report):</h3>
            <div class="row g-3">
                @foreach($data['lead_aging'] as $agKey => $agData)
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark">{{ $agData['label'] }}</span>
                                <span class="badge text-bg-warning fs-6">{{ $agData['count'] }} ليد</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@elseif($activeTab === 'marketing')
    <!-- Sources Performance Table -->
    @if(!empty($data['source_performance']))
        <div class="card admin-card p-4 shadow-sm bg-white rounded-4">
            <h3 class="h5 fw-bold text-dark mb-3">📢 تقرير جودة وأداء مصادر العملاء (Lead Sources Quality):</h3>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>المصدر (Source)</th>
                            <th>عدد الليدز</th>
                            <th>الحجوزات المؤكدة</th>
                            <th>المبيعات المتحققة</th>
                            <th>معدل التحويل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['source_performance'] as $srcRow)
                            <tr>
                                <td class="fw-bold text-dark">{{ $srcRow['source_name'] }}</td>
                                <td>{{ $srcRow['total_leads'] }}</td>
                                <td>{{ $srcRow['bookings'] }}</td>
                                <td class="fw-bold text-success">{{ number_format($srcRow['revenue'], 2) }} ج.م</td>
                                <td><span class="badge text-bg-primary fs-6">{{ $srcRow['conversion_rate'] }}%</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@else
    <div class="card admin-card p-5 text-center shadow-sm rounded-4">
        <div class="fs-1 mb-2">📊</div>
        <h4 class="fw-bold">تقرير تفصيلي مكتمل البيانات</h4>
        <p class="text-muted mb-0">يمكنك استخدام الفلاتر أعلاه أو اختيار التبويب المطلوب للتحليل العميق.</p>
    </div>
@endif

<!-- Interactive Drill-Down Modal -->
<div class="modal fade" id="drilldownModal" tabindex="-1" aria-labelledby="drilldownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white p-4">
                <div>
                    <h5 class="modal-title fw-bold" id="drilldownModalTitle">🔍 قائمة العملاء التفصيلية</h5>
                    <div class="small text-white-50">عرض وإدارة الحالات المرتبطة بهذا المؤشر/التنبيه</div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light" id="drilldownModalContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="mt-2 text-muted fw-bold">جاري تحميل البيانات التفصيلية...</div>
                </div>
            </div>
            <div class="modal-footer bg-white p-3 border-top">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إغلاق</button>
                <a id="drilldownExportBtn" href="#" class="btn btn-success fw-bold px-4">📥 تصدير القائمة لـ Excel</a>
            </div>
        </div>
    </div>
</div>

<script>
function openDrilldownModal(metricKey, title) {
    document.getElementById('drilldownModalTitle').innerText = '🔍 ' + title;
    document.getElementById('drilldownModalContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2 text-muted fw-bold">جاري تحميل البيانات التفصيلية...</div>
        </div>
    `;

    const exportUrl = `{{ route('admin.crm.reports-center.export') }}?metric_key=${metricKey}`;
    document.getElementById('drilldownExportBtn').setAttribute('href', exportUrl);

    var modal = new bootstrap.Modal(document.getElementById('drilldownModal'));
    modal.show();

    fetch(`{{ route('admin.crm.reports-center.drilldown') }}?metric_key=${metricKey}`)
        .then(res => res.json())
        .then(data => {
            if (!data.leads || data.leads.length === 0) {
                document.getElementById('drilldownModalContent').innerHTML = `
                    <div class="text-center py-5">
                        <div class="fs-1 mb-2 text-muted">🔍</div>
                        <div class="fw-bold text-dark">لا توجد حالات مسجلة لهذا المؤشر حالياً.</div>
                    </div>
                `;
                return;
            }

            let html = `
                <div class="table-responsive bg-white rounded-3 p-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th># ID</th>
                                <th>اسم العميل</th>
                                <th>رقم الهاتف</th>
                                <th>الحالة</th>
                                <th>الموظف المسئول</th>
                                <th>تاريخ التسجيل</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.leads.forEach(lead => {
                html += `
                    <tr>
                        <td class="fw-bold">#${lead.id}</td>
                        <td class="fw-bold text-dark">${lead.full_name}</td>
                        <td dir="ltr" class="text-start">${lead.phone}</td>
                        <td><span class="badge" style="background-color:${lead.status_color}">${lead.status_name}</span></td>
                        <td>${lead.assigned_user_name}</td>
                        <td class="small text-muted">${lead.created_at_formatted}</td>
                        <td>
                            <a href="${lead.edit_url}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                                ✏️ فتح العميل
                            </a>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            document.getElementById('drilldownModalContent').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('drilldownModalContent').innerHTML = `
                <div class="alert alert-danger">حدث خطأ أثناء تحميل البيانات التفصيلية.</div>
            `;
        });
}
</script>
@endsection
