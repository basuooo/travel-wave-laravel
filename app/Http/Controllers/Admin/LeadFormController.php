<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\LeadForm;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use App\Support\LeadFormManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeadFormController extends Controller
{
    public function index()
    {
        return view('admin.forms.index', [
            'items' => LeadForm::query()
                ->withCount(['assignments', 'inquiries'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.forms.form', $this->formViewData(new LeadForm([
            'is_active' => true,
        ])));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $form = LeadForm::create($data['form']);
        $form->fields()->createMany($data['fields']);
        $form->assignments()->createMany($data['assignments']);

        return redirect()->route('admin.forms.index')->with('success', 'Form created successfully.');
    }

    public function show(LeadForm $form)
    {
        return redirect()->route('admin.forms.edit', $form);
    }

    public function edit(LeadForm $form)
    {
        $form->load(['fields', 'assignments']);

        return view('admin.forms.form', $this->formViewData($form));
    }

    public function update(Request $request, LeadForm $form)
    {
        $data = $this->validatedData($request, $form->id);

        $form->update($data['form']);
        $form->fields()->delete();
        $form->assignments()->delete();
        $form->fields()->createMany($data['fields']);
        $form->assignments()->createMany($data['assignments']);

        return redirect()->route('admin.forms.index')->with('success', 'Form updated successfully.');
    }

    public function destroy(LeadForm $form)
    {
        if ($form->assignments()->where('is_active', true)->exists()) {
            return back()->withErrors('Please remove active assignments before deleting this form.');
        }

        $form->delete();

        return redirect()->route('admin.forms.index')->with('success', 'Form deleted successfully.');
    }

    public function duplicate(LeadForm $form)
    {
        $form->load(['fields', 'assignments']);

        $copy = $form->replicate();
        $copy->name = $form->name . ' Copy';
        $copy->slug = $this->uniqueSlug($form->slug . '-copy');
        $copy->is_active = false;
        $copy->save();

        foreach ($form->fields as $field) {
            $copy->fields()->create($field->only([
                'field_key',
                'type',
                'label_en',
                'label_ar',
                'placeholder_en',
                'placeholder_ar',
                'help_text_en',
                'help_text_ar',
                'validation_rule',
                'default_value',
                'options',
                'is_required',
                'is_enabled',
                'sort_order',
            ]));
        }

        foreach ($form->assignments as $assignment) {
            $copy->assignments()->create($assignment->only([
                'assignment_type',
                'target_id',
                'target_key',
                'display_position',
                'sort_order',
                'is_active',
            ]));
        }

        return redirect()->route('admin.forms.edit', $copy)->with('success', 'Form duplicated successfully.');
    }

    protected function validatedData(Request $request, ?int $formId = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:lead_forms,slug,' . $formId],
            'form_category' => ['nullable', 'string', 'max:50'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string'],
            'subtitle_ar' => ['nullable', 'string'],
            'submit_text_en' => ['nullable', 'string', 'max:255'],
            'submit_text_ar' => ['nullable', 'string', 'max:255'],
            'success_message_en' => ['nullable', 'string'],
            'success_message_ar' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'settings.layout_type' => ['nullable', 'string', 'max:50'],
            'settings.layout_variant' => ['nullable', 'string', 'max:50'],
            'settings.info_label_en' => ['nullable', 'string', 'max:255'],
            'settings.info_label_ar' => ['nullable', 'string', 'max:255'],
            'settings.info_heading_en' => ['nullable', 'string', 'max:255'],
            'settings.info_heading_ar' => ['nullable', 'string', 'max:255'],
            'settings.info_description_en' => ['nullable', 'string'],
            'settings.info_description_ar' => ['nullable', 'string'],
            'settings.info_items' => ['array'],
            'settings.info_items.*.title_en' => ['nullable', 'string', 'max:255'],
            'settings.info_items.*.title_ar' => ['nullable', 'string', 'max:255'],
            'settings.info_items.*.value_en' => ['nullable', 'string', 'max:255'],
            'settings.info_items.*.value_ar' => ['nullable', 'string', 'max:255'],
            'settings.info_items.*.sort_order' => ['nullable', 'integer'],
            'settings.info_items.*.is_active' => ['nullable', 'boolean'],
            'fields' => ['array'],
            'fields.*.field_key' => ['nullable', 'string', 'max:255'],
            'fields.*.type' => ['nullable', 'string', 'max:50'],
            'fields.*.label_en' => ['nullable', 'string', 'max:255'],
            'fields.*.label_ar' => ['nullable', 'string', 'max:255'],
            'fields.*.placeholder_en' => ['nullable', 'string', 'max:255'],
            'fields.*.placeholder_ar' => ['nullable', 'string', 'max:255'],
            'fields.*.help_text_en' => ['nullable', 'string'],
            'fields.*.help_text_ar' => ['nullable', 'string'],
            'fields.*.validation_rule' => ['nullable', 'string'],
            'fields.*.default_value' => ['nullable', 'string'],
            'fields.*.options_text' => ['nullable', 'string'],
            'fields.*.sort_order' => ['nullable', 'integer'],
            'fields.*.is_required' => ['nullable', 'boolean'],
            'fields.*.is_enabled' => ['nullable', 'boolean'],
            'assignments' => ['array'],
            'assignments.*.assignment_target' => ['nullable', 'string'],
            'assignments.*.display_position' => ['nullable', 'string', 'max:50'],
            'assignments.*.sort_order' => ['nullable', 'integer'],
            'assignments.*.is_active' => ['nullable', 'boolean'],
        ]);

        $layoutType = $request->input('settings.layout_type')
            ?: $request->input('settings.layout_variant')
            ?: 'standard';

        return [
            'form' => [
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'form_category' => $validated['form_category'] ?? null,
                'title_en' => $validated['title_en'] ?? null,
                'title_ar' => $validated['title_ar'] ?? null,
                'subtitle_en' => $validated['subtitle_en'] ?? null,
                'subtitle_ar' => $validated['subtitle_ar'] ?? null,
                'submit_text_en' => $validated['submit_text_en'] ?? null,
                'submit_text_ar' => $validated['submit_text_ar'] ?? null,
                'success_message_en' => $validated['success_message_en'] ?? null,
                'success_message_ar' => $validated['success_message_ar'] ?? null,
                'is_active' => $request->boolean('is_active'),
                'settings' => [
                    'layout_type' => $layoutType,
                    'layout_variant' => $layoutType,
                    'info_label_en' => $request->input('settings.info_label_en'),
                    'info_label_ar' => $request->input('settings.info_label_ar'),
                    'info_heading_en' => $request->input('settings.info_heading_en'),
                    'info_heading_ar' => $request->input('settings.info_heading_ar'),
                    'info_description_en' => $request->input('settings.info_description_en'),
                    'info_description_ar' => $request->input('settings.info_description_ar'),
                    'info_items' => $this->mapInfoItems($request->input('settings.info_items', [])),
                ],
            ],
            'fields' => $this->mapFields($request->input('fields', [])),
            'assignments' => $this->mapAssignments($request->input('assignments', [])),
        ];
    }

    protected function mapInfoItems(array $items): array
    {
        return collect($items)->map(function (array $item, int $index) {
            $titleEn = trim((string) ($item['title_en'] ?? ''));
            $titleAr = trim((string) ($item['title_ar'] ?? ''));
            $valueEn = trim((string) ($item['value_en'] ?? ''));
            $valueAr = trim((string) ($item['value_ar'] ?? ''));

            if ($titleEn === '' && $titleAr === '' && $valueEn === '' && $valueAr === '') {
                return null;
            }

            return [
                'title_en' => $titleEn,
                'title_ar' => $titleAr,
                'value_en' => $valueEn,
                'value_ar' => $valueAr,
                'sort_order' => (int) ($item['sort_order'] ?? ($index + 1)),
                'is_active' => !empty($item['is_active']),
            ];
        })->filter()->sortBy('sort_order')->values()->all();
    }

    protected function mapFields(array $fields): array
    {
        return collect($fields)->map(function (array $field, int $index) {
            $fieldKey = Str::snake(trim((string) ($field['field_key'] ?? '')));
            $labelAr = trim((string) ($field['label_ar'] ?? ''));
            $labelEn = trim((string) ($field['label_en'] ?? ''));

            if ($fieldKey === '' && $labelEn === '' && $labelAr === '') {
                return null;
            }

            return [
                'field_key' => $fieldKey ?: 'field_' . ($index + 1),
                'type' => trim((string) ($field['type'] ?? 'text')) ?: 'text',
                'label_en' => $labelEn,
                'label_ar' => $labelAr,
                'placeholder_en' => trim((string) ($field['placeholder_en'] ?? '')),
                'placeholder_ar' => trim((string) ($field['placeholder_ar'] ?? '')),
                'help_text_en' => trim((string) ($field['help_text_en'] ?? '')),
                'help_text_ar' => trim((string) ($field['help_text_ar'] ?? '')),
                'validation_rule' => trim((string) ($field['validation_rule'] ?? '')),
                'default_value' => trim((string) ($field['default_value'] ?? '')),
                'options' => $this->parseOptions($field['options_text'] ?? ''),
                'is_required' => !empty($field['is_required']),
                'is_enabled' => !empty($field['is_enabled']),
                'sort_order' => (int) ($field['sort_order'] ?? $index + 1),
            ];
        })->filter()->sortBy('sort_order')->values()->all();
    }

    protected function mapAssignments(array $assignments): array
    {
        return collect($assignments)->map(function (array $assignment, int $index) {
            $target = trim((string) ($assignment['assignment_target'] ?? ''));

            if ($target === '') {
                return null;
            }

            [$type, $value] = array_pad(explode('|', $target, 2), 2, null);

            if (!$type || !$value) {
                return null;
            }

            return [
                'assignment_type' => $type,
                'target_id' => in_array($type, ['visa_country', 'visa_category', 'destination'], true) ? (int) $value : null,
                'target_key' => in_array($type, ['page_key', 'page_group', 'destination_type'], true) ? $value : null,
                'display_position' => trim((string) ($assignment['display_position'] ?? 'bottom')) ?: 'bottom',
                'sort_order' => (int) ($assignment['sort_order'] ?? $index + 1),
                'is_active' => !empty($assignment['is_active']),
            ];
        })->filter()->sortBy('sort_order')->values()->all();
    }

    protected function parseOptions(?string $optionsText): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $optionsText))
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->map(function (string $line) {
                $parts = array_map('trim', explode('|', $line));

                return [
                    'value' => $parts[0] ?? '',
                    'label_en' => $parts[1] ?? ($parts[0] ?? ''),
                    'label_ar' => $parts[2] ?? ($parts[1] ?? ($parts[0] ?? '')),
                ];
            })
            ->values()
            ->all();
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $candidate = $slug;
        $counter = 2;

        while (LeadForm::query()->where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    protected function formViewData(LeadForm $form): array
    {
        $form->loadMissing(['fields', 'assignments']);

        return [
            'item' => $form,
            'isEdit' => $form->exists,
            'fields' => $form->fields->map(function ($field) {
                return [
                    'field_key' => $field->field_key,
                    'type' => $field->type,
                    'label_en' => $field->label_en,
                    'label_ar' => $field->label_ar,
                    'placeholder_en' => $field->placeholder_en,
                    'placeholder_ar' => $field->placeholder_ar,
                    'help_text_en' => $field->help_text_en,
                    'help_text_ar' => $field->help_text_ar,
                    'validation_rule' => $field->validation_rule,
                    'default_value' => $field->default_value,
                    'options_text' => collect($field->options ?: [])->map(fn ($option) => implode('|', array_filter([
                        $option['value'] ?? '',
                        $option['label_en'] ?? '',
                        $option['label_ar'] ?? '',
                    ], fn ($value) => $value !== '')))->implode("\n"),
                    'sort_order' => $field->sort_order,
                    'is_required' => $field->is_required,
                    'is_enabled' => $field->is_enabled,
                ];
            })->all(),
            'assignments' => $form->assignments->map(function ($assignment) {
                return [
                    'assignment_target' => $assignment->assignment_type . '|' . ($assignment->target_key ?: $assignment->target_id),
                    'display_position' => $assignment->display_position,
                    'sort_order' => $assignment->sort_order,
                    'is_active' => $assignment->is_active,
                ];
            })->all(),
            'fieldTypeOptions' => LeadFormManager::fieldTypeOptions(),
            'categoryOptions' => LeadFormManager::categoryOptions(),
            'layoutTypeOptions' => [
                'standard' => 'Standard Form',
                'split_details' => 'Split Details + Form Layout',
                'visa_split' => 'External Visa Split Layout',
            ],
            'assignmentTargets' => $this->assignmentTargetOptions(),
            'positionOptions' => LeadFormManager::positions(),
        ];
    }

    protected function assignmentTargetOptions(): array
    {
        return [
            'Specific pages' => collect(LeadFormManager::pageKeyOptions())->mapWithKeys(fn ($label, $key) => ['page_key|' . $key => $label])->all(),
            'Page groups' => collect(LeadFormManager::pageGroupOptions())->mapWithKeys(fn ($label, $key) => ['page_group|' . $key => $label])->all(),
            'Visa destinations' => VisaCountry::query()->orderBy('sort_order')->get()->mapWithKeys(fn (VisaCountry $country) => ['visa_country|' . $country->id => $country->name_en . ' / ' . $country->name_ar])->all(),
            'Visa categories' => VisaCategory::query()->orderBy('sort_order')->get()->mapWithKeys(fn (VisaCategory $category) => ['visa_category|' . $category->id => $category->name_en . ' / ' . $category->name_ar])->all(),
            'Domestic destinations' => Destination::query()->orderBy('sort_order')->get()->mapWithKeys(fn (Destination $destination) => ['destination|' . $destination->id => $destination->title_en . ' / ' . $destination->title_ar])->all(),
            'Destination types' => collect(LeadFormManager::destinationTypeOptions())->mapWithKeys(fn ($label, $key) => ['destination_type|' . $key => $label])->all(),
        ];
    }
}
