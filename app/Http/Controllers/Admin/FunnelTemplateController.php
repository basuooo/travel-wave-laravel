<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funnel;
use App\Models\FunnelTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FunnelTemplateController extends Controller
{
    public function index(Request $request)
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('funnel_templates')) {
            $templates = collect();
            $categories = [
                'Lead Generation', 'Quiz', 'Qualification', 'Assessment',
                'Calculator', 'Recommendation', 'Survey', 'Travel', 'Real Estate', 'E-commerce', 'Other'
            ];
            return view('admin.funnels.templates.index', compact('templates', 'categories'));
        }

        $query = FunnelTemplate::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $templates = $query->get();

        $categories = [
            'Lead Generation',
            'Quiz',
            'Qualification',
            'Assessment',
            'Calculator',
            'Recommendation',
            'Survey',
            'Travel',
            'Real Estate',
            'E-commerce',
            'Other',
        ];

        return view('admin.funnels.templates.index', compact('templates', 'categories'));
    }

    public function useTemplate(FunnelTemplate $template, Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        $name = $request->input('name') ?: $template->name . ' (Copy)';
        $slug = Str::slug($name . '-' . rand(100, 999));

        $funnelController = app(FunnelController::class);
        $funnel = Funnel::create([
            'user_id' => auth()->id(),
            'template_id' => $template->id,
            'name' => $name,
            'slug' => $slug,
            'status' => 'draft',
            'design_settings' => $template->schema_data['design_settings'] ?? [
                'primary_color' => '#2563eb',
                'font_family' => 'System',
                'button_style' => 'rounded-md',
            ],
            'crm_settings' => $template->schema_data['crm_settings'] ?? ['enabled' => true],
            'tracking_settings' => $template->schema_data['tracking_settings'] ?? [],
            'seo_settings' => $template->schema_data['seo_settings'] ?? [],
        ]);

        $funnelController->importSchemaToFunnel($funnel, $template->schema_data ?? []);

        return redirect()->route('admin.funnels.builder', $funnel)
            ->with('success', __('admin.template_used_successfully'));
    }
}
