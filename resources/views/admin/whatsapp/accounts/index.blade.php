@extends('layouts.admin')

@section('title', 'إدارة أرقام WhatsApp')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">WhatsApp Accounts</h3>
            <p class="text-muted small mb-0">إدارة أرقام الواتساب المرتبطة بالنظام وتحديد الموظف المسؤول</p>
        </div>
        <button class="btn btn-primary font-weight-bold" data-bs-toggle="modal" data-bs-target="#createAccountModal">
            ➕ إضافة رقم جديد
        </button>
    </div>

    @include('admin.whatsapp.nav')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الرقم</th>
                            <th>رقم الهاتف</th>
                            <th>حالة الاتصال</th>
                            <th>نوع الاستخدام</th>
                            <th>الموظف المسؤول</th>
                            <th>القسم / الفرع</th>
                            <th>المحادثات</th>
                            <th>مرسل / فاشل</th>
                            <th>آخر اتصال</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $acc)
                            @php
                                $settings = $acc->connection_settings ?: [];
                                $hasMeta = !empty($settings['phone_number_id']) && !empty($settings['access_token']);
                            @endphp
                            <tr>
                                <td>{{ $acc->id }}</td>
                                <td class="fw-bold">{{ $acc->name }}</td>
                                <td>
                                    <span class="font-monospace text-primary fw-bold">{{ $acc->phone_number }}</span>
                                </td>
                                <td>
                                    @if($acc->status === 'connected')
                                        <span class="badge bg-success fs-6">🟢 Connected</span>
                                    @else
                                        <span class="badge bg-danger fs-6">🔴 Disconnected</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ strtoupper($acc->usage_type) }}</span>
                                </td>
                                <td>{{ $acc->assignedUser?->name ?? 'غير معين' }}</td>
                                <td>{{ $acc->department_branch ?? 'الرئيسي' }}</td>
                                <td><span class="badge bg-info text-dark">{{ $acc->conversations_count }}</span></td>
                                <td>
                                    <span class="text-success font-monospace fw-bold">{{ $acc->sent_count }}</span> / 
                                    <span class="text-danger font-monospace fw-bold">{{ $acc->failed_count }}</span>
                                </td>
                                <td class="small text-muted">{{ $acc->last_connected_at ? $acc->last_connected_at->format('Y-m-d H:i') : '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <form action="{{ route('admin.whatsapp.accounts.toggle-connect', $acc->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @if($acc->status === 'connected')
                                                <button class="btn btn-outline-warning" title="إيقاف الاتصال">Disconnect</button>
                                            @else
                                                <button class="btn btn-outline-success" title="تفعيل الاتصال">Connect</button>
                                            @endif
                                        </form>

                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#qrModal{{ $acc->id }}" onclick="loadLiveQr({{ $acc->id }})" title="ربط / QR Code">
                                            📱 ربط / QR
                                        </button>

                                        <a href="{{ route('admin.whatsapp.conversations.index', ['account_id' => $acc->id]) }}" class="btn btn-outline-info" title="المحادثات">
                                            💬
                                        </a>

                                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editAccountModal{{ $acc->id }}" title="تعديل">
                                            ✏️
                                        </button>

                                        <form action="{{ route('admin.whatsapp.accounts.destroy', $acc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من حذف هذا الرقم؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger" title="حذف">🗑️</button>
                                        </form>
                                    </div>

                                    <!-- Modal Connection & QR Code -->
                                    <div class="modal fade" id="qrModal{{ $acc->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">إعدادات الربط والـ QR Code — {{ $acc->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    
                                                    <!-- Methods Nav Tabs -->
                                                    <ul class="nav nav-pills nav-justified mb-3" role="tablist">
                                                        <li class="nav-item">
                                                            <button class="nav-link active fw-bold" id="qr-tab-{{ $acc->id }}" data-bs-toggle="tab" data-bs-target="#qr-pane-{{ $acc->id }}" type="button">
                                                                📱 مسح الـ QR Code (WhatsApp Web)
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button class="nav-link fw-bold" id="meta-tab-{{ $acc->id }}" data-bs-toggle="tab" data-bs-target="#meta-pane-{{ $acc->id }}" type="button">
                                                                ⚡ Meta Cloud API (الربط المباشر)
                                                            </button>
                                                        </li>
                                                    </ul>

                                                    <div class="tab-content border rounded p-3 bg-light">
                                                        <!-- Pane 1: QR Code -->
                                                        <div class="tab-pane fade show active text-center" id="qr-pane-{{ $acc->id }}">
                                                            <div id="qr-loading-{{ $acc->id }}" class="py-4">
                                                                <div class="spinner-border text-primary" role="status"></div>
                                                                <p class="text-muted mt-2 small">جاري توليد كود الربط الحركي...</p>
                                                            </div>
                                                            <div id="qr-container-{{ $acc->id }}" class="d-none">
                                                                <div class="p-3 bg-white d-inline-block rounded shadow-sm border mb-2 position-relative">
                                                                    <img id="qr-img-{{ $acc->id }}" src="" alt="QR Code" width="220" height="220">
                                                                </div>
                                                                <div class="small font-monospace text-muted mb-2">
                                                                    Session Token: <span id="token-display-{{ $acc->id }}" class="fw-bold text-dark"></span>
                                                                </div>
                                                                <div class="d-flex justify-content-center gap-2 mb-3">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadLiveQr({{ $acc->id }})">
                                                                        🔄 تحديث الكود الآن (Refresh QR)
                                                                    </button>
                                                                </div>
                                                                <div class="alert alert-info py-2 small mb-0 text-start">
                                                                    <strong>📌 طريقة الربط عبر الموبايل:</strong>
                                                                    <ol class="mb-0 ps-3">
                                                                        <li>افتح تطبيق WhatsApp على هاتفك.</li>
                                                                        <li>اضغط على الإعدادات أو الثلاث نقاط ⬅️ <strong>"الأجهزة المرتبطة (Linked Devices)"</strong>.</li>
                                                                        <li>اختر <strong>"ربط جهاز"</strong> ووجه الكاميرا نحو الشاشة لمسح الـ QR.</li>
                                                                    </ol>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Pane 2: Meta Cloud API -->
                                                        <div class="tab-pane fade text-start" id="meta-pane-{{ $acc->id }}">
                                                            <h6 class="fw-bold text-primary mb-2">ربط الحساب عبر Meta Cloud API (رسمي ومباشر بدون QR):</h6>
                                                            <p class="text-muted small">هذه الطريقة تعتمد على بيانات WhatsApp Business API الرسمية من فيسبوك لضمان وصول الرسائل والحملات بدون انقطاع.</p>
                                                            
                                                            <form action="{{ route('admin.whatsapp.accounts.update', $acc->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="name" value="{{ $acc->name }}">
                                                                <input type="hidden" name="phone_number" value="{{ $acc->phone_number }}">
                                                                <input type="hidden" name="usage_type" value="{{ $acc->usage_type }}">
                                                                
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small">Meta Phone Number ID</label>
                                                                    <input type="text" name="phone_number_id" class="form-control font-monospace" value="{{ $settings['phone_number_id'] ?? '' }}" placeholder="مثال: 104829384920485">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small">Permanent Access Token</label>
                                                                    <textarea name="access_token" class="form-control font-monospace" rows="2" placeholder="Bearer Token من Meta...">{{ $settings['access_token'] ?? '' }}</textarea>
                                                                </div>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <button type="submit" class="btn btn-sm btn-success fw-bold">حفظ بيانات Meta والربط المباشر</button>
                                                                    @if($hasMeta)
                                                                        <span class="badge bg-success">✅ بيانات Meta مكتملة</span>
                                                                    @else
                                                                        <span class="badge bg-warning text-dark">⚠️ لم يتم حفظ Token بعد</span>
                                                                    @endif
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Edit Account -->
                                    <div class="modal fade" id="editAccountModal{{ $acc->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('admin.whatsapp.accounts.update', $acc->id) }}" method="POST" class="modal-content">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تعديل بيانات الرقم — {{ $acc->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">اسم الرقم في النظام</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $acc->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">رقم الهاتف</label>
                                                        <input type="text" name="phone_number" class="form-control" value="{{ $acc->phone_number }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">نوع الاستخدام</label>
                                                        <select name="usage_type" class="form-select" required>
                                                            <option value="both" {{ $acc->usage_type === 'both' ? 'selected' : '' }}>Both (Retargeting & Bulk)</option>
                                                            <option value="retargeting" {{ $acc->usage_type === 'retargeting' ? 'selected' : '' }}>Retargeting Only</option>
                                                            <option value="bulk" {{ $acc->usage_type === 'bulk' ? 'selected' : '' }}>Bulk Campaigns Only</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">الموظف المسؤول</label>
                                                        <select name="assigned_user_id" class="form-select">
                                                            <option value="">-- اختر موظف --</option>
                                                            @foreach($employees as $emp)
                                                                <option value="{{ $emp->id }}" {{ $acc->assigned_user_id == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">القسم / الفرع</label>
                                                        <input type="text" name="department_branch" class="form-control" value="{{ $acc->department_branch }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Meta Phone Number ID</label>
                                                        <input type="text" name="phone_number_id" class="form-control" value="{{ $settings['phone_number_id'] ?? '' }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Permanent Access Token</label>
                                                        <textarea name="access_token" class="form-control" rows="2">{{ $settings['access_token'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary font-weight-bold">تحديث البيانات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">لا توجد أرقام واتساب مضافة حالياً. اضغط "إضافة رقم جديد" للبدء.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Account -->
<div class="modal fade" id="createAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.whatsapp.accounts.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">إضافة رقم WhatsApp جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">اسم الرقم في النظام</label>
                    <input type="text" name="name" class="form-control" placeholder="مثال: واتساب خدمة المبيعات - مصر" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">رقم الهاتف</label>
                    <input type="text" name="phone_number" class="form-control" placeholder="مثال: +201012345678" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">نوع الاستخدام (Usage Type)</label>
                    <select name="usage_type" class="form-select" required>
                        <option value="both">Both (Retargeting & Bulk)</option>
                        <option value="retargeting">Retargeting Only</option>
                        <option value="bulk">Bulk Campaigns Only</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">الموظف المسؤول</label>
                    <select name="assigned_user_id" class="form-select">
                        <option value="">-- اختر موظف --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">القسم / الفرع</label>
                    <input type="text" name="department_branch" class="form-control" placeholder="مثال: فرع القاهرة / مبيعات الفيزا">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Meta Phone Number ID (اختياري / للربط المباشر)</label>
                    <input type="text" name="phone_number_id" class="form-control" placeholder="Phone Number ID">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Permanent Access Token (اختياري)</label>
                    <textarea name="access_token" class="form-control" rows="2" placeholder="Bearer Token..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary font-weight-bold">حفظ وإضافة</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function loadLiveQr(accId) {
    const loadingEl = document.getElementById(`qr-loading-${accId}`);
    const containerEl = document.getElementById(`qr-container-${accId}`);
    const imgEl = document.getElementById(`qr-img-${accId}`);
    const tokenEl = document.getElementById(`token-display-${accId}`);

    if (!loadingEl || !containerEl) return;

    loadingEl.classList.remove('d-none');
    containerEl.classList.add('d-none');

    fetch(`/admin/whatsapp/accounts/${accId}/qr`)
        .then(res => res.json())
        .then(data => {
            loadingEl.classList.add('d-none');
            containerEl.classList.remove('d-none');

            if (data.status === 'success') {
                if (data.qr) {
                    imgEl.src = data.qr;
                } else if (data.qr_url) {
                    imgEl.src = data.qr_url;
                }
                if (tokenEl && data.token_session) {
                    tokenEl.innerText = data.token_session;
                }
            }
        })
        .catch(err => {
            loadingEl.classList.add('d-none');
            containerEl.classList.remove('d-none');
            console.error(err);
        });
}
</script>
@endpush
@endsection
