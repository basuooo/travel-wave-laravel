@extends('layouts.admin')

@section('title', 'إنشاء حملة إعادة استهداف — Retargeting')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1 text-danger">🎯 Retargeting Campaign Builder</h3>
            <p class="text-muted small mb-0">إعادة التواصل مع المحادثات والعملاء السابقين لمطابقة استجابتهم حسب رقم WhatsApp المحدد</p>
        </div>
    </div>

    @include('admin.whatsapp.nav')

    <form id="retargetingForm" action="{{ route('admin.whatsapp.retargeting.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Settings & Audience Selection -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">
                        1. تفاصيل الحملة واختيار الحساب
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم الحملة</label>
                            <input type="text" name="name" class="form-control" placeholder="مثال: إعادة استهداف عملاء إسبانيا - سبتمبر" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">اختر حساب WhatsApp المرتبط (Crucial Rule)</label>
                            <select name="whatsapp_account_id" id="whatsapp_account_id" class="form-select" required>
                                <option value="">-- اختر رقم الواتساب الذي سيتم الإرسال منه --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->phone_number }})</option>
                                @endforeach
                            </select>
                            <div class="form-text text-danger small mt-1">
                                ⚠️ تذكر: يتم مطابقة الأرقام المرفوعة مع المحادثات السابقة الخاصة <strong>بنفس حساب الواتساب المحدد هنا فقط</strong>.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">رفع الأرقام أو لصقها (Upload Numbers)</label>
                            <ul class="nav nav-tabs mb-2" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabPaste" type="button">لصق أرقام / نص</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabFile" type="button">رفع ملف Excel / CSV</button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tabPaste">
                                    <textarea id="raw_numbers_input" class="form-control font-monospace" rows="5" placeholder="أدخل الأرقام هنا (رقم في كل سطر أو مفصولة بـ فاصلة):&#10;احمد | +34600112233&#10;+201012345678"></textarea>
                                </div>
                                <div class="tab-pane fade" id="tabFile">
                                    <input type="file" id="file_input" class="form-control" accept=".csv,.txt,.xlsx">
                                    <div class="form-text small">يدعم ملفات CSV و TXT المحتوية على الأرقام.</div>
                                </div>
                            </div>

                            <button type="button" id="btnMatchNumbers" class="btn btn-dark w-100 mt-3 font-weight-bold">
                                🔍 مطابقة الأرقام وفحص المحادثات (Match Contacts)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Message Builder & Scheduling -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">
                        2. نص الرسالة والإعدادات (Message & Schedule)
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اختر من القوالب الجاهزة (Templates)</label>
                            <select id="templateSelector" class="form-select mb-2">
                                <option value="">-- قالب فارغ --</option>
                                @foreach($templates as $tpl)
                                    <option value="{{ $tpl->content }}">{{ $tpl->name }} ({{ $tpl->category }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">نص الرسالة (Message Body)</label>
                            <textarea name="message_content" id="message_content" class="form-control" rows="6" required placeholder="أهلاً بك @{{name}}، أردنا متابعة استفسارك بخصوص @{{service}}..."></textarea>
                            <div class="mt-2 small text-muted">
                                المتغيرات المتاحة: 
                                <span class="badge bg-light text-dark border me-1 cursor-pointer" onclick="insertVar('&#123;&#123;name&#125;&#125;')">@{{name}}</span>
                                <span class="badge bg-light text-dark border me-1 cursor-pointer" onclick="insertVar('&#123;&#123;phone&#125;&#125;')">@{{phone}}</span>
                                <span class="badge bg-light text-dark border me-1 cursor-pointer" onclick="insertVar('&#123;&#123;country&#125;&#125;')">@{{country}}</span>
                                <span class="badge bg-light text-dark border me-1 cursor-pointer" onclick="insertVar('&#123;&#123;service&#125;&#125;')">@{{service}}</span>
                                <span class="badge bg-light text-dark border me-1 cursor-pointer" onclick="insertVar('&#123;&#123;employee&#125;&#125;')">@{{employee}}</span>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">نوع الفاصل الزمني</label>
                                <select name="interval_type" class="form-select">
                                    <option value="random">Random Delay (عشوائي)</option>
                                    <option value="fixed">Fixed Delay (ثابت)</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="form-label fw-bold">Min (sec)</label>
                                <input type="number" name="interval_min_sec" class="form-control" value="30" min="5">
                            </div>
                            <div class="col-3">
                                <label class="form-label fw-bold">Max (sec)</label>
                                <input type="number" name="interval_max_sec" class="form-control" value="90" min="5">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">توقيت الإرسال</label>
                            <select name="schedule_type" class="form-select">
                                <option value="now">تشغيل الآن فوراً (Send Now)</option>
                                <option value="scheduled">جدولة لوقت لاحق (Schedule)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Matching Table & Audience Control -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                        <span>3. جدول المطابقة والجمهور المستهدف (Contact Matching Results)</span>
                    </div>

                    <div class="card-body bg-light border-bottom">
                        <!-- Summary Bar -->
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="p-2 bg-white rounded border">
                                    <div class="small text-muted fw-bold">Total Selected</div>
                                    <div class="fs-4 fw-bold text-primary" id="cntTotalSelected">0</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 bg-white rounded border">
                                    <div class="small text-muted fw-bold">Previously Contacted</div>
                                    <div class="fs-4 fw-bold text-success" id="cntPrevContacted">0</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 bg-white rounded border">
                                    <div class="small text-muted fw-bold">Not Previously Contacted</div>
                                    <div class="fs-4 fw-bold text-warning" id="cntNotPrevContacted">0</div>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Actions Toolbar -->
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="selectAll(true)">تحديد الكل</button>
                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="selectAll(false)">إلغاء تحديد الكل</button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="selectByStatus('previously_contacted')">تحديد Previous Only</button>
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="selectByStatus('not_previously_contacted')">تحديد Not Previous Only</button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px;">
                            <table class="table align-middle mb-0" id="matchingTable">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="40"><input type="checkbox" id="chkHeaderSelect" checked onclick="toggleHeaderCheck(this)"></th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Contact Status</th>
                                        <th>Last Contact</th>
                                        <th>Select Option</th>
                                    </tr>
                                </thead>
                                <tbody id="matchingTbody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            الرجاء اختيار حساب الواتساب ثم الضغط على "مطابقة الأرقام وفحص المحادثات" لعرض نتائج الجدول هنا.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-white text-end p-3">
                        <button type="submit" class="btn btn-danger btn-lg font-weight-bold px-4" id="btnSubmitCampaign">
                            🚀 تأكيد وإطلاق حملة الـ Retargeting
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let currentMatchedRecords = [];

document.getElementById('templateSelector').addEventListener('change', function() {
    if(this.value) {
        document.getElementById('message_content').value = this.value;
    }
});

function insertVar(varName) {
    const area = document.getElementById('message_content');
    area.value += varName;
}

document.getElementById('btnMatchNumbers').addEventListener('click', function() {
    const accountId = document.getElementById('whatsapp_account_id').value;
    if(!accountId) {
        alert('يرجى اختيار حساب WhatsApp المرتبط أولاً قبل مطابقة الأرقام!');
        return;
    }

    const rawNumbers = document.getElementById('raw_numbers_input').value;
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = 'جاري مطابقة المحادثات السابقة...';

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('whatsapp_account_id', accountId);
    formData.append('raw_numbers', rawNumbers);

    const fileInput = document.getElementById('file_input');
    if(fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
    }

    fetch('{{ route("admin.whatsapp.retargeting.match-numbers") }}', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '🔍 مطابقة الأرقام وفحص المحادثات (Match Contacts)';
        currentMatchedRecords = data.records || [];
        renderMatchingTable(currentMatchedRecords);
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '🔍 مطابقة الأرقام وفحص المحادثات (Match Contacts)';
        alert('حدث خطأ أثناء فحص الأرقام: ' + err.message);
    });
});

function renderMatchingTable(records) {
    const tbody = document.getElementById('matchingTbody');
    tbody.innerHTML = '';

    if(records.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">لم يتم العثور على أرقام للمطابقة.</td></tr>`;
        updateCounters();
        return;
    }

    records.forEach((rec, idx) => {
        const isPrev = rec.contact_status === 'previously_contacted';
        const badgeClass = isPrev ? 'bg-success' : 'bg-warning text-dark';
        const statusText = isPrev ? 'Previously Contacted' : 'Not Previously Contacted';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="checkbox" class="chk-row" data-idx="${idx}" name="recipients[${idx}][is_selected]" value="1" ${rec.is_selected ? 'checked' : ''} onchange="updateCounters()">
                <input type="hidden" name="recipients[${idx}][phone]" value="${rec.phone}">
                <input type="hidden" name="recipients[${idx}][name]" value="${rec.name}">
                <input type="hidden" name="recipients[${idx}][status]" value="${rec.contact_status}">
            </td>
            <td class="fw-bold">${rec.name}</td>
            <td class="font-monospace text-primary">${rec.phone}</td>
            <td>
                <span class="badge ${badgeClass}">${statusText}</span>
            </td>
            <td class="small text-muted">${rec.last_contact_at || '-'}</td>
            <td>
                <span class="small ${isPrev ? 'text-success' : 'text-muted'}">
                    ${isPrev ? 'تواصل سابق من هذا الحساب' : 'رقم جديد (تضمين بالقرار)'}
                </span>
            </td>
        `;
        tbody.appendChild(tr);
    });

    updateCounters();
}

function updateCounters() {
    const checkboxes = document.querySelectorAll('.chk-row');
    let totalSelected = 0;
    let prevCount = 0;
    let notPrevCount = 0;

    checkboxes.forEach((chk, idx) => {
        if(chk.checked && currentMatchedRecords[idx]) {
            totalSelected++;
            if(currentMatchedRecords[idx].contact_status === 'previously_contacted') {
                prevCount++;
            } else {
                notPrevCount++;
            }
        }
    });

    document.getElementById('cntTotalSelected').innerText = totalSelected;
    document.getElementById('cntPrevContacted').innerText = prevCount;
    document.getElementById('cntNotPrevContacted').innerText = notPrevCount;
}

function selectAll(val) {
    document.querySelectorAll('.chk-row').forEach(chk => chk.checked = val);
    updateCounters();
}

function selectByStatus(statusName) {
    document.querySelectorAll('.chk-row').forEach((chk, idx) => {
        if(currentMatchedRecords[idx]) {
            chk.checked = (currentMatchedRecords[idx].contact_status === statusName);
        }
    });
    updateCounters();
}

function toggleHeaderCheck(masterChk) {
    selectAll(masterChk.checked);
}
</script>
@endsection
