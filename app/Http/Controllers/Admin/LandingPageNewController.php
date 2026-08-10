<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\LeadForm;
use App\Models\TrackingIntegration;
use App\Models\LandingPageNew\Brand;
use App\Models\LandingPageNew\LpNewActivityLog;
use App\Models\LandingPageNew\LpNewAsset;
use App\Models\LandingPageNew\LpNewExperiment;
use App\Models\LandingPageNew\LpNewLandingPage;
use App\Models\LandingPageNew\LpNewPageVersion;
use App\Models\LandingPageNew\LpNewSection;
use App\Models\LandingPageNew\LpNewTemplate;
use App\Services\LandingPageNew\LandingPageNewExporter;
use App\Services\LandingPageNew\LandingPageNewImporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingPageNewController extends Controller
{
    public function dashboard(Request $request)
    {
        LpNewLandingPage::ensureTableSchema();

        $query = LpNewLandingPage::with(['brand', 'assignedForm'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('internal_name', 'like', "%{$search}%")
                    ->orWhere('title_ar', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $pages = $query->paginate(15);
        $totalPages = LpNewLandingPage::count();
        $publishedCount = LpNewLandingPage::where('status', LpNewLandingPage::STATUS_PUBLISHED)->count();
        $activeCount = LpNewLandingPage::where('is_active', true)->count();
        $totalLeads = Inquiry::whereNotNull('lead_form_id')->count();

        $brands = Brand::where('is_active', true)->get();

        return view('admin.landing-pages-new.dashboard', compact(
            'pages',
            'totalPages',
            'publishedCount',
            'activeCount',
            'totalLeads',
            'brands'
        ));
    }

    public function index(Request $request)
    {
        return $this->dashboard($request);
    }

    public function create()
    {
        LpNewLandingPage::ensureTableSchema();

        $brands = Brand::where('is_active', true)->get();
        $templates = LpNewTemplate::where('is_active', true)->get();
        $forms = LeadForm::where('is_active', true)->get();

        return view('admin.landing-pages-new.create', compact('brands', 'templates', 'forms'));
    }

    public function store(Request $request)
    {
        LpNewLandingPage::ensureTableSchema();

        $validated = $request->validate([
            'internal_name' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'brand_id' => ['nullable', 'exists:lp_new_brands,id'],
            'assigned_lead_form_id' => ['nullable', 'exists:lead_forms,id'],
            'template_id' => ['nullable', 'exists:lp_new_templates,id'],
            'creation_mode' => ['required', 'in:scratch,template'],
        ]);

        $slug = LpNewLandingPage::makeUniqueSlug($validated['slug'] ?: $validated['internal_name']);

        $structure = [];

        if ($validated['creation_mode'] === 'template' && ! empty($validated['template_id'])) {
            $template = LpNewTemplate::find($validated['template_id']);
            if ($template && filled($template->structure)) {
                $structure = is_string($template->structure) ? json_decode($template->structure, true) : $template->structure;
            }
        }

        if (empty($structure)) {
            $structure = [
                'elements' => [
                    [
                        'id' => 'sec_hero_' . Str::random(6),
                        'type' => 'section',
                        'name_ar' => 'قسم الهيرو',
                        'name_en' => 'Hero Section',
                        'is_custom_html' => false,
                        'html' => '<div class="container py-5 text-center"><h1 class="display-4 fw-bold">مرحباً بك في ترافل ويف</h1><p class="lead text-muted">صفحة هبوط جديدة واحترافية قابلة للتعديل بسهولة.</p><a href="#lead-form" class="btn btn-primary btn-lg rounded-pill px-5">ابدأ الآن</a></div>',
                    ],
                ],
            ];
        }

        $page = LpNewLandingPage::create([
            'internal_name' => $validated['internal_name'],
            'title_ar' => $validated['title_ar'] ?: $validated['internal_name'],
            'title_en' => $validated['title_en'] ?: $validated['internal_name'],
            'slug' => $slug,
            'brand_id' => $validated['brand_id'] ?? null,
            'assigned_lead_form_id' => $validated['assigned_lead_form_id'] ?? null,
            'status' => LpNewLandingPage::STATUS_DRAFT,
            'is_active' => true,
            'structure' => is_array($structure) ? json_encode($structure, JSON_UNESCAPED_UNICODE) : $structure,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        LpNewActivityLog::log('created_landing_page', $page);

        return redirect()->route('admin.landing-pages-new.builder', $page)->with('success', 'تم إنشاء صفحة الهبوط بنجاح، يمكنك الآن البناء والتعديل.');
    }

    public function edit(LpNewLandingPage $landingPage)
    {
        $brands = Brand::where('is_active', true)->get();
        $forms = LeadForm::where('is_active', true)->get();
        $trackingIntegrations = TrackingIntegration::where('is_active', true)->get();

        return view('admin.landing-pages-new.edit', [
            'landingPage' => $landingPage,
            'brands' => $brands,
            'forms' => $forms,
            'trackingIntegrations' => $trackingIntegrations,
        ]);
    }

    public function update(Request $request, LpNewLandingPage $landingPage)
    {
        $validated = $request->validate([
            'internal_name' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:lp_new_landing_pages,slug,' . $landingPage->id],
            'brand_id' => ['nullable', 'exists:lp_new_brands,id'],
            'assigned_lead_form_id' => ['nullable', 'exists:lead_forms,id'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_active' => ['nullable', 'boolean'],
            'seo_title_ar' => ['nullable', 'string', 'max:255'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_description_ar' => ['nullable', 'string'],
            'seo_description_en' => ['nullable', 'string'],
            'custom_css' => ['nullable', 'string'],
            'custom_js' => ['nullable', 'string'],
            'custom_html_head' => ['nullable', 'string'],
        ]);

        $beforeState = $landingPage->toArray();

        $validated['is_active'] = $request->boolean('is_active');
        $validated['updated_by'] = auth()->id();

        $landingPage->update($validated);

        LpNewActivityLog::log('updated_landing_page_settings', $landingPage, $beforeState, $landingPage->fresh()->toArray());

        return redirect()->route('admin.landing-pages-new.index')->with('success', 'تم تحديث بيانات وإعدادات صفحة الهبوط بنجاح.');
    }

    public function toggleActive(LpNewLandingPage $landingPage)
    {
        $landingPage->is_active = ! $landingPage->is_active;
        $landingPage->save();

        LpNewActivityLog::log($landingPage->is_active ? 'activated_landing_page' : 'deactivated_landing_page', $landingPage);

        return back()->with('success', $landingPage->is_active ? 'تم تفعيل الصفحة بنجاح.' : 'تم إيقاف تفعيل الصفحة.');
    }

    public function duplicate(LpNewLandingPage $landingPage)
    {
        $copy = $landingPage->replicate();
        $copy->internal_name = $landingPage->internal_name . ' - نسخة';
        $copy->title_ar = $landingPage->title_ar . ' - نسخة';
        $copy->title_en = trim($landingPage->title_en . ' Copy');
        $copy->slug = LpNewLandingPage::makeUniqueSlug($landingPage->slug . '-copy');
        $copy->status = LpNewLandingPage::STATUS_DRAFT;
        $copy->is_active = false;
        $copy->created_by = auth()->id();
        $copy->updated_by = auth()->id();
        $copy->save();

        LpNewActivityLog::log('duplicated_landing_page', $copy);

        return redirect()->route('admin.landing-pages-new.edit', $copy)->with('success', 'تم تكرار صفحة الهبوط بنجاح.');
    }

    public function destroy(LpNewLandingPage $landingPage)
    {
        LpNewActivityLog::log('deleted_landing_page', $landingPage);
        $landingPage->delete();

        return redirect()->route('admin.landing-pages-new.index')->with('success', 'تم نقل صفحة الهبوط إلى سلة المهملات.');
    }

    public function importZip(Request $request, LandingPageNewImporter $importer)
    {
        $request->validate([
            'zip_file' => ['required', 'file', 'mimes:zip', 'max:51200'],
            'internal_name' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $overrides = [
                'internal_name' => $request->filled('internal_name') ? $request->input('internal_name') : null,
                'slug' => ($request->filled('slug') && $request->input('slug') !== 'null') ? $request->input('slug') : null,
            ];

            $landingPage = $importer->importFromZip(
                $request->file('zip_file'),
                $overrides
            );

            LpNewActivityLog::log('imported_zip_package', $landingPage);

            return redirect()->route('admin.landing-pages-new.builder', $landingPage)
                ->with('success', 'تم استيراد وتفكيك حزمة الـ ZIP بنجاح!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.landing-pages-new.dashboard')->with('error', 'حدث خطأ أثناء استيراد وتفكيك ملف الـ ZIP: ' . $e->getMessage());
        }
    }

    public function exportZip(LpNewLandingPage $landingPage, LandingPageNewExporter $exporter)
    {
        try {
            $zipPath = $exporter->exportToZip($landingPage);
            LpNewActivityLog::log('exported_zip_package', $landingPage);

            return response()->download($zipPath, 'landing-page-' . $landingPage->slug . '.zip');
        } catch (\Throwable $e) {
            return back()->with('error', 'حدث خطأ أثناء تصدير حزمة الـ ZIP: ' . $e->getMessage());
        }
    }

    public function builder(LpNewLandingPage $landingPage)
    {
        $forms = LeadForm::where('is_active', true)->get();
        $sections = LpNewSection::where('is_active', true)->get();
        $templates = LpNewTemplate::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.landing-pages-new.builder', [
            'page' => $landingPage,
            'forms' => $forms,
            'sections' => $sections,
            'templates' => $templates,
            'brands' => $brands,
        ]);
    }

    public function builderV2(LpNewLandingPage $landingPage)
    {
        $forms = LeadForm::where('is_active', true)->get();
        $sections = LpNewSection::where('is_active', true)->get();
        $templates = LpNewTemplate::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.landing-pages-new.builder-v2', [
            'page' => $landingPage,
            'forms' => $forms,
            'sections' => $sections,
            'templates' => $templates,
            'brands' => $brands,
        ]);
    }

    public function updateBuilderV2(Request $request, LpNewLandingPage $landingPage)
    {
        $validated = $request->validate([
            'structure' => ['nullable'],
            'custom_css' => ['nullable', 'string'],
            'custom_js' => ['nullable', 'string'],
            'custom_html_head' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,published,archived'],
        ]);

        $beforeState = ['structure' => $landingPage->structure];

        $structure = $validated['structure'] ?? $landingPage->structure;
        if (is_array($structure)) {
            $structure = json_encode($structure, JSON_UNESCAPED_UNICODE);
        }

        $landingPage->update([
            'structure' => $structure,
            'custom_css' => $validated['custom_css'] ?? $landingPage->custom_css,
            'custom_js' => $validated['custom_js'] ?? $landingPage->custom_js,
            'custom_html_head' => $validated['custom_html_head'] ?? $landingPage->custom_html_head,
            'status' => $validated['status'] ?? $landingPage->status,
            'updated_by' => auth()->id(),
        ]);

        LpNewActivityLog::log('updated_builder_v2', $landingPage, $beforeState, ['structure' => $landingPage->structure]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ تغييرات Builder V2 بنجاح!',
            'page' => $landingPage->fresh(),
        ]);
    }

    public function updateBuilder(Request $request, LpNewLandingPage $landingPage)
    {
        $validated = $request->validate([
            'structure' => ['nullable'],
            'custom_css' => ['nullable', 'string'],
            'custom_js' => ['nullable', 'string'],
            'custom_html_head' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,published,archived'],
        ]);

        $beforeState = ['structure' => $landingPage->structure];

        $structure = $validated['structure'] ?? $landingPage->structure;
        if (is_array($structure)) {
            $structure = json_encode($structure, JSON_UNESCAPED_UNICODE);
        }

        $dataToUpdate = [
            'structure' => $structure,
            'custom_css' => $validated['custom_css'] ?? $landingPage->custom_css,
            'custom_js' => $validated['custom_js'] ?? $landingPage->custom_js,
            'custom_html_head' => $validated['custom_html_head'] ?? $landingPage->custom_html_head,
            'updated_by' => auth()->id(),
        ];

        if (! empty($validated['status'])) {
            $dataToUpdate['status'] = $validated['status'];
        }

        $landingPage->update($dataToUpdate);

        // Create snapshot version
        $latestVersionNum = (int) $landingPage->versions()->max('version_number');
        LpNewPageVersion::create([
            'landing_page_id' => $landingPage->id,
            'version_number' => $latestVersionNum + 1,
            'label' => 'حفظ ' . now()->format('Y-m-d H:i'),
            'structure' => $landingPage->structure,
            'custom_html_head' => $landingPage->custom_html_head,
            'custom_css' => $landingPage->custom_css,
            'custom_js' => $landingPage->custom_js,
            'created_by' => auth()->id(),
        ]);

        LpNewActivityLog::log('saved_builder_canvas', $landingPage, $beforeState, ['structure' => $landingPage->structure]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ التصميم وحفظ نسخة جديدة بنجاح.',
                'version' => $latestVersionNum + 1,
            ]);
        }

        return back()->with('success', 'تم حفظ التعديلات في البناء المرئي بنجاح.');
    }

    public function autosave(Request $request, LpNewLandingPage $landingPage): JsonResponse
    {
        $structure = $request->input('structure');
        if (is_array($structure)) {
            $structure = json_encode($structure, JSON_UNESCAPED_UNICODE);
        }

        $landingPage->update([
            'structure' => $structure,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الحفظ التلقائي كمسودة.',
            'time' => now()->format('H:i:s'),
        ]);
    }

    public function versions(LpNewLandingPage $landingPage)
    {
        $versions = $landingPage->versions()->with('createdBy')->get();

        return view('admin.landing-pages-new.versions', compact('landingPage', 'versions'));
    }

    public function restoreVersion(LpNewLandingPage $landingPage, LpNewPageVersion $version)
    {
        $landingPage->update([
            'structure' => $version->structure,
            'custom_html_head' => $version->custom_html_head,
            'custom_css' => $version->custom_css,
            'custom_js' => $version->custom_js,
            'updated_by' => auth()->id(),
        ]);

        LpNewActivityLog::log('restored_page_version', $landingPage, null, ['version_number' => $version->version_number]);

        return redirect()->route('admin.landing-pages-new.builder', $landingPage)->with('success', "تم استعادة النسخة رقم {$version->version_number} بنجاح.");
    }

    public function templates()
    {
        $templates = LpNewTemplate::with('category')->get();

        return view('admin.landing-pages-new.templates', compact('templates'));
    }

    public function sections()
    {
        $sections = LpNewSection::with('category')->get();

        return view('admin.landing-pages-new.sections', compact('sections'));
    }

    public function media()
    {
        $assets = LpNewAsset::with('landingPage')->latest()->paginate(24);

        return view('admin.landing-pages-new.media', compact('assets'));
    }

    public function leads()
    {
        $leads = Inquiry::whereNotNull('lead_form_id')->with('form')->latest()->paginate(20);

        return view('admin.landing-pages-new.leads', compact('leads'));
    }

    public function analytics()
    {
        $pages = LpNewLandingPage::where('status', LpNewLandingPage::STATUS_PUBLISHED)->get();

        return view('admin.landing-pages-new.analytics', compact('pages'));
    }

    public function settings()
    {
        $brands = Brand::all();

        return view('admin.landing-pages-new.settings', compact('brands'));
    }
}
