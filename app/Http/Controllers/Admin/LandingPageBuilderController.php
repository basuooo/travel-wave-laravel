<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\LeadForm;
use App\Models\LandingPage\Brand;
use App\Models\LandingPage\LpActivityLog;
use App\Models\LandingPage\LpLandingPage;
use App\Models\LandingPage\LpPageVersion;
use App\Models\LandingPage\LpSection;
use App\Models\LandingPage\LpSectionCategory;
use App\Models\LandingPage\LpTemplate;
use App\Models\LandingPage\LpTemplateCategory;
use App\Models\TrackingIntegration;
use App\Models\VisaCountry;
use App\Models\Destination;
use App\Services\LandingPage\LandingPageBuilderService;
use App\Support\SimpleSpreadsheet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LandingPageBuilderController extends Controller
{
    protected LandingPageBuilderService $builderService;

    public function __construct(LandingPageBuilderService $builderService)
    {
        $this->builderService = $builderService;
    }

    /**
     * Landing Pages Dashboard Overview
     */
    public function dashboard()
    {
        $totalPages = LpLandingPage::count();
        $publishedPages = LpLandingPage::where('status', LpLandingPage::STATUS_PUBLISHED)->count();
        $draftPages = LpLandingPage::where('status', LpLandingPage::STATUS_DRAFT)->count();
        $activePages = LpLandingPage::where('is_active', true)->count();
        $inactivePages = LpLandingPage::where('is_active', false)->count();
        $totalTemplates = LpTemplate::where('is_active', true)->count();
        $totalSections = LpSection::where('is_active', true)->count();
        $totalForms = LeadForm::where('is_active', true)->count();
        $totalLeads = Inquiry::whereNotNull('marketing_landing_page_id')->orWhereNotNull('lead_form_id')->count();

        $recentPages = LpLandingPage::with(['brand', 'creator'])->latest()->take(6)->get();
        $recentActivity = LpActivityLog::with(['user', 'landingPage'])->latest()->take(10)->get();

        return view('admin.landing-pages.dashboard', compact(
            'totalPages',
            'publishedPages',
            'draftPages',
            'activePages',
            'inactivePages',
            'totalTemplates',
            'totalSections',
            'totalForms',
            'totalLeads',
            'recentPages',
            'recentActivity'
        ));
    }

    /**
     * List All Landing Pages
     */
    public function index(Request $request)
    {
        $query = LpLandingPage::with(['brand', 'leadForm', 'creator']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('internal_name', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('title_ar', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->has('active') && $request->string('active') !== '') {
            $query->where('is_active', $request->boolean('active'));
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        $items = $query->latest()->paginate(15)->withQueryString();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.landing-pages.index', compact('items', 'brands'));
    }

    /**
     * Create Landing Page Selection Screen (From Scratch vs From Template)
     */
    public function create()
    {
        $templates = LpTemplate::with(['category', 'brand'])->where('is_active', true)->latest()->get();
        $templateCategories = LpTemplateCategory::orderBy('sort_order')->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.landing-pages.create', compact('templates', 'templateCategories', 'brands'));
    }

    /**
     * Store new Landing Page
     */
    public function store(Request $request)
    {
        $mode = $request->input('creation_mode', 'scratch');

        if ($mode === 'template') {
            $request->validate([
                'template_id' => ['required', 'exists:lp_templates,id'],
                'internal_name' => ['required', 'string', 'max:255'],
            ]);

            $template = LpTemplate::findOrFail($request->input('template_id'));
            $page = $this->builderService->createFromTemplate($template, [
                'internal_name' => $request->input('internal_name'),
                'title_en' => $request->input('title_en', $request->input('internal_name')),
                'title_ar' => $request->input('title_ar', $request->input('internal_name')),
                'brand_id' => $request->input('brand_id'),
            ], auth()->id());

            return redirect()->route('admin.landing-pages.builder', $page)
                ->with('success', __('admin.landing_page_created_from_template'));
        }

        $data = $request->validate([
            'internal_name' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'brand_id' => ['nullable', 'exists:brands,id'],
        ]);

        $page = $this->builderService->createFromScratch($data, auth()->id());

        return redirect()->route('admin.landing-pages.builder', $page)
            ->with('success', __('admin.landing_page_created_from_scratch'));
    }

    /**
     * Visual Drag & Drop Builder Screen
     */
    public function builder(LpLandingPage $landingPage)
    {
        $brands = Brand::where('is_active', true)->get();
        $forms = LeadForm::where('is_active', true)->orderBy('name')->get();
        $trackingIntegrations = TrackingIntegration::where('is_active', true)->orderBy('sort_order')->get();
        $sectionCategories = LpSectionCategory::with(['sections' => fn ($q) => $q->where('is_active', true)])->orderBy('sort_order')->get();
        $templateCategories = LpTemplateCategory::with(['templates' => fn ($q) => $q->where('is_active', true)])->orderBy('sort_order')->get();
        
        // Data Sources for Dynamic Binding
        $visaCountries = VisaCountry::where('is_active', true)->orderBy('name_ar')->get(['id', 'name_en', 'name_ar', 'price', 'hero_image', 'flag_image']);
        $destinations = Destination::where('is_active', true)->orderBy('title_ar')->get(['id', 'title_en', 'title_ar', 'price', 'hero_image']);

        return view('admin.landing-pages.builder', compact(
            'landingPage',
            'brands',
            'forms',
            'trackingIntegrations',
            'sectionCategories',
            'templateCategories',
            'visaCountries',
            'destinations'
        ));
    }

    /**
     * Update Builder Structure & Settings via AJAX / Form Submission
     */
    public function update(Request $request, LpLandingPage $landingPage)
    {
        $validated = $request->validate([
            'internal_name' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('lp_landing_pages', 'slug')->ignore($landingPage->id)],
            'status' => ['required', Rule::in([LpLandingPage::STATUS_DRAFT, LpLandingPage::STATUS_PUBLISHED, LpLandingPage::STATUS_ARCHIVED])],
            'is_active' => ['required', 'boolean'],
            'header_mode' => ['required', 'in:website,custom,none'],
            'footer_mode' => ['required', 'in:website,custom,none'],
            'assigned_lead_form_id' => ['nullable', 'exists:lead_forms,id'],
            'publish_at' => ['nullable', 'date'],
            'unpublish_at' => ['nullable', 'date'],
            'tracking_mode' => ['nullable', 'in:website,brand,custom'],
            'tracking_integration_ids' => ['array'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_ar' => ['nullable', 'string', 'max:255'],
            'seo_description_en' => ['nullable', 'string'],
            'seo_description_ar' => ['nullable', 'string'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
            'utm_term' => ['nullable', 'string', 'max:255'],
            'custom_css' => ['nullable', 'string'],
            'custom_js' => ['nullable', 'string'],
            'custom_html_head' => ['nullable', 'string'],
            'structure' => ['nullable'], // JSON object or string
        ]);

        if (is_string($validated['structure'] ?? null)) {
            $validated['structure'] = json_decode($validated['structure'], true);
        }

        $validated['updated_by'] = auth()->id();

        $landingPage->update($validated);

        // Save autosave version snapshot
        $this->builderService->createVersionSnapshot($landingPage, 'Saved in Builder', auth()->id());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Landing page saved successfully.',
                'page' => $landingPage->fresh(),
            ]);
        }

        return redirect()->route('admin.landing-pages.builder', $landingPage)->with('success', 'Landing page saved successfully.');
    }

    /**
     * Active / Inactive Toggle (ON/OFF) AJAX
     */
    public function toggleActive(LpLandingPage $landingPage)
    {
        $landingPage->is_active = ! $landingPage->is_active;
        $landingPage->updated_by = auth()->id();
        $landingPage->save();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $landingPage->is_active,
                'message' => $landingPage->is_active ? 'Page Activated' : 'Page Deactivated',
            ]);
        }

        return back()->with('success', $landingPage->is_active ? 'Page Activated' : 'Page Deactivated');
    }

    /**
     * Duplicate Page
     */
    public function duplicate(LpLandingPage $landingPage)
    {
        $copy = $landingPage->replicate();
        $copy->internal_name = $landingPage->internal_name . ' Copy';
        $copy->slug = $this->builderService->resolveUniqueSlug($landingPage->slug . '-copy');
        $copy->status = LpLandingPage::STATUS_DRAFT;
        $copy->created_by = auth()->id();
        $copy->updated_by = auth()->id();
        $copy->save();

        return redirect()->route('admin.landing-pages.builder', $copy)->with('success', 'Landing page duplicated.');
    }

    /**
     * Save Page as Template
     */
    public function saveAsTemplate(Request $request, LpLandingPage $landingPage)
    {
        $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'template_category_id' => ['nullable', 'exists:lp_template_categories,id'],
        ]);

        $template = $this->builderService->savePageAsTemplate($landingPage, [
            'name_en' => $request->input('name_en'),
            'name_ar' => $request->input('name_ar'),
            'template_category_id' => $request->input('template_category_id'),
            'description_en' => $request->input('description_en'),
            'description_ar' => $request->input('description_ar'),
        ], auth()->id());

        return back()->with('success', 'Template created successfully: ' . $template->name_en);
    }

    /**
     * Export Package JSON
     */
    public function export(LpLandingPage $landingPage)
    {
        $package = $this->builderService->exportPackage($landingPage);
        $fileName = 'landing-page-' . $landingPage->slug . '-' . date('Y-m-d') . '.json';

        return response()->streamDownload(function () use ($package) {
            echo json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $fileName, ['Content-Type' => 'application/json']);
    }

    /**
     * Import Package JSON
     */
    public function import(Request $request)
    {
        $request->validate([
            'package_file' => ['required', 'file', 'mimes:json,txt'],
        ]);

        $content = file_get_contents($request->file('package_file')->getRealPath());
        $package = json_decode($content, true);

        if (! is_array($package) || empty($package['page'])) {
            return back()->with('error', 'Invalid package format. Failed to import.');
        }

        $page = $this->builderService->importPackage($package, 'create_new', auth()->id());

        return redirect()->route('admin.landing-pages.builder', $page)->with('success', 'Package imported successfully.');
    }

    /**
     * List and Restore Versions
     */
    public function versions(LpLandingPage $landingPage)
    {
        $versions = $landingPage->versions()->with('creator')->get();

        return view('admin.landing-pages.versions', compact('landingPage', 'versions'));
    }

    public function restoreVersion(LpLandingPage $landingPage, LpPageVersion $version)
    {
        $this->builderService->restoreVersion($landingPage, $version, auth()->id());

        return redirect()->route('admin.landing-pages.builder', $landingPage)->with('success', 'Restored to Version #' . $version->version_number);
    }

    /**
     * View & Export Leads for this Landing Page
     */
    public function leads(LpLandingPage $landingPage, Request $request)
    {
        $query = Inquiry::query()->where('marketing_landing_page_id', $landingPage->id)->latest();

        if ($request->input('export') === 'csv' || $request->input('export') === 'excel') {
            $leads = $query->get();
            $data = [
                ['ID', 'Full Name', 'Phone', 'Email', 'Form', 'UTM Source', 'UTM Campaign', 'Date'],
            ];
            foreach ($leads as $lead) {
                $data[] = [
                    $lead->id,
                    $lead->full_name,
                    $lead->phone,
                    $lead->email,
                    $lead->service_type,
                    $lead->utm_source,
                    $lead->utm_campaign,
                    $lead->created_at->format('Y-m-d H:i'),
                ];
            }

            return SimpleSpreadsheet::download($data, 'leads-' . $landingPage->slug . '-' . date('Y-m-d') . '.csv');
        }

        $leads = $query->paginate(20);

        return view('admin.landing-pages.leads', compact('landingPage', 'leads'));
    }

    /**
     * Delete / Archive Landing Page
     */
    public function destroy(LpLandingPage $landingPage)
    {
        $landingPage->delete();

        return redirect()->route('admin.landing-pages.index')->with('success', 'Landing page moved to trash.');
    }
}
