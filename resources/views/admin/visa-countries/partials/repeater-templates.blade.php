<template id="repeater-template-detail-highlights">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Title EN</label>
                    <input class="form-control" data-field="title_en" type="text">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Title AR</label>
                    <input class="form-control text-end" dir="rtl" data-field="title_ar" type="text">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Description EN</label>
                    <textarea class="form-control" data-field="description_en" rows="3"></textarea>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Description AR</label>
                    <textarea class="form-control text-end" dir="rtl" data-field="description_ar" rows="3"></textarea>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Order</label>
                    <input class="form-control" data-field="sort_order" type="number">
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4 pt-2">
                        <input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Card Image</label>
                    <input class="form-control" data-field="image_file" data-highlight-image-input data-media-target-field="existing_image" data-media-enhanced="1" type="file" accept="image/*">
                    <div class="admin-media-picker">
                        <div class="admin-media-picker__actions">
                            <button type="button" class="btn btn-outline-secondary btn-sm js-open-media-library">Select from Library</button>
                            <span class="admin-media-picker__hint">or upload new</span>
                        </div>
                        <div class="admin-media-picker__selected"></div>
                    </div>
                    <input data-field="existing_image" type="hidden" value="">
                    <img src="" alt="" class="img-fluid rounded border mt-3 js-highlight-preview d-none" style="max-height: 160px; object-fit: cover;">
                    <div class="form-check mt-2">
                        <input class="form-check-input" data-field="remove_image" type="checkbox" value="1">
                        <label class="form-check-label">Remove current image</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger w-100" data-repeater-remove>Remove</button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-intro-points">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Bullet EN</label>
                    <input class="form-control" name="introduction_points_en[]" value="">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Bullet AR</label>
                    <input class="form-control text-end" dir="rtl" name="introduction_points_ar[]" value="">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger w-100" data-repeater-remove>Remove</button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-quick-summary">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Card Label EN</label><input class="form-control" data-field="label_en" type="text"></div>
                <div class="col-md-3"><label class="form-label">Card Label AR</label><input class="form-control text-end" dir="rtl" data-field="label_ar" type="text"></div>
                <div class="col-md-2"><label class="form-label">Value EN</label><input class="form-control" data-field="value_en" type="text"></div>
                <div class="col-md-2"><label class="form-label">Value AR</label><input class="form-control text-end" dir="rtl" data-field="value_ar" type="text"></div>
                <div class="col-md-2">
                    <label class="form-label d-flex align-items-center gap-2">
                        <span>Icon</span>
                        <a href="https://icon-sets.iconify.design/" target="_blank" rel="noopener noreferrer" class="small text-decoration-none" aria-label="Browse Iconify icons"><span aria-hidden="true">&#127760;</span></a>
                    </label>
                    <input class="form-control" data-field="icon" type="text" placeholder="material-symbols:travel">
                    <div class="form-text">Example: material-symbols:travel</div>
                </div>
                <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-2"><div class="form-check mt-4 pt-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div></div>
                <div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-outline-danger w-100" data-repeater-remove>Remove</button></div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-why-choose">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" data-field="title_en" type="text"></div>
                <div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" data-field="title_ar" type="text"></div>
                <div class="col-md-4"><label class="form-label">Icon Keyword</label><input class="form-control" data-field="icon" type="text" placeholder="shield, file, calendar, support"></div>
                <div class="col-md-5"><label class="form-label">Description EN</label><textarea class="form-control" data-field="description_en" rows="3"></textarea></div>
                <div class="col-md-5"><label class="form-label">Description AR</label><textarea class="form-control text-end" dir="rtl" data-field="description_ar" rows="3"></textarea></div>
                <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-1 d-flex flex-column justify-content-end"><div class="form-check mb-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div><button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button></div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-documents">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Document Name EN</label><input class="form-control" data-field="name_en" type="text"></div>
                <div class="col-md-4"><label class="form-label">Document Name AR</label><input class="form-control text-end" dir="rtl" data-field="name_ar" type="text"></div>
                <div class="col-md-2"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-2 d-flex flex-column justify-content-end"><div class="form-check mb-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div><button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button></div>
                <div class="col-md-6"><label class="form-label">Description EN</label><textarea class="form-control" data-field="description_en" rows="3"></textarea></div>
                <div class="col-md-6"><label class="form-label">Description AR</label><textarea class="form-control text-end" dir="rtl" data-field="description_ar" rows="3"></textarea></div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-steps">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Step Title EN</label><input class="form-control" data-field="title_en" type="text"></div>
                <div class="col-md-4"><label class="form-label">Step Title AR</label><input class="form-control text-end" dir="rtl" data-field="title_ar" type="text"></div>
                <div class="col-md-1"><label class="form-label">Step</label><input class="form-control" data-field="step_number" type="number"></div>
                <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-2 d-flex flex-column justify-content-end"><div class="form-check mb-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div><button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button></div>
                <div class="col-md-6"><label class="form-label">Description EN</label><textarea class="form-control" data-field="description_en" rows="3"></textarea></div>
                <div class="col-md-6"><label class="form-label">Description AR</label><textarea class="form-control text-end" dir="rtl" data-field="description_ar" rows="3"></textarea></div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-faq">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-5"><label class="form-label">Question EN</label><input class="form-control" data-field="question_en" type="text"></div>
                <div class="col-md-5"><label class="form-label">Question AR</label><input class="form-control text-end" dir="rtl" data-field="question_ar" type="text"></div>
                <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-1 d-flex flex-column justify-content-end"><div class="form-check mb-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div><button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button></div>
                <div class="col-md-6"><label class="form-label">Answer EN</label><textarea class="form-control" data-field="answer_en" rows="3"></textarea></div>
                <div class="col-md-6"><label class="form-label">Answer AR</label><textarea class="form-control text-end" dir="rtl" data-field="answer_ar" rows="3"></textarea></div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-fees">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Label EN</label>
                    <input class="form-control" data-field="label_en" type="text">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Label AR</label>
                    <input class="form-control text-end" dir="rtl" data-field="label_ar" type="text">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Value EN</label>
                    <input class="form-control" data-field="value_en" type="text">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Value AR</label>
                    <input class="form-control text-end" dir="rtl" data-field="value_ar" type="text">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Order</label>
                    <input class="form-control" data-field="sort_order" type="number">
                </div>
                <div class="col-md-1 d-flex flex-column justify-content-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button>
                </div>
            </div>
        </div>
    </div>
