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
        try {
            \App\Services\Funnels\FunnelAutoMigrationService::ensureTablesExist();
        } catch (\Throwable $e) {
            report($e);
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('funnel_templates')) {
            $templates = collect();
            $categories = [
                'Travel', 'Real Estate', 'Education', 'B2B Services', 'Automotive',
                'Healthcare', 'Financial Services', 'Legal & Immigration', 'Fitness & Health', 'E-commerce', 'Qualification'
            ];
            return view('admin.funnels.templates.index', compact('templates', 'categories'));
        }

        try {
            (new \Database\Seeders\FunnelTemplateSeeder())->run();
        } catch (\Throwable $e) {
            report($e);
        }

        $query = FunnelTemplate::where('is_active', true)->withCount('funnels');

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

        // Sorting Filter Options
        $sortBy = $request->input('sort', 'default');
        if ($sortBy === 'latest') {
            $query->orderByDesc('created_at')->orderByDesc('id');
        } elseif ($sortBy === 'most_used') {
            $query->orderByDesc('funnels_count')->orderBy('sort_order');
        } elseif ($sortBy === 'name') {
            $query->orderBy('name');
        } else {
            $query->orderBy('sort_order')->orderByDesc('id');
        }

        $templates = $query->get();

        $categories = FunnelTemplate::where('is_active', true)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        return view('admin.funnels.templates.index', compact('templates', 'categories', 'sortBy'));
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

        if (empty($template->schema_data['steps']) || count($template->schema_data['steps'] ?? []) < 2) {
            (new \Database\Seeders\FunnelTemplateSeeder())->run();
            $template->refresh();
        }

        $funnelController->importSchemaToFunnel($funnel, $template->schema_data ?? []);

        return redirect()->route('admin.funnels.builder', $funnel)
            ->with('success', __('admin.template_used_successfully'));
    }

    public function preview(FunnelTemplate $template)
    {
        $schema = $template->schema_data;
        if (is_string($schema)) {
            $schema = json_decode($schema, true);
        }
        $schema = is_array($schema) ? $schema : [];

        if (empty($schema['steps']) || count($schema['steps']) < 2) {
            (new \Database\Seeders\FunnelTemplateSeeder())->run();
            $template->refresh();
            $schema = $template->schema_data;
            if (is_string($schema)) {
                $schema = json_decode($schema, true);
            }
            $schema = is_array($schema) ? $schema : [];
        }

        return view('admin.funnels.templates.preview', compact('template', 'schema'));
    }
}
