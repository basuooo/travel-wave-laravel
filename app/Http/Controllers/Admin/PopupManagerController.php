<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\LeadForm;
use App\Models\PopupManager\Popup;
use App\Models\PopupManager\PopupAnalytic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PopupManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        Popup::ensureTableSchema();
        PopupAnalytic::ensureTableSchema();

        $popups = Popup::with('assignedForm')->latest()->paginate(12);

        $totalPopups = Popup::count();
        $activeCount = Popup::where('is_active', true)->where('status', Popup::STATUS_ACTIVE)->count();
        $totalViews = Popup::sum('views_count');
        $totalClicks = Popup::sum('clicks_count');
        $totalConversions = Popup::sum('conversions_count');

        $overallCtr = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 2) : 0;
        $overallConversionRate = $totalViews > 0 ? round(($totalConversions / $totalViews) * 100, 2) : 0;

        return view('admin.popup-manager.dashboard', compact(
            'popups',
            'totalPopups',
            'activeCount',
            'totalViews',
            'totalClicks',
            'totalConversions',
            'overallCtr',
            'overallConversionRate'
        ));
    }

    public function index(Request $request)
    {
        Popup::ensureTableSchema();

        $query = Popup::with('assignedForm')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $popups = $query->paginate(15);
        $forms = LeadForm::where('is_active', true)->get();

        return view('admin.popup-manager.index', compact('popups', 'forms'));
    }

    public function create()
    {
        $forms = LeadForm::where('is_active', true)->get();

        return view('admin.popup-manager.create', compact('forms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'layout' => ['nullable', 'string'],
            'assigned_lead_form_id' => ['nullable', 'exists:lead_forms,id'],
            'trigger_mode' => ['nullable', 'string'],
        ]);

        $slug = Popup::makeUniqueSlug($validated['name']);

        $popup = Popup::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'status' => Popup::STATUS_DRAFT,
            'is_active' => true,
            'priority' => 10,
            'layout' => $validated['layout'] ?? 'center',
            'assigned_lead_form_id' => $validated['assigned_lead_form_id'] ?? null,
            'trigger_settings' => [
                'mode' => $validated['trigger_mode'] ?? 'random_time',
                'delay_seconds' => 10,
                'min_delay_seconds' => 20,
                'max_delay_seconds' => 60,
                'scroll_percentage' => 50,
                'min_scroll_percentage' => 30,
                'max_scroll_percentage' => 70,
                'exit_intent' => false,
                'visit_count' => 1,
            ],
            'condition_settings' => [
                'pages_mode' => 'all',
                'devices' => ['desktop' => true, 'tablet' => true, 'mobile' => true],
            ],
            'frequency_settings' => [
                'mode' => 'once_per_session',
                'max_displays' => 1,
            ],
            'structure' => [
                'html' => '<div class="p-4 bg-white rounded-4 text-center shadow-lg"><h3 class="fw-bold mb-2">🔥 عرض خاص ومريح لك!</h3><p class="text-muted mb-4">أدخل بياناتك للحصول على خصم إضافي واستشارة مجانية فورية.</p><button type="button" class="btn btn-success btn-lg w-100 rounded-pill fw-bold">أطلب الآن</button></div>',
            ],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.popups.builder', $popup)->with('success', 'تم إنشاء الـ Popup بنجاح!');
    }

    public function builder(Popup $popup)
    {
        $forms = LeadForm::where('is_active', true)->get();
        $landingPages = \App\Models\LandingPageNew\LpNewLandingPage::where('is_active', true)->select('id', 'internal_name', 'title_ar', 'slug')->get();

        return view('admin.popup-manager.builder', compact('popup', 'forms', 'landingPages'));
    }

    public function updateBuilder(Request $request, Popup $popup)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string'],
            'priority' => ['nullable', 'integer'],
            'layout' => ['nullable', 'string'],
            'assigned_lead_form_id' => ['nullable'],
            'trigger_settings' => ['nullable'],
            'condition_settings' => ['nullable'],
            'frequency_settings' => ['nullable'],
            'schedule_settings' => ['nullable'],
            'size_settings' => ['nullable'],
            'overlay_settings' => ['nullable'],
            'close_button_settings' => ['nullable'],
            'animation_settings' => ['nullable'],
            'structure' => ['nullable'],
            'custom_css' => ['nullable', 'string'],
            'custom_js' => ['nullable', 'string'],
        ]);

        $popup->update([
            'name' => $validated['name'] ?? $popup->name,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $popup->is_active,
            'status' => $validated['status'] ?? $popup->status,
            'priority' => $validated['priority'] ?? $popup->priority,
            'layout' => $validated['layout'] ?? $popup->layout,
            'assigned_lead_form_id' => $validated['assigned_lead_form_id'] ?? $popup->assigned_lead_form_id,
            'trigger_settings' => $validated['trigger_settings'] ?? $popup->trigger_settings,
            'condition_settings' => $validated['condition_settings'] ?? $popup->condition_settings,
            'frequency_settings' => $validated['frequency_settings'] ?? $popup->frequency_settings,
            'schedule_settings' => $validated['schedule_settings'] ?? $popup->schedule_settings,
            'size_settings' => $validated['size_settings'] ?? $popup->size_settings,
            'overlay_settings' => $validated['overlay_settings'] ?? $popup->overlay_settings,
            'close_button_settings' => $validated['close_button_settings'] ?? $popup->close_button_settings,
            'animation_settings' => $validated['animation_settings'] ?? $popup->animation_settings,
            'structure' => $validated['structure'] ?? $popup->structure,
            'custom_css' => $validated['custom_css'] ?? $popup->custom_css,
            'custom_js' => $validated['custom_js'] ?? $popup->custom_js,
            'updated_by' => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الـ Popup بنجاح!',
                'popup' => $popup->fresh(),
            ]);
        }

        return redirect()->route('admin.popups.builder', $popup)->with('success', 'تم حفظ الـ Popup بنجاح!');
    }

    public function toggleActive(Popup $popup)
    {
        $popup->update(['is_active' => ! $popup->is_active]);

        return back()->with('success', 'تم تغيير حالة التفعيل بنجاح!');
    }

    public function duplicate(Popup $popup)
    {
        $newPopup = $popup->replicate();
        $newPopup->name = $popup->name . ' (نسخة)';
        $newPopup->slug = Popup::makeUniqueSlug($newPopup->name);
        $newPopup->views_count = 0;
        $newPopup->clicks_count = 0;
        $newPopup->conversions_count = 0;
        $newPopup->save();

        return redirect()->route('admin.popups.builder', $newPopup)->with('success', 'تم تكرار الـ Popup بنجاح!');
    }

    public function destroy(Popup $popup)
    {
        $popup->delete();

        return redirect()->route('admin.popups.dashboard')->with('success', 'تم حذف الـ Popup بنجاح!');
    }

    public function analytics(Popup $popup)
    {
        $impressions = PopupAnalytic::where('popup_id', $popup->id)->where('event_type', PopupAnalytic::EVENT_IMPRESSION)->count();
        $clicks = PopupAnalytic::where('popup_id', $popup->id)->where('event_type', PopupAnalytic::EVENT_CLICK)->count();
        $conversions = PopupAnalytic::where('popup_id', $popup->id)->where('event_type', PopupAnalytic::EVENT_CONVERSION)->count();
        $closes = PopupAnalytic::where('popup_id', $popup->id)->where('event_type', PopupAnalytic::EVENT_CLOSE)->count();

        $recentAnalytics = PopupAnalytic::where('popup_id', $popup->id)->latest()->take(50)->get();

        return view('admin.popup-manager.analytics', compact(
            'popup',
            'impressions',
            'clicks',
            'conversions',
            'closes',
            'recentAnalytics'
        ));
    }
}
