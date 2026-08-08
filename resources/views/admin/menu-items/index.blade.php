@extends('layouts.admin')

@section('page_title', 'إدارة القوائم والتنقل (Menu Management)')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 text-navy font-weight-bold">إدارة القوائم والتنقل (Menu Management)</h1>
        <p class="text-muted mb-0">إدارة القائمة الرئيسية وقائمة التذييل، إعادة الترتيب، الربط بالصفحات، وإنشاء القوائم المنسدلة.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.menu-items.trash') }}" class="btn btn-outline-secondary">
            🗑️ سلة المهملات
        </a>
        <a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary tw-btn-primary px-4 fw-bold">
            ➕ إضافة عنصر قائمة جديد
        </a>
    </div>
</div>

<!-- LOCATION SWITCHER TABS -->
<ul class="nav nav-pills custom-admin-tabs mb-4 bg-light p-2 rounded-3 border">
    <li class="nav-item">
        <a href="{{ route('admin.menu-items.index', ['location' => 'header']) }}" class="nav-link fw-bold {{ ($location ?? 'header') === 'header' ? 'active' : '' }}">
            🔝 القائمة الرئيسية بالهيدر (Header Menu)
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.menu-items.index', ['location' => 'footer']) }}" class="nav-link fw-bold {{ ($location ?? 'header') === 'footer' ? 'active' : '' }}">
            🔻 قائمة التذييل (Footer Menu)
        </a>
    </li>
</ul>