</template>

@php
    $repeaterConfigs = [
        'detail-highlights' => ['name' => 'highlight_items', 'fields' => ['title_en', 'title_ar', 'description_en', 'description_ar', 'image_file', 'existing_image', 'remove_image', 'sort_order', 'is_active']],
        'quick-summary' => ['name' => 'quick_summary_items', 'fields' => ['label_en', 'label_ar', 'value_en', 'value_ar', 'icon', 'sort_order', 'is_active']],
        'why-choose' => ['name' => 'why_choose_items', 'fields' => ['title_en', 'title_ar', 'description_en', 'description_ar', 'icon', 'sort_order', 'is_active']],
        'documents' => ['name' => 'document_items', 'fields' => ['name_en', 'name_ar', 'description_en', 'description_ar', 'sort_order', 'is_active']],
        'steps' => ['name' => 'step_items', 'fields' => ['title_en', 'title_ar', 'description_en', 'description_ar', 'step_number', 'sort_order', 'is_active']],
        'faq' => ['name' => 'faq_items', 'fields' => ['question_en', 'question_ar', 'answer_en', 'answer_ar', 'sort_order', 'is_active']],
        'fees' => ['name' => 'fee_items', 'fields' => ['label_en', 'label_ar', 'value_en', 'value_ar', 'sort_order', 'is_active']],
    ];
@endphp

<script>
const repeaterConfig = @json($repeaterConfigs);

document.addEventListener('click', function (event) {
    const addButton = event.target.closest('[data-repeater-add]');
    const removeButton = event.target.closest('[data-repeater-remove]');

    if (addButton) {
        const key = addButton.getAttribute('data-repeater-add');
        const list = document.querySelector('[data-repeater-list="' + key + '"]');
        const template = document.getElementById('repeater-template-' + key);

        if (!list || !template) {
            return;
        }

        const clone = template.content.firstElementChild.cloneNode(true);
        list.appendChild(clone);
        syncRepeaterNames(key);
    }

    if (removeButton) {
        const item = removeButton.closest('[data-repeater-item]');
        const list = removeButton.closest('[data-repeater-list]');

        if (item) {
            item.remove();
        }

        if (list) {
            syncRepeaterNames(list.getAttribute('data-repeater-list'));
        }
    }
});

function syncRepeaterNames(key) {
    const list = document.querySelector('[data-repeater-list="' + key + '"]');
    if (!list) {
        return;
    }

    list.querySelectorAll('[data-repeater-item]').forEach(function (item, index) {
        const inputs = item.querySelectorAll('input, textarea, select');
        inputs.forEach(function (input) {
            const field = input.getAttribute('data-field');
            const base = repeaterConfig[key] ? repeaterConfig[key].name : null;

            if (field && base) {
                input.name = `${base}[${index}][${field}]`;
            }
        });
    });
}

['detail-highlights', 'quick-summary', 'why-choose', 'documents', 'steps', 'faq', 'fees'].forEach(syncRepeaterNames);

document.addEventListener('change', function (event) {
    const input = event.target.closest('[data-highlight-image-input]');

    if (!input) {
        return;
    }

    const preview = input.closest('[data-repeater-item]')?.querySelector('.js-highlight-preview');
    const removeCheckbox = input.closest('[data-repeater-item]')?.querySelector('[data-field="remove_image"]');

    if (!preview || !input.files || !input.files.length) {
        return;
    }

    const reader = new FileReader();
    reader.onload = function (loadEvent) {
        preview.src = loadEvent.target?.result || '';
        preview.classList.remove('d-none');
    };
    reader.readAsDataURL(input.files[0]);

    if (removeCheckbox) {
        removeCheckbox.checked = false;
    }
});
</script>
