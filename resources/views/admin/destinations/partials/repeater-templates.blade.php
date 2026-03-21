<template id="repeater-template-about-points">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Point EN</label>
                    <input class="form-control" name="about_points_en[]" value="">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Point AR</label>
                    <input class="form-control text-end" dir="rtl" name="about_points_ar[]" value="">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger w-100" data-repeater-remove>Remove</button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-quick-info">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Label EN</label><input class="form-control" data-field="label_en" type="text"></div>
                <div class="col-md-3"><label class="form-label">Label AR</label><input class="form-control text-end" dir="rtl" data-field="label_ar" type="text"></div>
                <div class="col-md-2"><label class="form-label">Value EN</label><input class="form-control" data-field="value_en" type="text"></div>
                <div class="col-md-2"><label class="form-label">Value AR</label><input class="form-control text-end" dir="rtl" data-field="value_ar" type="text"></div>
                <div class="col-md-1"><label class="form-label">Icon</label><input class="form-control" data-field="icon" type="text"></div>
                <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-2"><div class="form-check mt-4 pt-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div></div>
                <div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-outline-danger w-100" data-repeater-remove>Remove</button></div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-highlights">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" data-field="title_en" type="text"></div>
                <div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" data-field="title_ar" type="text"></div>
                <div class="col-md-2"><label class="form-label">Icon</label><input class="form-control" data-field="icon" type="text"></div>
                <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-1 d-flex flex-column justify-content-end"><div class="form-check mb-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div><button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button></div>
                <div class="col-md-6"><label class="form-label">Description EN</label><textarea class="form-control" data-field="description_en" rows="3"></textarea></div>
                <div class="col-md-6"><label class="form-label">Description AR</label><textarea class="form-control text-end" dir="rtl" data-field="description_ar" rows="3"></textarea></div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-services">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" data-field="title_en" type="text"></div>
                <div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" data-field="title_ar" type="text"></div>
                <div class="col-md-2"><label class="form-label">Icon</label><input class="form-control" data-field="icon" type="text"></div>
                <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-1 d-flex flex-column justify-content-end"><div class="form-check mb-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div><button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button></div>
                <div class="col-md-6"><label class="form-label">Description EN</label><textarea class="form-control" data-field="description_en" rows="3"></textarea></div>
                <div class="col-md-6"><label class="form-label">Description AR</label><textarea class="form-control text-end" dir="rtl" data-field="description_ar" rows="3"></textarea></div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-documents">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" data-field="title_en" type="text"></div>
                <div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" data-field="title_ar" type="text"></div>
                <div class="col-md-2"><label class="form-label">Icon</label><input class="form-control" data-field="icon" type="text"></div>
                <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-1 d-flex flex-column justify-content-end"><div class="form-check mb-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div><button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button></div>
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
                <div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" data-field="title_en" type="text"></div>
                <div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" data-field="title_ar" type="text"></div>
                <div class="col-md-1"><label class="form-label">Step</label><input class="form-control" data-field="step_number" type="number"></div>
                <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-2"><label class="form-label">Icon</label><input class="form-control" data-field="icon" type="text"></div>
                <div class="col-md-6"><label class="form-label">Description EN</label><textarea class="form-control" data-field="description_en" rows="3"></textarea></div>
                <div class="col-md-6"><label class="form-label">Description AR</label><textarea class="form-control text-end" dir="rtl" data-field="description_ar" rows="3"></textarea></div>
                <div class="col-md-2 d-flex flex-column justify-content-end"><div class="form-check mb-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div><button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button></div>
            </div>
        </div>
    </div>
</template>

<template id="repeater-template-pricing">
    <div class="col-12" data-repeater-item>
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Label EN</label><input class="form-control" data-field="label_en" type="text"></div>
                <div class="col-md-3"><label class="form-label">Label AR</label><input class="form-control text-end" dir="rtl" data-field="label_ar" type="text"></div>
                <div class="col-md-2"><label class="form-label">Value EN</label><input class="form-control" data-field="value_en" type="text"></div>
                <div class="col-md-2"><label class="form-label">Value AR</label><input class="form-control text-end" dir="rtl" data-field="value_ar" type="text"></div>
                <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" data-field="sort_order" type="number"></div>
                <div class="col-md-1 d-flex flex-column justify-content-end"><div class="form-check mb-2"><input class="form-check-input" data-field="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div><button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button></div>
                <div class="col-md-6"><label class="form-label">Note EN</label><textarea class="form-control" data-field="note_en" rows="3"></textarea></div>
                <div class="col-md-6"><label class="form-label">Note AR</label><textarea class="form-control text-end" dir="rtl" data-field="note_ar" rows="3"></textarea></div>
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

@php
    $repeaterConfigs = [
        'quick-info' => ['name' => 'quick_info_items', 'fields' => ['label_en', 'label_ar', 'value_en', 'value_ar', 'icon', 'sort_order', 'is_active']],
        'highlights' => ['name' => 'highlight_items', 'fields' => ['title_en', 'title_ar', 'description_en', 'description_ar', 'icon', 'sort_order', 'is_active']],
        'services' => ['name' => 'service_items', 'fields' => ['title_en', 'title_ar', 'description_en', 'description_ar', 'icon', 'sort_order', 'is_active']],
        'documents' => ['name' => 'document_items', 'fields' => ['title_en', 'title_ar', 'description_en', 'description_ar', 'icon', 'sort_order', 'is_active']],
        'steps' => ['name' => 'step_items', 'fields' => ['title_en', 'title_ar', 'description_en', 'description_ar', 'icon', 'step_number', 'sort_order', 'is_active']],
        'pricing' => ['name' => 'pricing_items', 'fields' => ['label_en', 'label_ar', 'value_en', 'value_ar', 'note_en', 'note_ar', 'sort_order', 'is_active']],
        'faq' => ['name' => 'faq_items', 'fields' => ['question_en', 'question_ar', 'answer_en', 'answer_ar', 'sort_order', 'is_active']],
    ];
@endphp

<script>
const destinationRepeaterConfig = @json($repeaterConfigs);

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
        syncDestinationRepeaterNames(key);
    }

    if (removeButton) {
        const item = removeButton.closest('[data-repeater-item]');
        const list = removeButton.closest('[data-repeater-list]');

        if (item) {
            item.remove();
        }

        if (list) {
            syncDestinationRepeaterNames(list.getAttribute('data-repeater-list'));
        }
    }
});

function syncDestinationRepeaterNames(key) {
    const list = document.querySelector('[data-repeater-list="' + key + '"]');
    if (!list || key === 'about-points') {
        return;
    }

    list.querySelectorAll('[data-repeater-item]').forEach(function (item, index) {
        const inputs = item.querySelectorAll('input, textarea, select');
        inputs.forEach(function (input) {
            const field = input.getAttribute('data-field');
            const base = destinationRepeaterConfig[key] ? destinationRepeaterConfig[key].name : null;

            if (field && base) {
                input.name = `${base}[${index}][${field}]`;
            }
        });
    });
}

['quick-info', 'highlights', 'services', 'documents', 'steps', 'pricing', 'faq'].forEach(syncDestinationRepeaterNames);
</script>
