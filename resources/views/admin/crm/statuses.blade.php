@extends('layouts.admin')

@section('page_title', __('admin.crm_statuses'))
@section('page_description', __('admin.crm_statuses_desc'))

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.create') }} {{ __('admin.status') }}</h2>
            <form method="post" action="{{ route('admin.crm.statuses.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6"><label class="form-label">EN</label><input class="form-control" name="name_en" required></div>
                <div class="col-md-6"><label class="form-label">AR</label><input class="form-control text-end" dir="rtl" name="name_ar" required></div>
                <div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug"></div>
                <div class="col-md-3"><label class="form-label">{{ __('admin.sort_order') }}</label><input type="number" class="form-control" name="sort_order" value="0"></div>
                <div class="col-md-3"><label class="form-label">{{ __('admin.color') }}</label><input class="form-control" name="color" placeholder="warning"></div>
                <div class="col-12"><button class="btn btn-primary">{{ __('admin.create') }}</button></div>
            </form>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.crm_status_map') }}</h2>
            <div class="d-grid gap-2">
                @foreach($statusMap as $row)
                    <div class="d-flex justify-content-between align-items-center border rounded-4 px-3 py-2 @if(!$row['status']->is_active) bg-light text-muted @endif">
                        <div class="d-flex align-items-center gap-2">
                            <span>{{ $row['status']->localizedName() }}</span>
                            @if(!$row['status']->is_active)
                                <span class="badge bg-secondary-subtle text-secondary small">غير مفعّل</span>
                            @endif
                        </div>
                        <strong>{{ $row['count'] }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card admin-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">{{ __('admin.crm_statuses') }}</h2>
                <span class="text-muted small">
                    <i class="bi bi-arrows-move me-1"></i> اسحب الحالات لإعادة ترتيبها
                </span>
            </div>
            @if(session('error'))
                <div class="alert alert-danger py-2 px-3 mb-3 d-flex align-items-center small">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success py-2 px-3 mb-3 d-flex align-items-center small">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            <div id="status-reorder-alert" class="alert alert-success py-2 px-3 mb-3 d-none align-items-center small">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span id="status-reorder-alert-msg">تم تحديث ترتيب الحالات بنجاح.</span>
            </div>
            <div class="d-grid gap-3 js-status-sortable-container">
                @foreach($statuses as $status)
                    <div class="border rounded-4 p-3 @if(!$status->is_active) bg-light-subtle opacity-75 @else bg-white @endif js-status-sortable-row" data-id="{{ $status->id }}" draggable="true" style="transition: transform 0.15s ease, box-shadow 0.15s ease;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="drag-handle text-muted cursor-grab fs-5" style="cursor: grab;" title="اسحب للتغيير">
                                    <i class="bi bi-grip-vertical"></i>
                                </span>
                                <span class="badge bg-primary text-white rounded-pill px-3 py-1 fs-6 js-order-badge">
                                    #{{ $loop->iteration }}
                                </span>
                                <span class="fw-bold text-dark fs-6">{{ $status->localizedName() }}</span>
                                @if(!$status->is_active)
                                    <span class="badge bg-secondary text-white rounded-pill ms-1">غير مفعّل</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2 ms-auto">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary js-move-up" title="تحريك لأعلى">
                                        <i class="bi bi-chevron-up"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary js-move-down" title="تحريك لأسفل">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                                @if(!$status->is_system)
                                    <form method="post" action="{{ route('admin.crm.statuses.destroy', $status) }}" class="d-inline" onsubmit="return confirm('هل أنت تأكد من رغبتك في حذف هذه الحالة نهائياً؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="حذف الحالة">
                                            <i class="bi bi-trash me-1"></i> {{ __('admin.delete') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-light text-muted border" title="حالة خاصة بالنظام">حالة نظام</span>
                                @endif
                            </div>
                        </div>
                        <form method="post" action="{{ route('admin.crm.statuses.update', $status) }}" class="row g-2 align-items-end">
                            @csrf @method('PUT')
                            <input type="hidden" name="sort_order" class="js-sort-order-input" value="{{ $status->sort_order }}">
                            <div class="col-md-4"><label class="form-label small text-muted">اسم الحالة (EN)</label><input class="form-control" name="name_en" value="{{ $status->name_en }}"></div>
                            <div class="col-md-4"><label class="form-label small text-muted">اسم الحالة (AR)</label><input class="form-control text-end" dir="rtl" name="name_ar" value="{{ $status->name_ar }}"></div>
                            <div class="col-md-4"><label class="form-label small text-muted">اللون (Color)</label><input class="form-control" name="color" value="{{ $status->color }}"></div>
                            <div class="col-md-4">
                                <div class="form-check mt-2">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="status-{{ $status->id }}" @checked($status->is_active)>
                                    <label class="form-check-label" for="status-{{ $status->id }}">{{ __('admin.active') }}</label>
                                </div>
                            </div>
                            <div class="col-md-4 text-muted small d-flex align-items-center">
                                <code>{{ $status->slug }}</code>
                            </div>
                            <div class="col-md-4"><button class="btn btn-outline-primary w-100">{{ __('admin.update') }}</button></div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .js-status-sortable-row.dragging {
        opacity: 0.5;
        border: 2px dashed #0d6efd !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .cursor-grab {
        cursor: grab;
    }
    .cursor-grab:active {
        cursor: grabbing;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.js-status-sortable-container');
    if (!container) return;

    const alertBox = document.getElementById('status-reorder-alert');
    const alertMsg = document.getElementById('status-reorder-alert-msg');

    const showAlert = (message) => {
        if (!alertBox) return;
        if (alertMsg) alertMsg.textContent = message;
        alertBox.classList.remove('d-none');
        alertBox.classList.add('d-flex');
        setTimeout(() => {
            alertBox.classList.add('d-none');
            alertBox.classList.remove('d-flex');
        }, 3000);
    };

    const saveOrderToBackend = () => {
        const rows = Array.from(container.querySelectorAll('.js-status-sortable-row'));
        const orderData = rows.map((row, index) => {
            const badge = row.querySelector('.js-order-badge');
            if (badge) badge.textContent = '#' + (index + 1);

            const input = row.querySelector('.js-sort-order-input');
            if (input) input.value = index + 1;

            return {
                id: row.getAttribute('data-id')
            };
        });

        fetch("{{ route('admin.crm.statuses.reorder') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ order: orderData })
        })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message || 'تم تحديث ترتيب الحالات بنجاح.');
        })
        .catch(err => console.error('Error saving status order:', err));
    };

    // Up / Down Buttons
    container.addEventListener('click', (e) => {
        const btnUp = e.target.closest('.js-move-up');
        const btnDown = e.target.closest('.js-move-down');
        
        if (btnUp) {
            const row = btnUp.closest('.js-status-sortable-row');
            const prev = row.previousElementSibling;
            if (prev && prev.classList.contains('js-status-sortable-row')) {
                container.insertBefore(row, prev);
                saveOrderToBackend();
            }
        } else if (btnDown) {
            const row = btnDown.closest('.js-status-sortable-row');
            const next = row.nextElementSibling;
            if (next && next.classList.contains('js-status-sortable-row')) {
                container.insertBefore(next, row);
                saveOrderToBackend();
            }
        }
    });

    // HTML5 Drag & Drop
    let draggedRow = null;

    container.querySelectorAll('.js-status-sortable-row').forEach(row => {
        row.addEventListener('dragstart', (e) => {
            draggedRow = row;
            e.dataTransfer.effectAllowed = 'move';
            row.classList.add('dragging');
        });

        row.addEventListener('dragend', () => {
            draggedRow = null;
            row.classList.remove('dragging');
            saveOrderToBackend();
        });

        row.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (draggedRow && draggedRow !== row) {
                const rect = row.getBoundingClientRect();
                const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                container.insertBefore(draggedRow, next ? row.nextSibling : row);
            }
        });
    });
});
</script>
@endsection