<div class="card admin-card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
        <h2 class="h5 mb-0 text-primary fw-bold">
            {{ ($location ?? 'header') === 'header' ? '🔝 عناصر القائمة الرئيسية بالهيدر' : '🔻 عناصر قائمة التذييل' }}
        </h2>
        <span class="badge bg-info text-dark px-3 py-2">
            إجمالي العناصر: {{ $items->count() }}
        </span>
    </div>

    @if($items->isEmpty())
        <div class="text-center py-5 text-muted">
            <div class="display-6 mb-3">📁</div>
            <p class="h6 mb-3">لا توجد عناصر في هذه القائمة بعد.</p>
            <a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary btn-sm">➕ إضافة أول عنصر الآن</a>
        </div>
    @else
        <div class="alert alert-light border small mb-4">
            💡 <strong>نصيحة:</strong> يمكنك سحب العناصر <strong>☰</strong> لإعادة الترتيب أو استخدام أسهم التحريك ⬆️ ⬇️، ويتم حفظ الترتيب تلقائياً.
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tw-menu-items-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">سحب</th>
                        <th style="width: 80px;" class="text-center">الترتيب</th>
                        <th>اسم العنصر / Menu Title</th>
                        <th>نوع الرابط / Type</th>
                        <th>الرابط المستهدف / Resolved URL</th>
                        <th style="width: 100px;">الحالة</th>
                        <th style="width: 220px;" class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="js-menu-sortable-body">
                    @foreach($items as $item)
                        <!-- PARENT ITEM -->
                        <tr class="js-menu-sortable-row bg-white border-bottom" data-id="{{ $item->id }}" data-parent="">
                            <td class="text-center cursor-move text-muted">
                                <span class="js-drag-handle fs-5 fw-bold">☰</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary js-order-badge">{{ $item->sort_order }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($item->icon)
                                        <span class="text-primary">@include('partials.frontend.icon', ['icon' => $item->icon, 'fallback' => 'link'])</span>
                                    @endif
                                    <div>
                                        <span class="fw-bold text-dark d-block mb-0">{{ $item->title_ar }}</span>
                                        <span class="text-muted small">{{ $item->title_en }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @switch($item->type)
                                    @case('page')
                                        <span class="badge bg-primary">📄 صفحة موقع</span>
                                        @break
                                    @case('section')
                                        <span class="badge bg-warning text-dark">⚓ قسم داخلي</span>
                                        @break
                                    @case('submenu')
                                        <span class="badge bg-purple text-white" style="background:#6f42c1;">📁 قائمة فرعية</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">🔗 رابط مخصص</span>
                                @endswitch
                            </td>
                            <td>
                                <small class="text-muted text-break" dir="ltr">
                                    {{ Str::limit($item->frontendUrl() ?: ($item->url ?: $item->route_name), 45) }}
                                </small>
                            </td>
                            <td>
                                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $item->is_active ? 'نشط' : 'مسودة' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary js-move-item-up" title="للأعلى">▲</button>
                                    <button type="button" class="btn btn-outline-secondary js-move-item-down" title="للأسفل">▼</button>
                                    <a href="{{ route('admin.menu-items.edit', $item) }}" class="btn btn-outline-primary" title="تعديل">تعديل</a>
                                    <form method="post" action="{{ route('admin.menu-items.duplicate', $item) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-outline-success" title="تكرار / Duplicate">📋 تكرار</button>
                                    </form>
                                    @if($item->frontendUrl() && $item->frontendUrl() !== '#')
                                        <a href="{{ $item->frontendUrl() }}" class="btn btn-outline-info" target="_blank" rel="noopener noreferrer" title="معاينة">معاينة</a>
                                    @endif
                                    <form method="post" action="{{ route('admin.menu-items.destroy', $item) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger" onclick="return confirm('هل أنت تأكد من نقل هذا العنصر إلى سلة المهملات؟')">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- CHILDREN / SUBMENU ITEMS -->
                        @foreach($item->children as $child)
                            <tr class="js-menu-sortable-row table-light border-bottom" data-id="{{ $child->id }}" data-parent="{{ $item->id }}">
                                <td class="text-center cursor-move text-muted pe-4">
                                    <span class="js-drag-handle text-secondary">↳ ☰</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border js-order-badge">{{ $child->sort_order }}</span>
                                </td>
                                <td style="padding-right: 2.5rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted">↳</span>
                                        @if($child->icon)
                                            <span class="text-primary">@include('partials.frontend.icon', ['icon' => $child->icon, 'fallback' => 'link'])</span>
                                        @endif
                                        <div>
                                            <span class="fw-semibold text-dark d-block mb-0">{{ $child->title_ar }}</span>
                                            <span class="text-muted small">{{ $child->title_en }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @switch($child->type)
                                        @case('page')
                                            <span class="badge bg-primary">📄 صفحة موقع</span>
                                            @break
                                        @case('section')
                                            <span class="badge bg-warning text-dark">⚓ قسم داخلي</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">🔗 رابط مخصص</span>
                                    @endswitch
                                </td>
                                <td>
                                    <small class="text-muted text-break" dir="ltr">
                                        {{ Str::limit($child->frontendUrl() ?: ($child->url ?: $child->route_name), 45) }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge {{ $child->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $child->is_active ? 'نشط' : 'مسودة' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary js-move-item-up" title="للأعلى">▲</button>
                                        <button type="button" class="btn btn-outline-secondary js-move-item-down" title="للأسفل">▼</button>
                                        <a href="{{ route('admin.menu-items.edit', $child) }}" class="btn btn-outline-primary" title="تعديل">تعديل</a>
                                        <form method="post" action="{{ route('admin.menu-items.duplicate', $child) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-outline-success" title="تكرار / Duplicate">📋 تكرار</button>
                                        </form>
                                        @if($child->frontendUrl() && $child->frontendUrl() !== '#')
                                            <a href="{{ $child->frontendUrl() }}" class="btn btn-outline-info" target="_blank" rel="noopener noreferrer" title="معاينة">معاينة</a>
                                        @endif
                                        <form method="post" action="{{ route('admin.menu-items.destroy', $child) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger" onclick="return confirm('هل أنت تأكد من حذف هذا العنصر الفرعي؟')">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('.js-menu-sortable-body');
    if (!tableBody) return;

    const saveOrderToBackend = () => {
        const rows = Array.from(tableBody.querySelectorAll('.js-menu-sortable-row'));
        const orderData = rows.map((row, index) => {
            const badge = row.querySelector('.js-order-badge');
            if (badge) badge.textContent = index + 1;
            return {
                id: row.getAttribute('data-id'),
                parent_id: row.getAttribute('data-parent') || null
            };
        });

        fetch("{{ route('admin.menu-items.reorder') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ order: orderData })
        })
        .then(res => res.json())
        .then(data => {
            console.log('Order saved:', data);
        })
        .catch(err => console.error('Error saving menu order:', err));
    };

    tableBody.addEventListener('click', (e) => {
        const row = e.target.closest('.js-menu-sortable-row');
        if (!row) return;

        if (e.target.classList.contains('js-move-item-up')) {
            const prev = row.previousElementSibling;
            if (prev) {
                tableBody.insertBefore(row, prev);
                saveOrderToBackend();
            }
        } else if (e.target.classList.contains('js-move-item-down')) {
            const next = row.nextElementSibling;
            if (next) {
                tableBody.insertBefore(next, row);
                saveOrderToBackend();
            }
        }
    });

    // HTML5 Drag and Drop Reordering
    let draggedRow = null;

    tableBody.querySelectorAll('.js-menu-sortable-row').forEach(row => {
        row.setAttribute('draggable', 'true');

        row.addEventListener('dragstart', (e) => {
            draggedRow = row;
            e.dataTransfer.effectAllowed = 'move';
            row.classList.add('table-warning');
        });

        row.addEventListener('dragend', () => {
            draggedRow = null;
            row.classList.remove('table-warning');
            saveOrderToBackend();
        });

        row.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (draggedRow && draggedRow !== row) {
                const rect = row.getBoundingClientRect();
                const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                tableBody.insertBefore(draggedRow, next ? row.nextSibling : row);
            }
        });
    });
});
</script>
@endsection
