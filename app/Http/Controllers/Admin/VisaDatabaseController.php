<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        // Search query (Country name AR/EN or Visa Type)
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('visa_type', 'like', "%{$search}%")
                    ->orWhereHas('country', function ($cq) use ($search) {
                        $cq->where('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%");
                    });
            });
        }

        // Category filter
        if ($categoryId = $request->input('category_id')) {
            $query->whereHas('country', function ($cq) use ($categoryId) {
                $cq->where('visa_category_id', $categoryId)
                    ->orWhereHas('categories', fn ($catQ) => $catQ->where('visa_categories.id', $categoryId));
            });
        }

        // Country filter
        if ($countryId = $request->input('country_id')) {
            $query->where('visa_country_id', $countryId);
        }

        // Visa Type filter
        if ($visaType = $request->input('visa_type')) {
            $query->where('visa_type', $visaType);
        }

        // Application Center filter
        if ($center = $request->input('application_center')) {
            $query->where(function ($q) use ($center) {
                $q->whereJsonContains('application_center', $center)
                    ->orWhere('application_center', 'like', "%{$center}%");
            });
        }

        // Biometrics filter
        if ($request->has('is_biometrics_required') && $request->input('is_biometrics_required') !== '') {
            $query->where('is_biometrics_required', $request->boolean('is_biometrics_required'));
        }

        // Interview filter
        if ($request->has('is_interview_required') && $request->input('is_interview_required') !== '') {
            $query->where('is_interview_required', $request->boolean('is_interview_required'));
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $records = $query->orderBy('sort_order')->orderByDesc('id')->paginate(25)->withQueryString();

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
            $record->country?->categories()->sync($request->input('category_ids'));
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
            $visa_record->country?->categories()->sync($request->input('category_ids'));
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
        if (! in_array($newStatus, ['active', 'temporarily_unavailable', 'inactive'], true)) {
            return back()->with('error', 'حالة غير صالحة.');
        }

        $oldLabel = $visa_record->status_label;
        $visa_record->status = $newStatus;
        $visa_record->save();

        VisaActivityLog::create([
            'visa_record_id' => $visa_record->id,
            'visa_country_id' => $visa_record->visa_country_id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?: 'System',
            'action' => 'تغيير الحالة',
            'description' => "تم تغيير حالة تأشيرة " . ($visa_record->country?->name_ar ?: '') . " من ({$oldLabel}) إلى ({$visa_record->status_label})",
        ]);

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
}
