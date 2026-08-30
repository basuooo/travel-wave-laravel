<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadForm;
use App\Models\MapSection;
use App\Models\PublicCatalogSetting;
use App\Models\VisaActivityLog;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use App\Models\VisaRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisaDatabaseController extends Controller
{
    public function index(Request $request)
    {
        VisaRecord::ensureTableSchema();

        $query = VisaRecord::query()->with(['country.categories', 'activityLogs']);

        // Search query (Country name AR/EN, Visa Type, Notes, Documents, or Category name)
        if ($request->filled('search')) {
            $rawSearch = trim($request->input('search'));
            $normalizedSearch = preg_replace('/[أإآ]/u', 'ا', preg_replace('/[\x{064B}-\x{0652}\x{0670}]/u', '', $rawSearch));

            $query->where(function ($q) use ($rawSearch, $normalizedSearch) {
                $q->where('visa_type', 'like', "%{$rawSearch}%")
                    ->orWhere('visa_type', 'like', "%{$normalizedSearch}%")
                    ->orWhere('notes', 'like', "%{$rawSearch}%")
                    ->orWhere('notes', 'like', "%{$normalizedSearch}%")
                    ->orWhere('required_documents', 'like', "%{$rawSearch}%")
                    ->orWhere('required_documents', 'like', "%{$normalizedSearch}%")
                    ->orWhereHas('country', function ($cq) use ($rawSearch, $normalizedSearch) {
                        $cq->withTrashed()->where(function ($cInner) use ($rawSearch, $normalizedSearch) {
                            $cInner->where('name_ar', 'like', "%{$rawSearch}%")
                                ->orWhere('name_ar', 'like', "%{$normalizedSearch}%")
                                ->orWhere('name_en', 'like', "%{$rawSearch}%")
                                ->orWhereHas('categories', function ($catQ) use ($rawSearch, $normalizedSearch) {
                                    $catQ->where('name_ar', 'like', "%{$rawSearch}%")
                                        ->orWhere('name_ar', 'like', "%{$normalizedSearch}%")
                                        ->orWhere('name_en', 'like', "%{$rawSearch}%");
                                });
                        });
                    });
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $categoryId = $request->input('category_id');
            $query->whereHas('country', function ($cq) use ($categoryId) {
                $cq->withTrashed()->where(function ($cInner) use ($categoryId) {
                    $cInner->where('visa_category_id', $categoryId)
                        ->orWhereHas('categories', fn ($catQ) => $catQ->where('visa_categories.id', $categoryId));
                });
            });
        }

        // Country filter
        if ($request->filled('country_id')) {
            $query->where('visa_country_id', $request->input('country_id'));
        }

        // Visa Type filter
        if ($request->filled('visa_type')) {
            $query->where('visa_type', $request->input('visa_type'));
        }

        // Application Center filter
        if ($request->filled('application_center')) {
            $center = $request->input('application_center');
            $query->where(function ($q) use ($center) {
                $q->whereJsonContains('application_center', $center)
                    ->orWhere('application_center', 'like', "%{$center}%");
            });
        }

        // Biometrics filter
        if ($request->filled('is_biometrics_required')) {
            $val = $request->input('is_biometrics_required');
            $query->where('is_biometrics_required', (bool) (int) $val);
        }

        // Interview filter
        if ($request->filled('is_interview_required')) {
            $val = $request->input('is_interview_required');
            $query->where('is_interview_required', (bool) (int) $val);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPageInput = strtolower((string) $request->input('per_page', '25'));
        if ($perPageInput === 'all') {
            $perPage = 5000;
        } else {
            $perPage = in_array((int) $perPageInput, [10, 20, 25, 50, 100, 500]) ? (int) $perPageInput : 25;
        }

        $records = $query->orderBy('sort_order')->orderByDesc('id')->paginate($perPage)->withQueryString();

        $categories = VisaCategory::orderBy('sort_order')->get();
        $countries = VisaCountry::orderBy('name_ar')->get();
        $distinctVisaTypes = VisaRecord::distinct()->pluck('visa_type')->filter()->values();

        return view('admin.visa-database.index', compact('records', 'categories', 'countries', 'distinctVisaTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'visa_country_id' => ['required', 'exists:visa_countries,id'],
            'visa_type' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'working_days' => ['nullable', 'string', 'max:255'],
            'proposed_duration' => ['nullable', 'string', 'max:255'],
            'stay_duration' => ['nullable', 'string', 'max:255'],
            'entries_count' => ['nullable', 'string', 'max:255'],
            'required_documents' => ['nullable', 'string'],
            'embassy_fee' => ['nullable', 'string', 'max:255'],
            'embassy_fee_currency' => ['nullable', 'string', 'max:10'],
            'embassy_fee_payment_method' => ['nullable', 'string', 'max:255'],
            'application_center' => ['nullable', 'array'],
            'is_biometrics_required' => ['nullable', 'boolean'],
            'is_interview_required' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,temporarily_unavailable,inactive'],
        ]);

        $data['visa_type_slug'] = Str::slug($data['visa_type']);
        $data['is_biometrics_required'] = $request->boolean('is_biometrics_required');
        $data['is_interview_required'] = $request->boolean('is_interview_required');

        $record = VisaRecord::create($data);

        // Update country categories if provided
        if ($request->has('category_ids') && is_array($request->input('category_ids'))) {
            $catIds = array_filter(array_map('intval', $request->input('category_ids')));
            if ($record->country) {
                $record->country->categories()->sync($catIds);
                if (! empty($catIds)) {
                    $record->country->update(['visa_category_id' => reset($catIds)]);
                }
            }
        }

        VisaActivityLog::create([
            'visa_record_id' => $record->id,
            'visa_country_id' => $record->visa_country_id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?: 'System',
            'action' => 'أنشئ نوع تأشيرة جديد',
            'description' => "تم إنشاء تأشيرة {$record->visa_type} بسعر " . ($record->price ? number_format($record->price, 0) . " {$record->currency}" : "غير محدد"),
        ]);

        return redirect()->route('admin.visa-database.index')->with('success', 'تمت إضافة التأشيرة بنجاح.');
    }

    public function update(Request $request, VisaRecord $visa_record)
    {
        $data = $request->validate([
            'visa_country_id' => ['required', 'exists:visa_countries,id'],
            'visa_type' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'working_days' => ['nullable', 'string', 'max:255'],
            'proposed_duration' => ['nullable', 'string', 'max:255'],
            'stay_duration' => ['nullable', 'string', 'max:255'],
            'entries_count' => ['nullable', 'string', 'max:255'],
            'required_documents' => ['nullable', 'string'],
            'embassy_fee' => ['nullable', 'string', 'max:255'],
            'embassy_fee_currency' => ['nullable', 'string', 'max:10'],
            'embassy_fee_payment_method' => ['nullable', 'string', 'max:255'],
            'application_center' => ['nullable', 'array'],
            'is_biometrics_required' => ['nullable', 'boolean'],
            'is_interview_required' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,temporarily_unavailable,inactive'],
        ]);

        $data['visa_type_slug'] = Str::slug($data['visa_type']);
        $data['is_biometrics_required'] = $request->boolean('is_biometrics_required');
        $data['is_interview_required'] = $request->boolean('is_interview_required');

        // Audit Trail comparison
        $changes = [];
        if ((float) $visa_record->price !== (float) ($data['price'] ?? 0)) {
            $oldP = $visa_record->price !== null ? number_format($visa_record->price, 0) . ' ' . $visa_record->currency : 'غير محدد';
            $newP = ! empty($data['price']) ? number_format($data['price'], 0) . ' ' . $data['currency'] : 'غير محدد';
            $changes[] = "سعر التأشيرة (قديم: {$oldP} ➔ جديد: {$newP})";
        }

        if ($visa_record->status !== $data['status']) {
            $changes[] = "حالة التأشيرة (قديم: {$visa_record->status_label} ➔ جديد: " . match ($data['status']) { 'active' => 'نشطة', 'temporarily_unavailable' => 'متوقفة مؤقتاً', 'inactive' => 'غير متاحة', default => $data['status'] } . ")";
        }

        if ($visa_record->embassy_fee !== ($data['embassy_fee'] ?? null)) {
            $changes[] = "رسوم السفارة (قديم: " . ($visa_record->embassy_fee ?: 'غير محدد') . " ➔ جديد: " . ($data['embassy_fee'] ?: 'غير محدد') . ")";
        }

        if ($visa_record->working_days !== ($data['working_days'] ?? null)) {
            $changes[] = "أيام العمل (قديم: " . ($visa_record->working_days ?: 'غير محدد') . " ➔ جديد: " . ($data['working_days'] ?: 'غير محدد') . ")";
        }

        $visa_record->update($data);

        // Update country categories if provided
        if ($request->has('category_ids') && is_array($request->input('category_ids'))) {
            $catIds = array_filter(array_map('intval', $request->input('category_ids')));
            if ($visa_record->country) {
                $visa_record->country->categories()->sync($catIds);
                if (! empty($catIds)) {
                    $visa_record->country->update(['visa_category_id' => reset($catIds)]);
                }
            }
        }

        // Log to Activity Log
        $description = ! empty($changes)
            ? "تم تعديل: " . implode(' | ', $changes)
            : "تم تحديث بيانات التأشيرة";

        VisaActivityLog::create([
            'visa_record_id' => $visa_record->id,
            'visa_country_id' => $visa_record->visa_country_id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?: 'System',
            'action' => 'تعديل التأشيرة',
            'description' => $description,
            'old_value' => json_encode($visa_record->getOriginal(), JSON_UNESCAPED_UNICODE),
            'new_value' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('admin.visa-database.index')->with('success', 'تم حفظ التعديلات بنجاح.');
    }

    public function destroy(VisaRecord $visa_record)
    {
        VisaActivityLog::create([
            'visa_record_id' => null,
            'visa_country_id' => $visa_record->visa_country_id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?: 'System',
            'action' => 'حذف التأشيرة',
            'description' => "تم حذف نوع التأشيرة: {$visa_record->visa_type} للدولة " . ($visa_record->country?->name_ar ?: $visa_record->country?->name_en),
        ]);

        $visa_record->delete();

        return redirect()->route('admin.visa-database.index')->with('success', 'تم حذف التأشيرة.');
    }

    public function toggleStatus(Request $request, VisaRecord $visa_record)
    {
        $newStatus = $request->input('status');
        if ($request->has('is_active_toggle')) {
            $newStatus = $request->boolean('is_active_toggle') ? 'active' : 'inactive';
        }

        $isJsonReq = $request->expectsJson() || $request->ajax() || $request->wantsJson();

        if (! in_array($newStatus, ['active', 'temporarily_unavailable', 'inactive'], true)) {
            if ($isJsonReq) {
                return response()->json(['error' => 'حالة غير صالحة.'], 422);
            }
            return back()->with('error', 'حالة غير صالحة.');
        }

        $oldLabel = $visa_record->status_label;
        $visa_record->status = $newStatus;
        $visa_record->save();

        if ($visa_record->country) {
            if ($newStatus === 'active') {
                $visa_record->country->update(['is_active' => true]);
            } else {
                $hasActiveRecords = $visa_record->country->visaRecords()->where('status', 'active')->exists();
                if (! $hasActiveRecords) {
                    $visa_record->country->update(['is_active' => false]);
                }
            }
        }

        VisaActivityLog::create([
            'visa_record_id' => $visa_record->id,
            'visa_country_id' => $visa_record->visa_country_id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?: 'System',
            'action' => 'تغيير الحالة',
            'description' => "تم تغيير حالة تأشيرة " . ($visa_record->country?->name_ar ?: '') . " من ({$oldLabel}) إلى ({$visa_record->status_label})",
        ]);

        if ($isJsonReq) {
            return response()->json([
                'success' => true,
                'status' => $visa_record->status,
                'status_label' => $visa_record->status_label,
                'status_badge_class' => $visa_record->status_badge_class,
                'is_active' => $visa_record->status === 'active',
                'message' => 'تم تحديث حالة التأشيرة بنجاح.',
            ]);
        }

        return back()->with('success', 'تم تحديث حالة التأشيرة بنجاح.');
    }

    public function activityLogs(VisaRecord $visa_record)
    {
        $logs = $visa_record->activityLogs()->take(50)->get();

        return response()->json([
            'success' => true,
            'country_name' => $visa_record->country?->name_ar ?: $visa_record->country?->name_en,
            'visa_type' => $visa_record->visa_type,
            'logs' => $logs,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
        ]);

        $data['slug'] = VisaCategory::makeUniqueSlug($data['name_en'] ?: $data['name_ar']);
        $data['sort_order'] = VisaCategory::max('sort_order') + 1;
        $data['is_active'] = true;

        VisaCategory::create($data);

        return back()->with('success', 'تمت إضافة التصنيف الجديد بنجاح.');
    }

    public function catalogSettings()
    {
        $setting = PublicCatalogSetting::getSettings();
        $leadForms = LeadForm::where('is_active', true)->orderBy('name')->get();
        $mapSections = MapSection::where('is_active', true)->orderBy('title_ar')->get();

        return view('admin.visa-database.catalog_settings', compact('setting', 'leadForms', 'mapSections'));
    }

    public function updateCatalogSettings(Request $request)
    {
        $setting = PublicCatalogSetting::getSettings();

        $setting->show_price = $request->boolean('show_price');
        $setting->show_embassy_fee = $request->boolean('show_embassy_fee');
        $setting->show_working_days = $request->boolean('show_working_days');
        $setting->show_biometrics = $request->boolean('show_biometrics');
        $setting->show_interview = $request->boolean('show_interview');
        $setting->show_notes = $request->boolean('show_notes');
        $setting->show_preview_button = $request->boolean('show_preview_button');
        $setting->floating_whatsapp_enabled = $request->boolean('floating_whatsapp_enabled');

        $setting->whatsapp_phone = $request->input('whatsapp_phone');
        $setting->whatsapp_message_template = $request->input('whatsapp_message_template');
        $setting->selected_lead_form_id = $request->input('selected_lead_form_id') ?: null;
        $setting->selected_map_section_id = $request->input('selected_map_section_id') ?: null;

        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('public_catalog', 'public');
            $setting->logo_path = $path;
        } elseif ($request->boolean('remove_logo')) {
            $setting->logo_path = null;
        }

        $setting->logo_width = max(30, (int) $request->input('logo_width', 180));
        $setting->logo_height = max(15, (int) $request->input('logo_height', 50));
        $setting->logo_keep_aspect_ratio = $request->boolean('logo_keep_aspect_ratio');

        $buttons = [];
        if ($request->has('custom_buttons') && is_array($request->input('custom_buttons'))) {
            foreach ($request->input('custom_buttons') as $btn) {
                if (filled($btn['title_ar'] ?? null) && filled($btn['url'] ?? null)) {
                    $buttons[] = [
                        'title_ar' => trim($btn['title_ar']),
                        'url' => trim($btn['url']),
                        'icon' => trim($btn['icon'] ?? 'bi-link-45deg'),
                        'button_class' => trim($btn['button_class'] ?? 'btn-outline-primary'),
                        'is_active' => ! empty($btn['is_active']),
                    ];
                }
            }
        }
        $setting->custom_buttons = $buttons;
        $setting->save();

        return back()->with('success', 'تم حفظ إعدادات الدليل العام للعملاء بنجاح.');
    }

    public function publicPreview($identifier, $id = null)
    {
        VisaRecord::ensureTableSchema();
        VisaCountry::ensureTableSchema();

        $record = null;

        if ($id) {
            $record = VisaRecord::with(['country.categories'])->find($id);
        }

        if (! $record && is_numeric($identifier)) {
            $record = VisaRecord::with(['country.categories'])->find($identifier);
        }

        if (! $record) {
            $rawSearch = trim((string) $identifier);
            $slugSearch = \Illuminate\Support\Str::slug($rawSearch);
            $normalizedSearch = preg_replace('/[أإآ]/u', 'ا', preg_replace('/[\x{064B}-\x{0652}\x{0670}]/u', '', $rawSearch));

            $record = VisaRecord::with(['country.categories'])
                ->where('status', 'active')
                ->whereHas('country', function ($cq) use ($rawSearch, $slugSearch, $normalizedSearch) {
                    $cq->where('slug', $rawSearch)
                        ->orWhere('slug', $slugSearch)
                        ->orWhere('name_en', 'like', "%{$rawSearch}%")
                        ->orWhere('name_en', 'like', "%{$slugSearch}%")
                        ->orWhere('name_ar', 'like', "%{$rawSearch}%")
                        ->orWhere('name_ar', 'like', "%{$normalizedSearch}%");
                })
                ->orderBy('sort_order')
                ->first();
        }

        if (! $record) {
            $record = VisaRecord::with(['country.categories'])->findOrFail($identifier);
        }

        $setting = PublicCatalogSetting::getSettings();

        $selectedLeadForm = $setting->selected_lead_form_id ? LeadForm::with('fields')->find($setting->selected_lead_form_id) : null;
        $selectedMapSection = $setting->selected_map_section_id ? MapSection::find($setting->selected_map_section_id) : null;

        return view('admin.visa-database.public_preview', compact('record', 'setting', 'selectedLeadForm', 'selectedMapSection'));
    }

    public function publicCatalog(Request $request)
    {
        VisaRecord::ensureTableSchema();
        $setting = PublicCatalogSetting::getSettings();

        $query = VisaRecord::query()
            ->with(['country.categories'])
            ->where('status', 'active')
            ->whereHas('country', function ($cq) {
                $cq->where('is_active', true);
            });

        if ($request->filled('search')) {
            $rawSearch = trim($request->input('search'));
            $normalizedSearch = preg_replace('/[أإآ]/u', 'ا', preg_replace('/[\x{064B}-\x{0652}\x{0670}]/u', '', $rawSearch));

            $query->where(function ($q) use ($rawSearch, $normalizedSearch) {
                $q->where('visa_type', 'like', "%{$rawSearch}%")
                    ->orWhere('visa_type', 'like', "%{$normalizedSearch}%")
                    ->orWhereHas('country', function ($cq) use ($rawSearch, $normalizedSearch) {
                        $cq->where('name_ar', 'like', "%{$rawSearch}%")
                            ->orWhere('name_ar', 'like', "%{$normalizedSearch}%")
                            ->orWhere('name_en', 'like', "%{$rawSearch}%");
                    });
            });
        }

        if ($request->filled('category_id')) {
            $categoryId = $request->input('category_id');
            $query->whereHas('country', function ($cq) use ($categoryId) {
                $cq->where('visa_category_id', $categoryId)
                    ->orWhereHas('categories', fn ($catQ) => $catQ->where('visa_categories.id', $categoryId));
            });
        }

        $records = $query->orderBy('sort_order')->paginate(24);
        $categories = VisaCategory::where('is_active', true)->orderBy('sort_order')->get();
        $selectedLeadForm = $setting->selected_lead_form_id ? LeadForm::with('fields')->find($setting->selected_lead_form_id) : null;
        $selectedMapSection = $setting->selected_map_section_id ? MapSection::find($setting->selected_map_section_id) : null;

        return view('admin.visa-database.public_catalog', compact('records', 'categories', 'setting', 'selectedLeadForm', 'selectedMapSection'));
    }
}
