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

    const fieldKeysList = document.getElementById('field-keys-list');
    
    const updateFieldKeysList = () => {
        if (!fieldKeysList) return;
        const keys = Array.from(document.querySelectorAll('.field-key-input'))
            .map(input => input.value.trim())
            .filter(val => val !== '');
        
        fieldKeysList.innerHTML = [...new Set(keys)].map(key => `<option value="${key}">`).join('');
    };

    const appendRow = (container, template, selector) => {
        const index = container.querySelectorAll(selector).length;
        container.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', index));
        updateFieldKeysList();
        initializeAllTags();
    };

    document.getElementById('add-form-field')?.addEventListener('click', () => appendRow(fieldList, fieldTemplate, '.form-field-row'));
    document.getElementById('add-form-assignment')?.addEventListener('click', () => appendRow(assignmentList, assignmentTemplate, '.form-assignment-row'));
    document.getElementById('add-form-info-item')?.addEventListener('click', () => appendRow(infoList, infoTemplate, '.form-info-item-row'));

    document.addEventListener('click', (event) => {
        if (event.target.matches('.remove-field-row')) {
            event.target.closest('.form-field-row')?.remove();
            updateFieldKeysList();
        }

        if (event.target.matches('.remove-assignment-row')) {
            event.target.closest('.form-assignment-row')?.remove();
        }

        if (event.target.matches('.remove-info-item-row')) {
            event.target.closest('.form-info-item-row')?.remove();
        }

        if (event.target.closest('.copy-field-key')) {
            const input = event.target.closest('.input-group')?.querySelector('.field-key-input');
            if (input && input.value) {
                navigator.clipboard.writeText(input.value).then(() => {
                    const btn = event.target.closest('.copy-field-key');
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check text-success"></i>';
                    setTimeout(() => btn.innerHTML = originalHtml, 1500);
                });
            }
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

        if (event.target.matches('.field-key-input')) {
            updateFieldKeysList();
        }
    });

    // Custom Dual Tags Input Logic
    const initDualTagsInput = (wrapper) => {
        if (wrapper.dataset.tagsInitialized) return;
        wrapper.dataset.tagsInitialized = '1';

        const hiddenTextarea = wrapper.querySelector('.js-dual-hidden-textarea');
        const containerEn = wrapper.querySelector('.js-tags-container-en');
        const containerAr = wrapper.querySelector('.js-tags-container-ar');
        const inputEn = containerEn.querySelector('.js-tags-input');
        const inputAr = containerAr.querySelector('.js-tags-input');
        
        let tagsEn = [];
        let tagsAr = [];

        if (hiddenTextarea.value.trim() !== '') {
            const lines = hiddenTextarea.value.split(/[\r\n]+/);
            lines.forEach(line => {
                const trimmed = line.trim();
                if (trimmed !== '') {
                    if (trimmed.includes('|')) {
                        const parts = trimmed.split('|');
                        const enValue = (parts[1] ?? '').trim();
                        const arValue = (parts[2] ?? '').trim();
                        tagsEn.push(enValue);
                        tagsAr.push(arValue);
                    } else if (trimmed.includes(',')) {
                        trimmed.split(',').forEach(part => {
                            if (part.trim() !== '') {
                                tagsEn.push(part.trim());
                                tagsAr.push(part.trim());
                            }
                        });
                    } else {
                        tagsEn.push(trimmed);
                        tagsAr.push(trimmed);
                    }
                }
            });
        }

        const syncHiddenTextarea = () => {
            const maxLen = Math.max(tagsEn.length, tagsAr.length);
            let lines = [];
            for (let i = 0; i < maxLen; i++) {
                let en = tagsEn[i] || '';
                let ar = tagsAr[i] || '';
                let val = en || ar;
                if (!en) en = '';
                if (!ar) ar = '';
                lines.push(`${val}|${en}|${ar}`);
            }
            hiddenTextarea.value = lines.join('\n');
        };

        const renderTags = (tags, container, input) => {
            container.querySelectorAll('.js-tag-badge').forEach(el => el.remove());
            tags.forEach((tag, idx) => {
                const badge = document.createElement('span');
                badge.className = 'badge text-bg-primary d-flex align-items-center gap-1 js-tag-badge px-2 py-1';
                badge.innerHTML = `<span>${tag}</span> <i class="fas fa-times ms-1 js-tag-remove" style="cursor:pointer;" data-index="${idx}"></i>`;
                container.insertBefore(badge, input);
            });
            syncHiddenTextarea();
        };

        const setupContainer = (container, input, tags) => {
            const addTag = (value) => {
                let added = false;
                const parts = value.split(',');
                parts.forEach(part => {
                    const cleanVal = part.trim();
                    if (cleanVal !== '') {
                        tags.push(cleanVal);
                        added = true;
                    }
                });
                
                input.value = '';
                if (added) renderTags(tags, container, input);
            };

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                if (paste) addTag(paste);
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addTag(input.value);
                } else if (e.key === 'Tab' || e.key === ',') {
                    if (input.value.trim() !== '') {
                        e.preventDefault();
                        addTag(input.value);
                    }
                } else if (e.key === 'Backspace' && input.value === '' && tags.length > 0) {
                    tags.pop();
                    renderTags(tags, container, input);
                }
            });

            container.addEventListener('click', (e) => {
                if (e.target.closest('.js-tag-remove')) {
                    const idx = parseInt(e.target.closest('.js-tag-remove').dataset.index);
                    tags.splice(idx, 1);
                    renderTags(tags, container, input);
                } else {
                    input.focus();
                }
            });
            
            renderTags(tags, container, input);
        };

        setupContainer(containerEn, inputEn, tagsEn);
        setupContainer(containerAr, inputAr, tagsAr);
    };

    const initializeAllTags = () => {
        document.querySelectorAll('.js-dual-tags-wrapper').forEach(initDualTagsInput);
    };

    updateFieldKeysList(); // Initial population
    initializeAllTags(); // Initial population
});
</script>
