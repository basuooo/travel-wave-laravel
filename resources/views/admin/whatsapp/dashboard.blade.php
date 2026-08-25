@extends('layouts.admin')

@section('title', 'WhatsApp CRM & Campaign Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">WhatsApp CRM & Campaign Management</h3>
            <p class="text-muted small mb-0">لوحة التحكم والمراقبة المركزية لنظام واتساب</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.whatsapp.retargeting.create') }}" class="btn btn-danger font-weight-bold">
                🎯 حملة Retargeting جديدة
            </a>
            <a href="{{ route('admin.whatsapp.bulk.create') }}" class="btn btn-primary font-weight-bold">
                🚀 حملة Bulk جديدة
            </a>
        </div>
    </div>

    @include('admin.whatsapp.nav')

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center p-3 h-100 bg-white">
                <div class="text-primary mb-2" style="font-size: 2rem;">📱</div>
                <div class="text-muted small fw-bold">أرقام WhatsApp المرتبطة</div>
                <div class="fs-2 fw-bold text-dark">{{ $accountsCount }}</div>
                <div class="mt-2">
                    <span class="badge bg-success">{{ $connectedAccountsCount }} Connected</span>
                    <span class="badge bg-danger">{{ $disconnectedAccountsCount }} Disconnected</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center p-3 h-100 bg-white">
                <div class="text-info mb-2" style="font-size: 2rem;">💬</div>
                <div class="text-muted small fw-bold">إجمالي المحادثات</div>
                <div class="fs-2 fw-bold text-dark">{{ $conversationsCount }}</div>
                <div class="text-muted small mt-2">محادثات نشطة وسابقة</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center p-3 h-100 bg-white">
                <div class="text-warning mb-2" style="font-size: 2rem;">👥</div>
                <div class="text-muted small fw-bold">جهة اتصال في CRM</div>
                <div class="fs-2 fw-bold text-dark">{{ $contactsCount }}</div>
                <div class="text-muted small mt-2">عملاء ومحتملين</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center p-3 h-100 bg-white">
                <div class="text-success mb-2" style="font-size: 2rem;">⚡</div>
                <div class="text-muted small fw-bold">الحملات النشطة والمجدولة</div>
                <div class="fs-2 fw-bold text-dark">{{ $activeCampaignsCount }}</div>
                <div class="mt-2">
                    <span class="badge bg-success">{{ $activeCampaignsCount }} نشطة الآن</span>
                    <span class="badge bg-secondary">{{ $scheduledCampaignsCount }} مجدولة</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-success border-4">
                <div class="text-muted small">الرسائل المرسلة</div>
                <div class="fs-3 fw-bold text-success">{{ number_format($sentMessagesCount) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-danger border-4">
                <div class="text-muted small">الرسائل الفاشلة</div>
                <div class="fs-3 fw-bold text-danger">{{ number_format($failedMessagesCount) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-warning border-4">
                <div class="text-muted small">الرسائل في الانتظار (Pending)</div>
                <div class="fs-3 fw-bold text-warning">{{ number_format($pendingMessagesCount) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-info border-4">
                <div class="text-muted small">إجمالي الـ Replies الواردة</div>
                <div class="fs-3 fw-bold text-info">{{ number_format($repliesCount) }}</div>
            </div>
        </div>
    </div>

    <!-- Accounts Status & Recent Campaigns -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    <span>📱 أرقام الواتساب المرتبطة بالنظام</span>
                    <a href="{{ route('admin.whatsapp.accounts.index') }}" class="btn btn-sm btn-outline-primary">إدارة الأرقام</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>اسم الرقم</th>
                                    <th>رقم الهاتف</th>
                                    <th>الحالة</th>
                                    <th>الموظف</th>
                                    <th>آخر نشاط</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accounts as $acc)
                                    <tr>
                                        <td class="fw-bold">{{ $acc->name }}</td>
                                        <td>{{ $acc->phone_number }}</td>
                                        <td>
                                            @if($acc->status === 'connected')
                                                <span class="badge bg-success">🟢 Connected</span>
                                            @else
                                                <span class="badge bg-danger">🔴 Disconnected</span>
                                            @endif
                                        </td>
                                        <td>{{ $acc->assignedUser?->name ?? 'غير معين' }}</td>
                                        <td class="small text-muted">{{ $acc->last_connected_at ? $acc->last_connected_at->diffForHumans() : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">لا توجد أرقام واتساب مرتبطة حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    <span>🚀 آخر الحملات النشطة</span>
                    <a href="{{ route('admin.whatsapp.bulk.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>اسم الحملة</th>
                                    <th>النوع</th>
                                    <th>التقدم</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCampaigns as $camp)
                                    @php($pct = $camp->total_contacts > 0 ? round(($camp->sent_count / $camp->total_contacts) * 100) : 0)
                                    <tr>
                                        <td class="fw-bold">{{ $camp->name }}</td>
                                        <td>
                                            <span class="badge {{ $camp->type === 'retargeting' ? 'bg-danger' : 'bg-primary' }}">
                                                {{ strtoupper($camp->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $pct }}%"></div>
                                                </div>
                                                <span class="small font-monospace">{{ $camp->sent_count }}/{{ $camp->total_contacts }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">{{ $camp->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">لا توجد حملات منشأة بعد</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
