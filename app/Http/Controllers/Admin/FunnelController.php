<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funnel;
use App\Models\FunnelTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FunnelController extends Controller
{
    public function index(Request $request)
    {
        $query = Funnel::query()->with('template')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $funnels = $query->paginate(20);

        return view('admin.funnels.index', compact('funnels'));
    }

    public function create()
    {
        $templates = FunnelTemplate::where('is_active', true)->orderBy('sort_order')->get();
        $existingFunnels = Funnel::whereNull('deleted_at')->latest()->get();

        return view('admin.funnels.create', compact('templates', 'existingFunnels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:funnels,slug',
            'creation_type' => 'required|in:scratch,template,duplicate',
            'template_id' => 'nullable|exists:funnel_templates,id',
            'duplicate_funnel_id' => 'nullable|exists:funnels,id',
        ]);

        $slug = Str::slug($validated['slug']);

        if ($validated['creation_type'] === 'template' && ! empty($validated['template_id'])) {
            $template = FunnelTemplate::findOrFail($validated['template_id']);
            $funnel = $this->createFromTemplate($template, $validated['name'], $slug);
        } elseif ($validated['creation_type'] === 'duplicate' && ! empty($validated['duplicate_funnel_id'])) {
            $sourceFunnel = Funnel::findOrFail($validated['duplicate_funnel_id']);
            $funnel = $this->duplicateFunnel($sourceFunnel, $validated['name'], $slug);
        } else {
            $funnel = Funnel::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'slug' => $slug,
                'status' => 'draft',
                'design_settings' => [
                    'primary_color' => '#2563eb',
                    'font_family' => 'System',
                    'button_style' => 'rounded-md',
                ],
                'crm_settings' => [
                    'enabled' => true,
                ],
            ]);

            // Add default welcome step
            $funnel->steps()->create([
                'title' => 'Welcome',
                'step_type' => 'welcome',
                'sort_order' => 1,
            ]);
        }

        return redirect()->route('admin.funnels.builder', $funnel)
            ->with('success', __('admin.funnel_created_successfully'));
    }

    public function publish(Funnel $funnel)
    {
        $funnel->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return redirect()->back()->with('success', __('admin.funnel_published_successfully'));
    }

    public function unpublish(Funnel $funnel)
    {
        $funnel->update([
            'status' => 'unpublished',
        ]);

        return redirect()->back()->with('success', __('admin.funnel_unpublished_successfully'));
    }

    public function duplicate(Funnel $funnel)
    {
        $newName = $funnel->name . ' (Copy)';
        $newSlug = Str::slug($funnel->slug . '-copy-' . rand(100, 999));

        $copy = $this->duplicateFunnel($funnel, $newName, $newSlug);

        return redirect()->route('admin.funnels.index')
            ->with('success', __('admin.funnel_duplicated_successfully'));
    }

    public function destroy(Funnel $funnel)
    {
        $funnel->delete();

        return redirect()->route('admin.funnels.index')
            ->with('success', __('admin.funnel_deleted_successfully'));
    }

    protected function createFromTemplate(FunnelTemplate $template, string $name, string $slug): Funnel
    {
        $schema = $template->schema_data ?? [];

        $funnel = Funnel::create([
            'user_id' => auth()->id(),
            'template_id' => $template->id,
            'name' => $name,
            'slug' => $slug,
            'status' => 'draft',
            'design_settings' => $schema['design_settings'] ?? [],
            'crm_settings' => $schema['crm_settings'] ?? ['enabled' => true],
            'tracking_settings' => $schema['tracking_settings'] ?? [],
            'seo_settings' => $schema['seo_settings'] ?? [],
        ]);

        $this->importSchemaToFunnel($funnel, $schema);

        return $funnel;
    }

    protected function duplicateFunnel(Funnel $source, string $name, string $slug): Funnel
    {
        $copy = Funnel::create([
            'user_id' => auth()->id(),
            'template_id' => $source->template_id,
            'name' => $name,
            'slug' => $slug,
            'status' => 'draft',
            'design_settings' => $source->design_settings,
            'crm_settings' => $source->crm_settings,
            'tracking_settings' => $source->tracking_settings,
            'seo_settings' => $source->seo_settings,
        ]);

        $source->loadMissing(['steps.elements', 'results', 'conditions']);

        foreach ($source->steps as $step) {
            $newStep = $copy->steps()->create([
                'title' => $step->title,
                'subtitle' => $step->subtitle,
                'step_type' => $step->step_type,
                'sort_order' => $step->sort_order,
                'is_hidden' => $step->is_hidden,
            ]);

            foreach ($step->elements as $el) {
                $newStep->elements()->create([
                    'element_type' => $el->element_type,
                    'label' => $el->label,
                    'question_key' => $el->question_key,
                    'properties' => $el->properties,
                    'sort_order' => $el->sort_order,
                ]);
            }
        }

        foreach ($source->results as $result) {
            $copy->results()->create([
                'title' => $result->title,
                'description' => $result->description,
                'image_url' => $result->image_url,
                'min_score' => $result->min_score,
                'max_score' => $result->max_score,
                'cta_label' => $result->cta_label,
                'cta_type' => $result->cta_type,
                'cta_url' => $result->cta_url,
                'cta_whatsapp_number' => $result->cta_whatsapp_number,
                'logic_conditions' => $result->logic_conditions,
                'sort_order' => $result->sort_order,
            ]);
        }

        return $copy;
    }

    public function importSchemaToFunnel(Funnel $funnel, $schema): void
    {
        if (is_string($schema)) {
            $schema = json_decode($schema, true) ?: [];
        }

        if (! empty($schema['steps']) && is_array($schema['steps'])) {
            foreach ($schema['steps'] as $sIndex => $stepData) {
                $step = $funnel->steps()->create([
                    'title' => $stepData['title'] ?? 'Step ' . ($sIndex + 1),
                    'subtitle' => $stepData['subtitle'] ?? null,
                    'step_type' => $stepData['step_type'] ?? 'question',
                    'sort_order' => $stepData['sort_order'] ?? ($sIndex + 1),
                ]);

                if (! empty($stepData['elements']) && is_array($stepData['elements'])) {
                    foreach ($stepData['elements'] as $eIndex => $elData) {
                        $step->elements()->create([
                            'element_type' => $elData['element_type'] ?? 'text',
                            'label' => $elData['label'] ?? null,
                            'question_key' => $elData['question_key'] ?? null,
                            'properties' => $elData['properties'] ?? [],
                            'sort_order' => $elData['sort_order'] ?? ($eIndex + 1),
                        ]);
                    }
                }
            }
        }

        if (! empty($schema['results']) && is_array($schema['results'])) {
            foreach ($schema['results'] as $rIndex => $rData) {
                $funnel->results()->create([
                    'title' => $rData['title'] ?? 'Result',
                    'description' => $rData['description'] ?? null,
                    'image_url' => $rData['image_url'] ?? null,
                    'min_score' => $rData['min_score'] ?? null,
                    'max_score' => $rData['max_score'] ?? null,
                    'cta_label' => $rData['cta_label'] ?? 'Contact Us',
                    'cta_type' => $rData['cta_type'] ?? 'button',
                    'cta_url' => $rData['cta_url'] ?? null,
                    'cta_whatsapp_number' => $rData['cta_whatsapp_number'] ?? null,
                    'sort_order' => $rData['sort_order'] ?? ($rIndex + 1),
                ]);
            }
        }
    }
}
