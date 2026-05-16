<template id="field-row-template">
    @include('admin.forms.partials.field-row', ['index' => '__INDEX__', 'field' => [], 'fieldTypeOptions' => $fieldTypeOptions])
</template>

<template id="assignment-row-template">
    @include('admin.forms.partials.assignment-row', ['index' => '__INDEX__', 'assignment' => [], 'assignmentTargets' => $assignmentTargets, 'positionOptions' => $positionOptions])
</template>

<template id="info-item-row-template">
    @include('admin.forms.partials.info-item-row', ['index' => '__INDEX__', 'item' => []])
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const fieldList = document.getElementById('form-fields-list');
    const assignmentList = document.getElementById('form-assignments-list');
    const infoList = document.getElementById('form-info-items-list');
    const fieldTemplate = document.getElementById('field-row-template')?.innerHTML ?? '';
    const assignmentTemplate = document.getElementById('assignment-row-template')?.innerHTML ?? '';
    const infoTemplate = document.getElementById('info-item-row-template')?.innerHTML ?? '';

    const appendRow = (container, template, selector) => {
        const index = container.querySelectorAll(selector).length;
        container.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', index));
    };

    document.getElementById('add-form-field')?.addEventListener('click', () => appendRow(fieldList, fieldTemplate, '.form-field-row'));
    document.getElementById('add-form-assignment')?.addEventListener('click', () => appendRow(assignmentList, assignmentTemplate, '.form-assignment-row'));
    document.getElementById('add-form-info-item')?.addEventListener('click', () => appendRow(infoList, infoTemplate, '.form-info-item-row'));

    document.addEventListener('click', (event) => {
        if (event.target.matches('.remove-field-row')) {
            event.target.closest('.form-field-row')?.remove();
        }

        if (event.target.matches('.remove-assignment-row')) {
            event.target.closest('.form-assignment-row')?.remove();
        }

        if (event.target.matches('.remove-info-item-row')) {
            event.target.closest('.form-info-item-row')?.remove();
        }
    });

    document.addEventListener('change', (event) => {
        if (event.target.matches('select[name*="[type]"]')) {
            const row = event.target.closest('.form-field-row');
            const optionsWrapper = row?.querySelector('.field-options-wrapper');
            if (optionsWrapper) {
                optionsWrapper.style.display = event.target.value === 'select' ? '' : 'none';
            }
        }
    });
});
</script>
