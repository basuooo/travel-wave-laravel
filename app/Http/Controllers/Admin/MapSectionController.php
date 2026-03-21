<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\MapSection;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use App\Support\MapSectionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MapSectionController extends Controller
{
    public function index()
    {
        return view('admin.map-sections.index', [
            'items' => MapSection::query()->withCount('assignments')->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.map-sections.form', $this->formViewData(new MapSection([
            'layout_type' => 'split',
            'background_style' => 'default',
            'spacing_preset' => 'normal',
            'height' => 380,
            'rounded_corners' => true,
            'is_active' => true,
        ])));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $item = MapSection::create($data['map']);
        $item->assignments()->createMany($data['assignments']);

        return redirect()->route('admin.map-sections.index')->with('success', 'Map section created successfully.');
    }

    public function edit(MapSection $map_section)
    {
        $map_section->load('assignments');

        return view('admin.map-sections.form', $this->formViewData($map_section));
    }

    public function show(MapSection $map_section)
    {
        return redirect()->route('admin.map-sections.edit', $map_section);
    }

    public function update(Request $request, MapSection $map_section)
    {
        $data = $this->validatedData($request, $map_section->id);

        $map_section->update($data['map']);
        $map_section->assignments()->delete();
        $map_section->assignments()->createMany($data['assignments']);

        return redirect()->route('admin.map-sections.index')->with('success', 'Map section updated successfully.');
    }

    public function destroy(MapSection $map_section)
    {
        if ($map_section->assignments()->where('is_active', true)->exists()) {
            return back()->withErrors('Please remove active assignments before deleting this map section.');
        }

        $map_section->delete();

        return redirect()->route('admin.map-sections.index')->with('success', 'Map section deleted successfully.');
    }

    public function duplicate(MapSection $map_section)
    {
        $map_section->load('assignments');

        $copy = $map_section->replicate();
        $copy->name = $map_section->name . ' Copy';
        $copy->slug = $this->uniqueSlug($map_section->slug . '-copy');
        $copy->is_active = false;
        $copy->save();

        foreach ($map_section->assignments as $assignment) {
            $copy->assignments()->create($assignment->only([
                'assignment_type',
                'target_id',
                'target_key',
                'display_position',
                'sort_order',
                'is_active',
            ]));
        }

        return redirect()->route('admin.map-sections.edit', $copy)->with('success', 'Map section duplicated successfully.');
    }

    protected function validatedData(Request $request, ?int $id = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:map_sections,slug,' . $id],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string'],
            'subtitle_ar' => ['nullable', 'string'],
            'address_en' => ['nullable', 'string'],
            'address_ar' => ['nullable', 'string'],
            'button_text_en' => ['nullable', 'string', 'max:255'],
            'button_text_ar' => ['nullable', 'string', 'max:255'],
            'button_link' => ['nullable', 'string', 'max:1000'],
            'embed_code' => ['nullable', 'string'],
            'map_url' => ['nullable', 'string', 'max:2000'],
            'layout_type' => ['required', 'string', 'max:50'],
            'height' => ['nullable', 'integer', 'min:200', 'max:1200'],
            'background_style' => ['nullable', 'string', 'max:50'],
            'spacing_preset' => ['nullable', 'string', 'max:50'],
            'rounded_corners' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'assignments' => ['array'],
            'assignments.*.assignment_target' => ['nullable', 'string'],
            'assignments.*.display_position' => ['nullable', 'string', 'max:50'],
            'assignments.*.sort_order' => ['nullable', 'integer'],
            'assignments.*.is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'map' => [
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'title_en' => $validated['title_en'] ?? null,
                'title_ar' => $validated['title_ar'] ?? null,
                'subtitle_en' => $validated['subtitle_en'] ?? null,
                'subtitle_ar' => $validated['subtitle_ar'] ?? null,
                'address_en' => $validated['address_en'] ?? null,
                'address_ar' => $validated['address_ar'] ?? null,
                'button_text_en' => $validated['button_text_en'] ?? null,
                'button_text_ar' => $validated['button_text_ar'] ?? null,
                'button_link' => $validated['button_link'] ?? null,
                'embed_code' => $validated['embed_code'] ?? null,
                'map_url' => $validated['map_url'] ?? null,
                'layout_type' => $validated['layout_type'] ?? 'split',
                'height' => (int) ($validated['height'] ?? 380),
                'background_style' => $validated['background_style'] ?? 'default',
                'spacing_preset' => $validated['spacing_preset'] ?? 'normal',
                'rounded_corners' => $request->boolean('rounded_corners', true),
                'is_active' => $request->boolean('is_active', true),
            ],
            'assignments' => $this->mapAssignments($request->input('assignments', [])),
        ];
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

    protected function uniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $candidate = $slug;
        $counter = 2;

        while (MapSection::query()->where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    protected function formViewData(MapSection $item): array
    {
        $item->loadMissing('assignments');

        return [
            'item' => $item,
            'isEdit' => $item->exists,
            'assignments' => $item->assignments->map(function ($assignment) {
                return [
                    'assignment_target' => $assignment->assignment_type . '|' . ($assignment->target_key ?: $assignment->target_id),
                    'display_position' => $assignment->display_position,
                    'sort_order' => $assignment->sort_order,
                    'is_active' => $assignment->is_active,
                ];
            })->all(),
            'assignmentTargets' => $this->assignmentTargetOptions(),
            'positionOptions' => MapSectionManager::positions(),
            'layoutOptions' => MapSectionManager::layoutTypeOptions(),
            'backgroundOptions' => MapSectionManager::backgroundStyleOptions(),
            'spacingOptions' => MapSectionManager::spacingPresetOptions(),
        ];
    }

    protected function assignmentTargetOptions(): array
    {
        return [
            'Specific pages' => collect(MapSectionManager::pageKeyOptions())->mapWithKeys(fn ($label, $key) => ['page_key|' . $key => $label])->all(),
            'Page groups' => collect(MapSectionManager::pageGroupOptions())->mapWithKeys(fn ($label, $key) => ['page_group|' . $key => $label])->all(),
            'Visa destinations' => VisaCountry::query()->orderBy('sort_order')->get()->mapWithKeys(fn (VisaCountry $country) => ['visa_country|' . $country->id => $country->name_en . ' / ' . $country->name_ar])->all(),
            'Visa categories' => VisaCategory::query()->orderBy('sort_order')->get()->mapWithKeys(fn (VisaCategory $category) => ['visa_category|' . $category->id => $category->name_en . ' / ' . $category->name_ar])->all(),
            'Domestic destinations' => Destination::query()->orderBy('sort_order')->get()->mapWithKeys(fn (Destination $destination) => ['destination|' . $destination->id => $destination->title_en . ' / ' . $destination->title_ar])->all(),
            'Destination types' => collect(MapSectionManager::destinationTypeOptions())->mapWithKeys(fn ($label, $key) => ['destination_type|' . $key => $label])->all(),
        ];
    }
}
