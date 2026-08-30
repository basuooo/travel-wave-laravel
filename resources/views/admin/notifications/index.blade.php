@extends('layouts.admin')

@section('title', __('admin.notifications_ui_page_title'))
@section('page_title', __('admin.notifications_ui_page_title'))
@section('page_description', __('admin.notifications_ui_page_desc'))

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.notifications_ui_total') }}</div>
                    <div class="fs-4 fw-bold">{{ $summary['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-warning">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.notifications_ui_unread') }}</div>
                    <div class="fs-4 fw-bold text-warning">{{ $summary['unread'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-danger">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.notifications_ui_urgent_unread') }}</div>
                    <div class="fs-4 fw-bold text-danger">{{ $summary['urgent_unread'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-primary">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.notifications_ui_actionable') }}</div>
                    <div class="fs-4 fw-bold text-primary">{{ $summary['actionable_unread'] }}</div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <h6 class="fw-bold mb-2">تنبيه في البيانات المدخلة:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('smtp_diagnostic'))
        @php $diag = session('smtp_diagnostic'); @endphp
        <div class="card mb-4 {{ $diag['success'] ? 'border-success' : 'border-danger' }} shadow-sm">
            <div class="card-header {{ $diag['success'] ? 'bg-success text-white' : 'bg-danger text-white' }} py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    {{ $diag['success'] ? '✅ نتائج الفحص التشخيصي لـ SMTP: نجاح الاتصال 100%' : '❌ نتائج الفحص التشخيصي لـ SMTP: يوجد خطأ يحتاج إلى تعديل' }}
                </h6>
                <span class="badge bg-white text-dark">{{ $diag['success'] ? 'PASSED' : 'FAILED' }}</span>
            </div>
            <div class="card-body">
                <p class="fw-bold mb-3 fs-6">{{ $diag['summary'] }}</p>
                <div class="list-group">
                    @foreach($diag['steps'] as $index => $step)
                        <div class="list-group-item d-flex justify-content-between align-items-start {{ $step['status'] === 'passed' ? 'list-group-item-success' : ($step['status'] === 'failed' ? 'list-group-item-danger' : 'list-group-item-light') }}">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">
                                    @if($step['status'] === 'passed')
                                        ✅ الخطوة {{ $index + 1 }}: {{ $step['name'] }}
                                    @elseif($step['status'] === 'failed')
                                        ❌ الخطوة {{ $index + 1 }}: {{ $step['name'] }}
                                    @else
                                        ⏳ الخطوة {{ $index + 1 }}: {{ $step['name'] }}
                                    @endif
                                </div>
                                <small class="d-block mt-1 text-wrap" style="white-space: pre-line;">{{ $step['detail'] ?: 'لم يتم الوصول لهذه الخطوة' }}</small>
                            </div>
                            <span class="badge {{ $step['status'] === 'passed' ? 'bg-success' : ($step['status'] === 'failed' ? 'bg-danger' : 'bg-secondary') }}">
                                {{ strtoupper($step['status']) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">
                📧 إعدادات البريد الإلكتروني وخادم SMTP
            </h5>
            <span class="badge text-bg-info">Email & SMTP Settings</span>
        </div>
        <div class="card-body">
            <form method="post" action="{{ route('admin.notifications.email-settings.update') }}">
                @csrf
                @method('PUT')
                
                <h6 class="fw-bold mb-3 text-secondary">1. إيميلات استقبال الإشعارات (Notification Recipients)</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">إيميلات الاستقبال (ممكن إضافة أكثر من إيميل تفصل بينها فاصلة)</label>
                        <textarea class="form-control" name="notification_emails" rows="2" placeholder="example1@domain.com, example2@domain.com">{{ old('notification_emails', $mailSettings['notification_emails'] ?? $setting->notification_emails) }}</textarea>
                        <small class="form-text text-muted">يمكنك كتابة بريد إلكتروني واحد أو عدة إيميلات تفصل بينها فاصلة (,) لاستقبال إشعارات النظام.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">وضع إرسال البريد الإلكتروني (Notification Email Routing)</label>
                        <select class="form-select mb-3" name="notification_email_mode">
                            <option value="custom_only" {{ old('notification_email_mode', $mailSettings['notification_email_mode'] ?? $setting->notification_email_mode) === 'custom_only' ? 'selected' : '' }}>
                                1. إرسال للإيميلات المضافة في خانة "إيميلات الاستقبال" فقط
                            </option>
                            <option value="assigned_and_custom" {{ old('notification_email_mode', $mailSettings['notification_email_mode'] ?? ($setting->notification_email_mode ?? 'assigned_and_custom')) === 'assigned_and_custom' ? 'selected' : '' }}>
                                2. إرسال للإيميلات المضافة في خانة "إيميلات الاستقبال" + إيميل البائع/المسؤول معاً
                            </option>
                            <option value="assigned_only" {{ old('notification_email_mode', $mailSettings['notification_email_mode'] ?? $setting->notification_email_mode) === 'assigned_only' ? 'selected' : '' }}>
                                3. إرسال لإيميل البائع/المسؤول فقط
                            </option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-bold mb-3 text-secondary">2. إعدادات خادم البريد (SMTP Mail Server Credentials)</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">خادم البريد (SMTP Host)</label>
                        <input type="text" class="form-control" name="mail_host" value="{{ old('mail_host', $mailSettings['mail_host'] ?? $setting->mail_host) }}" placeholder="smtp.hostinger.com أو smtp.gmail.com">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">المنفذ (Port)</label>
                        <input type="text" class="form-control" name="mail_port" value="{{ old('mail_port', $mailSettings['mail_port'] ?? ($setting->mail_port ?: '587')) }}" placeholder="587 أو 465">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">اسم المستخدم (SMTP Username)</label>
                        <input type="text" class="form-control" name="mail_username" value="{{ old('mail_username', $mailSettings['mail_username'] ?? $setting->mail_username) }}" placeholder="info@travelwave-ras.com">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">كلمة المرور (SMTP Password)</label>
                        <input type="password" class="form-control" name="mail_password" value="{{ old('mail_password', $mailSettings['mail_password'] ?? $setting->mail_password) }}" placeholder="••••••••">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">التشفير (Encryption)</label>
                        <select class="form-select" name="mail_encryption">
                            <option value="tls" {{ old('mail_encryption', $mailSettings['mail_encryption'] ?? ($setting->mail_encryption ?? 'tls')) === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                            <option value="ssl" {{ old('mail_encryption', $mailSettings['mail_encryption'] ?? $setting->mail_encryption) === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                            <option value="null" {{ old('mail_encryption', $mailSettings['mail_encryption'] ?? $setting->mail_encryption) === 'null' ? 'selected' : '' }}>بدون تشفير (None)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">إيميل المُرسِل (From Address)</label>
                        <input type="text" class="form-control" name="mail_from_address" value="{{ old('mail_from_address', $mailSettings['mail_from_address'] ?? ($setting->mail_from_address ?: $setting->contact_email)) }}" placeholder="info@travelwave-ras.com">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">اسم المُرسِل (From Name)</label>
                        <input type="text" class="form-control" name="mail_from_name" value="{{ old('mail_from_name', $mailSettings['mail_from_name'] ?? ($setting->mail_from_name ?: 'Travel Wave')) }}" placeholder="Travel Wave Notifications">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" formaction="{{ route('admin.notifications.test-connection') }}" class="btn btn-outline-primary px-4 fw-bold">
                        🔍 فحص واختبار الاتصال بـ SMTP
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        💾 حفظ إعدادات البريد و SMTP
                    </button>
                </div>
            </form>

            <hr class="my-4">

            <div class="bg-light p-3 rounded-3">
                <h6 class="fw-bold mb-2 text-dark">🧪 اختبار إرسال بريد تجريبي (Test Email)</h6>
                <form method="post" action="{{ route('admin.notifications.test-email') }}" class="row g-2 align-items-center">
                    @csrf
                    <div class="col-md-8">
                        <input type="email" name="test_email" class="form-control" placeholder="أدخل إيميل لاختبار الإرسال إليه" value="{{ auth()->user()->email }}" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-success w-100">
                            🚀 إرسال رسالة تجريبية الآن
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_status') }}</label>
                    <select name="state" class="form-select">
                        <option value="all" @selected(($filters['state'] ?? 'all') === 'all')>{{ __('admin.notifications_ui_all') }}</option>
                        <option value="unread" @selected(($filters['state'] ?? null) === 'unread')>{{ __('admin.notifications_ui_state_unread') }}</option>
                        <option value="read" @selected(($filters['state'] ?? null) === 'read')>{{ __('admin.notifications_ui_state_read') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_type') }}</label>
                    <select name="type" class="form-select">
                        <option value="">{{ __('admin.notifications_ui_all') }}</option>
                        @foreach($typeOptions as $type)
                            <option value="{{ $type }}" @selected(($filters['type'] ?? null) === $type)>{{ $notificationCenterService->localizedTypeLabel($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_severity') }}</label>
                    <select name="severity" class="form-select">
                        <option value="">{{ __('admin.notifications_ui_all') }}</option>
                        @foreach($severityOptions as $severity)
                            <option value="{{ $severity }}" @selected(($filters['severity'] ?? null) === $severity)>{{ $notificationCenterService->localizedSeverityLabel($severity) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_module') }}</label>
                    <select name="module" class="form-select">
                        <option value="">{{ __('admin.notifications_ui_all') }}</option>
                        @foreach($moduleOptions as $module)
                            <option value="{{ $module }}" @selected(($filters['module'] ?? null) === $module)>{{ $notificationCenterService->localizedModuleLabel($module) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_from_date') }}</label>
                    <input type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_to_date') }}</label>
                    <input type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="actionable_only" name="actionable" value="1" @checked(($filters['actionable'] ?? null) === '1')>
                        <label class="form-check-label" for="actionable_only">{{ __('admin.notifications_ui_actionable_only') }}</label>
                    </div>
                </div>
                <div class="col-md-9 d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">{{ __('admin.notifications_ui_reset') }}</a>
                    <button class="btn btn-primary" type="submit">{{ __('admin.notifications_ui_filter') }}</button>
                    @if(($summary['unread'] ?? 0) > 0)
                        <button type="button" class="btn btn-outline-dark" data-notifications-read-all="{{ route('admin.notifications.read-all') }}">{{ __('admin.notifications_ui_mark_all_read') }}</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($items as $notification)
                    <div class="list-group-item {{ $notification['is_read'] ? '' : 'bg-light' }}" data-notification-item="{{ $notification['id'] }}" data-notification-state="{{ $notification['is_read'] ? 'read' : 'unread' }}">
                        <div class="d-flex flex-wrap justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                    <strong>{{ $notification['title'] }}</strong>
                                    <span class="badge text-bg-{{ $notification['severity'] }}">{{ $notification['severity_label'] }}</span>
                                    <span class="badge text-bg-light">{{ $notification['type_label'] }}</span>
                                    @if(! $notification['is_read'])
                                        <span class="badge text-bg-primary">{{ __('admin.notifications_ui_state_unread') }}</span>
                                    @endif
                                </div>
                                @if($notification['message'])
                                    <div class="text-muted mb-2">{{ $notification['message'] }}</div>
                                @endif
                                <div class="small text-muted">
                                    {{ optional($notification['created_at'])->translatedFormat('Y-m-d h:i A') }}
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                @if(! $notification['is_read'])
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-notification-read="{{ route('admin.notifications.read', $notification['id']) }}">{{ __('admin.notifications_ui_mark_read') }}</button>
                                @endif
                                @if($notification['is_actionable'])
                                    <a href="{{ $notification['url'] }}" class="btn btn-sm btn-primary">{{ $notification['action_label'] }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">{{ __('admin.notifications_ui_empty') }}</div>
                @endforelse
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection
