<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmbassyAppointment;
use App\Models\EmbassyAppointmentLog;
use App\Models\EmbassyAppointmentNotification;
use App\Models\VisaCountry;
use App\Models\VisaRecord;
use App\Support\CrmLeadAccess;
use App\Support\EmbassyAppointmentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmbassyAppointmentController extends Controller
{
    protected EmbassyAppointmentService $service;

    public function __construct(EmbassyAppointmentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        EmbassyAppointment::ensureTableSchema();
        $this->ensureSchengenCountriesExist();

        $query = EmbassyAppointment::query()
            ->with(['country', 'updatedBy'])
            ->latest('last_updated_at');

        if ($request->filled('q')) {
            $needle = '%' . trim((string) $request->query('q')) . '%';
            $query->whereHas('country', function ($q) use ($needle) {
                $q->where('name_ar', 'like', $needle)
                  ->orWhere('name_en', 'like', $needle);
            });
        }

        if ($request->filled('visa_country_id')) {
            $query->where('visa_country_id', $request->integer('visa_country_id'));
        }

        if ($request->filled('visa_type')) {
            $query->where('visa_type', $request->string('visa_type')->toString());
        }

        if ($request->filled('appointment_center')) {
            $query->where('appointment_center', $request->string('appointment_center')->toString());
        }

        if ($request->filled('appointment_type')) {
            $query->where('appointment_type', $request->string('appointment_type')->toString());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $appointments = $query->paginate(15)->withQueryString();

        // Calculate waiting leads count for listed items
        $appointments->getCollection()->transform(function (EmbassyAppointment $appt) {
            $appt->setAttribute('waiting_leads_count', $this->service->countWaitingLeads($appt));
            return $appt;
        });

        // Summary metrics
        $allAppointments = EmbassyAppointment::with('country')->get();
        $totalWaitingLeads = 0;
        foreach ($allAppointments as $appt) {
            $totalWaitingLeads += $this->service->countWaitingLeads($appt);
        }

        $summary = [
            'total' => $allAppointments->count(),
            'available_now' => $allAppointments->where('status', EmbassyAppointment::STATUS_AVAILABLE_NOW)->count(),
            'available_later' => $allAppointments->where('status', EmbassyAppointment::STATUS_AVAILABLE_LATER)->count(),
            'no_availability' => $allAppointments->where('status', EmbassyAppointment::STATUS_NO_AVAILABILITY)->count(),
            'unknown' => $allAppointments->where('status', EmbassyAppointment::STATUS_UNKNOWN)->count(),
            'waiting_leads' => $totalWaitingLeads,
        ];

        $countries = VisaCountry::orderBy('name_ar')->get();
        $trashedCount = EmbassyAppointment::onlyTrashed()->count();

        return view('admin.embassy-appointments.index', [
            'items' => $appointments,
            'summary' => $summary,
            'countries' => $countries,
            'trashedCount' => $trashedCount,
        ]);
    }

    public function syncSeed()
    {
        try {
            if (class_exists(\App\Models\VisaCategory::class)) {
                try { \App\Models\VisaCategory::ensureTableSchema(); } catch (\Throwable $e) {}
            }
            if (class_exists(\App\Models\VisaCountry::class)) {
                try { \App\Models\VisaCountry::ensureTableSchema(); } catch (\Throwable $e) {}
            }

            EmbassyAppointment::ensureTableSchema();
            EmbassyAppointment::autoSeedAppointmentsFromImage();

            $count = EmbassyAppointment::count();
            $countriesCount = VisaCountry::count();

            return redirect()->route('admin.embassy-appointments.index')
                ->with('success', "تم مزامنة وإدراج {$count} موعد سفارة بنجاح 🟢 (إجمالي الدول: {$countriesCount})");
        } catch (\Throwable $e) {
            return redirect()->route('admin.embassy-appointments.index')
                ->with('error', "حدث خطأ أثناء المزامنة: " . $e->getMessage());
        }
    }

    protected function ensureSchengenCountriesExist(): void
    {
        try {
            $category = \App\Models\VisaCategory::first() ?? \App\Models\VisaCategory::withTrashed()->first();
            if (! $category && \Illuminate\Support\Facades\Schema::hasTable('visa_categories')) {
                try {
                    $category = \App\Models\VisaCategory::create([
                        'name_ar' => 'شنغن (Schengen)',
                        'name_en' => 'Schengen',
                        'slug' => 'schengen',
                        'is_active' => true,
                    ]);
                } catch (\Throwable $e) {}
            }
            $catId = $category?->id ?? 1;

            $list = [
                ['name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'slug' => 'germany'],
                ['name_ar' => 'إسبانيا', 'name_en' => 'Spain', 'slug' => 'spain'],
                ['name_ar' => 'اليونان', 'name_en' => 'Greece', 'slug' => 'greece'],
                ['name_ar' => 'المجر', 'name_en' => 'Hungary', 'slug' => 'hungary'],
                ['name_ar' => 'هولندا', 'name_en' => 'Netherlands', 'slug' => 'netherlands'],
                ['name_ar' => 'البرتغال', 'name_en' => 'Portugal', 'slug' => 'portugal'],
                ['name_ar' => 'السويد', 'name_en' => 'Sweden', 'slug' => 'sweden'],
                ['name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'slug' => 'italy'],
                ['name_ar' => 'سويسرا', 'name_en' => 'Switzerland', 'slug' => 'switzerland'],
                ['name_ar' => 'كرواتيا', 'name_en' => 'Croatia', 'slug' => 'croatia'],
                ['name_ar' => 'بلجيكا', 'name_en' => 'Belgium', 'slug' => 'belgium'],
                ['name_ar' => 'فرنسا', 'name_en' => 'France', 'slug' => 'france'],
                ['name_ar' => 'النمسا', 'name_en' => 'Austria', 'slug' => 'austria'],
                ['name_ar' => 'النرويج', 'name_en' => 'Norway', 'slug' => 'norway'],
            ];

            foreach ($list as $item) {
                $cleanAr = preg_replace('/[أإآ]/u', 'ا', $item['name_ar']);
                $c = VisaCountry::where('slug', $item['slug'])
                    ->orWhere('name_en', 'like', $item['name_en'])
                    ->orWhere('name_ar', 'like', '%' . $cleanAr . '%')
                    ->first();

                if (! $c) {
                    try {
                        $c = new VisaCountry();
                        $c->visa_category_id = $catId;
                        $c->name_ar = $item['name_ar'];
                        $c->name_en = $item['name_en'];
                        $c->slug = $item['slug'];
                        $c->is_active = true;
                        $c->save();
                    } catch (\Throwable $ex) {}
                }
            }
        } catch (\Throwable $e) {}
    }

    public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'appointments' => ['required', 'array', 'min:1'],
            'appointments.*.visa_country_id' => ['required', 'exists:visa_countries,id'],
            'appointments.*.visa_type' => ['required', 'string', 'max:100'],
            'appointments.*.appointment_center' => ['required', 'string', 'max:100'],
            'appointments.*.appointment_type' => ['required', 'string', 'max:100'],
            'appointments.*.status' => ['required', 'string'],
            'appointments.*.earliest_date' => ['nullable', 'string', 'max:255'],
            'appointments.*.notes' => ['nullable', 'string'],
        ]);

        $savedCount = 0;
        foreach ($validated['appointments'] as $item) {
            $appt = EmbassyAppointment::withTrashed()->where([
                'visa_country_id' => $item['visa_country_id'],
                'visa_type' => $item['visa_type'],
                'appointment_center' => $item['appointment_center'],
                'appointment_type' => $item['appointment_type'],
            ])->first();

            if ($appt) {
                if ($appt->trashed()) {
                    $appt->restore();
                }
                $appt->update([
                    'status' => $item['status'],
                    'earliest_date' => $item['earliest_date'] ?? null,
                    'notes' => $item['notes'] ?? null,
                    'last_updated_at' => now(),
                    'updated_by' => auth()->id(),
                ]);
            } else {
                $appt = EmbassyAppointment::create([
                    'visa_country_id' => $item['visa_country_id'],
                    'visa_type' => $item['visa_type'],
                    'appointment_center' => $item['appointment_center'],
                    'appointment_type' => $item['appointment_type'],
                    'status' => $item['status'],
                    'earliest_date' => $item['earliest_date'] ?? null,
                    'notes' => $item['notes'] ?? null,
                    'last_updated_at' => now(),
                    'updated_by' => auth()->id(),
                ]);
            }

            if ($item['status'] === EmbassyAppointment::STATUS_AVAILABLE_NOW) {
                $this->service->updateStatus(
                    $appt,
                    EmbassyAppointment::STATUS_AVAILABLE_NOW,
                    $item['earliest_date'] ?? null,
                    $item['notes'] ?? null,
                    auth()->user()
                );
            }

            $savedCount++;
        }

        return redirect()->route('admin.embassy-appointments.index')
            ->with('success', "تم إضافة وتحديث {$savedCount} موعد سفارة بنجاح 🟢");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'visa_country_id' => ['required', 'exists:visa_countries,id'],
            'visa_type' => ['required', 'string', 'max:100'],
            'appointment_center' => ['required', 'string', 'max:100'],
            'appointment_type' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', Rule::in([
                EmbassyAppointment::STATUS_AVAILABLE_NOW,
                EmbassyAppointment::STATUS_AVAILABLE_LATER,
                EmbassyAppointment::STATUS_NO_AVAILABILITY,
                EmbassyAppointment::STATUS_UNKNOWN,
            ])],
            'earliest_date' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'booking_link' => ['nullable', 'string', 'max:500'],
        ]);

        $bookingLink = $validated['booking_link'] ?? null;

        $appointment = EmbassyAppointment::withTrashed()->where([
            'visa_country_id' => $validated['visa_country_id'],
            'visa_type' => $validated['visa_type'],
            'appointment_center' => $validated['appointment_center'],
            'appointment_type' => $validated['appointment_type'],
        ])->first();

        if ($appointment) {
            if ($appointment->trashed()) {
                $appointment->restore();
            }
            $appointment->update([
                'booking_link' => $bookingLink,
                'status' => $validated['status'],
            ]);
        } else {
            $appointment = EmbassyAppointment::create([
                'visa_country_id' => $validated['visa_country_id'],
                'visa_type' => $validated['visa_type'],
                'appointment_center' => $validated['appointment_center'],
                'appointment_type' => $validated['appointment_type'],
                'booking_link' => $bookingLink,
                'status' => $validated['status'],
            ]);
        }

        $this->service->updateStatus(
            $appointment,
            $validated['status'],
            $validated['earliest_date'] ?? null,
            $validated['notes'] ?? null,
            auth()->user()
        );

        return redirect()->route('admin.embassy-appointments.index')
            ->with('success', 'تم حفظ موعد السفارة وتحديث حالته بنجاح 🟢');
    }

    public function update(Request $request, EmbassyAppointment $embassyAppointment)
    {
        $validated = $request->validate([
            'visa_country_id' => ['required', 'exists:visa_countries,id'],
            'visa_type' => ['required', 'string', 'max:100'],
            'appointment_center' => ['required', 'string', 'max:100'],
            'appointment_type' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', Rule::in([
                EmbassyAppointment::STATUS_AVAILABLE_NOW,
                EmbassyAppointment::STATUS_AVAILABLE_LATER,
                EmbassyAppointment::STATUS_NO_AVAILABILITY,
                EmbassyAppointment::STATUS_UNKNOWN,
            ])],
            'earliest_date' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'booking_link' => ['nullable', 'string', 'max:500'],
        ]);

        $existing = EmbassyAppointment::withTrashed()
            ->where('visa_country_id', $validated['visa_country_id'])
            ->where('visa_type', $validated['visa_type'])
            ->where('appointment_center', $validated['appointment_center'])
            ->where('appointment_type', $validated['appointment_type'])
            ->where('id', '!=', $embassyAppointment->id)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $target = $existing;
            $embassyAppointment->delete();
        } else {
            $target = $embassyAppointment;
            $target->visa_country_id = $validated['visa_country_id'];
            $target->visa_type = $validated['visa_type'];
            $target->appointment_center = $validated['appointment_center'];
            $target->appointment_type = $validated['appointment_type'];
            $target->booking_link = $validated['booking_link'] ?? null;
            $target->save();
        }

        $this->service->updateStatus(
            $target,
            $validated['status'],
            $request->input('earliest_date'),
            $request->input('notes'),
            auth()->user()
        );

        return redirect()->route('admin.embassy-appointments.index')
            ->with('success', 'تم تحديث موعد السفارة وحفظ حالته الجديدة بنجاح 🟢');
    }

    public function toggleAvailableNow(EmbassyAppointment $embassyAppointment)
    {
        $this->service->updateStatus(
            $embassyAppointment,
            EmbassyAppointment::STATUS_AVAILABLE_NOW,
            $embassyAppointment->earliest_date,
            $embassyAppointment->notes,
            auth()->user()
        );

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تغيير حالة الموعد إلى: مواعيد متاحة الآن 🟢',
            ]);
        }

        return redirect()->back()->with('success', '🟢 تم تغيير الحالة إلى: مواعيد متاحة الآن، وتنبيه البائعين للـ Leads المطابقة.');
    }

    public function toggleNoAvailability(EmbassyAppointment $embassyAppointment)
    {
        $this->service->updateStatus(
            $embassyAppointment,
            EmbassyAppointment::STATUS_NO_AVAILABILITY,
            null,
            $embassyAppointment->notes,
            auth()->user()
        );

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تغيير حالة الموعد إلى: لا توجد مواعيد 🔴',
            ]);
        }

        return redirect()->back()->with('success', '🔴 تم تغيير الحالة إلى: لا توجد مواعيد.');
    }

    public function updateQuickStatus(EmbassyAppointment $embassyAppointment, Request $request)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([
                EmbassyAppointment::STATUS_AVAILABLE_NOW,
                EmbassyAppointment::STATUS_AVAILABLE_LATER,
                EmbassyAppointment::STATUS_NO_AVAILABILITY,
                EmbassyAppointment::STATUS_UNKNOWN,
            ])],
        ]);

        $this->service->updateStatus(
            $embassyAppointment,
            $validated['status'],
            $embassyAppointment->earliest_date,
            $embassyAppointment->notes,
            auth()->user()
        );

        $statusLabels = [
            EmbassyAppointment::STATUS_AVAILABLE_NOW => '🟢 متاحة الآن',
            EmbassyAppointment::STATUS_AVAILABLE_LATER => '🟡 متاحة مستقبلاً',
            EmbassyAppointment::STATUS_NO_AVAILABILITY => '🔴 لا توجد مواعيد',
            EmbassyAppointment::STATUS_UNKNOWN => '⚪ غير معروف',
        ];

        $label = $statusLabels[$validated['status']] ?? $validated['status'];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "تم تغيير حالة الموعد بنجاح إلى: {$label}",
            ]);
        }

        return redirect()->back()->with('success', "تم تغيير حالة الموعد بنجاح إلى: {$label}");
    }

    public function show(EmbassyAppointment $embassyAppointment)
    {
        $embassyAppointment->load([
            'country',
            'updatedBy',
            'events.triggeredBy',
            'events.notifications.lead.assignedUser',
            'logs.user',
        ]);

        $waitingLeadsCount = $this->service->countWaitingLeads($embassyAppointment);

        // Group notifications of the latest event by seller
        $latestEvent = $embassyAppointment->events->first();
        $notificationsBySeller = collect();

        if ($latestEvent) {
            $notificationsBySeller = $latestEvent->notifications->groupBy('seller_id');
        }

        return view('admin.embassy-appointments.show', [
            'item' => $embassyAppointment,
            'waitingLeadsCount' => $waitingLeadsCount,
            'latestEvent' => $latestEvent,
            'notificationsBySeller' => $notificationsBySeller,
        ]);
    }

    public function destroy(EmbassyAppointment $embassyAppointment)
    {
        $embassyAppointment->update(['deleted_by' => auth()->id()]);
        $embassyAppointment->delete();

        return redirect()->route('admin.embassy-appointments.index')
            ->with('success', 'تم نقل موعد السفارة إلى سلة المحذوفات 🗑️');
    }

    public function trash()
    {
        $deletedItems = EmbassyAppointment::onlyTrashed()
            ->with(['country'])
            ->latest('deleted_at')
            ->get();

        return view('admin.embassy-appointments.trash', [
            'items' => $deletedItems,
        ]);
    }

    public function restore($id)
    {
        $appointment = EmbassyAppointment::onlyTrashed()->findOrFail($id);
        $appointment->restore();
        $appointment->update(['deleted_by' => null]);

        return redirect()->route('admin.embassy-appointments.trash')
            ->with('success', 'تم استعادة موعد السفارة من سلة المحذوفات بنجاح 🟢');
    }

    public function forceDelete($id)
    {
        $appointment = EmbassyAppointment::onlyTrashed()->findOrFail($id);
        $appointment->forceDelete();

        return redirect()->route('admin.embassy-appointments.trash')
            ->with('success', 'تم حذف موعد السفارة بشكل نهائي ❌');
    }

    // --- Seller Popup API Endpoints ---

    public function getPendingPopups()
    {
        EmbassyAppointment::ensureTableSchema();

        $user = auth()->user();
        if (! $user) {
            return response()->json(['items' => []]);
        }

        try {
            $this->service->syncAllAvailableNowAppointments();
        } catch (\Throwable $e) {
            logger()->error('syncAllAvailableNowAppointments error: ' . $e->getMessage());
        }

        $notifications = $this->service->getSellerPendingNotifications($user);

        $data = $notifications->map(function (EmbassyAppointmentNotification $notif) {
            return [
                'id' => $notif->id,
                'status' => $notif->status,
                'lead_id' => $notif->inquiry_id,
                'lead_name' => $notif->lead?->full_name ?? 'عميل',
                'lead_phone' => $notif->lead?->phone ?? '',
                'lead_url' => route('admin.crm.leads.show', $notif->inquiry_id),
                'country_name' => $notif->appointment?->country_name ?? '',
                'visa_type' => $notif->appointment?->visa_type ?? '',
                'appointment_center' => $notif->appointment?->appointment_center ?? '',
                'appointment_type' => $notif->appointment?->appointment_type ?? '',
                'earliest_date' => $notif->appointment?->earliest_date ?? 'غير محدد',
                'booking_link' => $notif->appointment?->booking_link ?? '',
            ];
        });

        return response()->json([
            'count' => $data->count(),
            'items' => $data->values(),
        ]);
    }

    public function handleContact(Request $request, EmbassyAppointmentNotification $notification)
    {
        $user = auth()->user();
        if ((int) $notification->seller_id !== (int) $user->id && ! CrmLeadAccess::canViewAll($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'contact_result' => ['required', 'string', Rule::in([
                'agreed', 'no_answer', 'call_later', 'not_ready', 'refused', 'other'
            ])],
            'contact_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->service->markContacted(
            $notification,
            $validated['contact_result'],
            $validated['contact_notes'] ?? null,
            $user
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الاتصال بالعميل بنجاح.',
        ]);
    }

    public function handleSnooze(Request $request, EmbassyAppointmentNotification $notification)
    {
        $user = auth()->user();
        if ((int) $notification->seller_id !== (int) $user->id && ! CrmLeadAccess::canViewAll($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'snooze_option' => ['required', 'string', Rule::in([
                '15', '30', '60', '120', 'tomorrow', 'custom'
            ])],
            'custom_time' => ['nullable', 'required_if:snooze_option,custom', 'date'],
        ]);

        $this->service->snooze(
            $notification,
            $validated['snooze_option'],
            $validated['custom_time'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تأجيل التنبيه بنجاح.',
        ]);
    }
}
