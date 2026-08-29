<?php

namespace App\Support;

use App\Models\AccountingCustomerAccount;
use App\Models\CrmFollowUp;
use App\Models\CrmLeadCall;
use App\Models\CrmLeadNote;
use App\Models\CrmLeadSource;
use App\Models\CrmLeadWhatsApp;
use App\Models\CrmStatus;
use App\Models\CrmStatusUpdate;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CrmReportsCenterService
{
    public function __construct(
        protected CrmDelayedLeadService $delayedLeadService
    ) {}

    public function normalizeFilters(Request $request): array
    {
        $range = $request->string('date_range')->toString() ?: 'this_month';
        $from = $request->string('date_from')->toString();
        $to = $request->string('date_to')->toString();

        $now = Carbon::now();
        $startAt = null;
        $endAt = null;
        $prevStartAt = null;
        $prevEndAt = null;

        switch ($range) {
            case 'today':
                $startAt = $now->copy()->startOfDay();
                $endAt = $now->copy()->endOfDay();
                $prevStartAt = $startAt->copy()->subDay();
                $prevEndAt = $endAt->copy()->subDay();
                break;
            case 'yesterday':
                $startAt = $now->copy()->subDay()->startOfDay();
                $endAt = $now->copy()->subDay()->endOfDay();
                $prevStartAt = $startAt->copy()->subDay();
                $prevEndAt = $endAt->copy()->subDay();
                break;
            case 'this_week':
                $startAt = $now->copy()->startOfWeek();
                $endAt = $now->copy()->endOfWeek();
                $prevStartAt = $startAt->copy()->subWeek();
                $prevEndAt = $endAt->copy()->subWeek();
                break;
            case 'last_week':
                $startAt = $now->copy()->subWeek()->startOfWeek();
                $endAt = $now->copy()->subWeek()->endOfWeek();
                $prevStartAt = $startAt->copy()->subWeek();
                $prevEndAt = $endAt->copy()->subWeek();
                break;
            case 'last_month':
                $startAt = $now->copy()->subMonth()->startOfMonth();
                $endAt = $now->copy()->subMonth()->endOfMonth();
                $prevStartAt = $startAt->copy()->subMonth();
                $prevEndAt = $endAt->copy()->subMonth();
                break;
            case 'this_quarter':
                $startAt = $now->copy()->startOfQuarter();
                $endAt = $now->copy()->endOfQuarter();
                $prevStartAt = $startAt->copy()->subQuarter();
                $prevEndAt = $endAt->copy()->subQuarter();
                break;
            case 'this_year':
                $startAt = $now->copy()->startOfYear();
                $endAt = $now->copy()->endOfYear();
                $prevStartAt = $startAt->copy()->subYear();
                $prevEndAt = $endAt->copy()->subYear();
                break;
            case 'custom':
                $startAt = $from ? Carbon::parse($from)->startOfDay() : $now->copy()->startOfMonth();
                $endAt = $to ? Carbon::parse($to)->endOfDay() : $now->copy()->endOfDay();
                $diffDays = max(1, $startAt->diffInDays($endAt));
                $prevStartAt = $startAt->copy()->subDays($diffDays + 1);
                $prevEndAt = $startAt->copy()->subSecond();
                break;
            case 'this_month':
            default:
                $startAt = $now->copy()->startOfMonth();
                $endAt = $now->copy()->endOfMonth();
                $prevStartAt = $startAt->copy()->subMonth();
                $prevEndAt = $endAt->copy()->subMonth();
                break;
        }

        return [
            'date_range' => $range,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'prev_start_at' => $prevStartAt,
            'prev_end_at' => $prevEndAt,
            'sales_rep_id' => $request->filled('sales_rep_id') ? (int) $request->input('sales_rep_id') : null,
            'status_id' => $request->filled('status_id') ? (int) $request->input('status_id') : null,
            'source_id' => $request->filled('source_id') ? (int) $request->input('source_id') : null,
            'campaign' => $request->string('campaign')->toString() ?: null,
            'destination' => $request->string('destination')->toString() ?: null,
            'service_type' => $request->string('service_type')->toString() ?: null,
            'customer_type' => $request->string('customer_type')->toString() ?: null,
        ];
    }

    public function applyBaseFilters(Builder $query, array $filters, ?User $viewer, string $dateColumn = 'inquiries.created_at'): Builder
    {
        // Scope by user permissions
        if ($viewer && method_exists($viewer, 'hasPermission')) {
            if (!$viewer->hasPermission('leads.view_all') && !$viewer->is_admin) {
                $query->where('inquiries.assigned_user_id', $viewer->id);
            }
        }

        if ($filters['sales_rep_id']) {
            $query->where('inquiries.assigned_user_id', $filters['sales_rep_id']);
        }
        if ($filters['status_id']) {
            $query->where('inquiries.crm_status_id', $filters['status_id']);
        }
        if ($filters['source_id']) {
            $query->where('inquiries.crm_source_id', $filters['source_id']);
        }
        if ($filters['campaign']) {
            $query->where(function ($q) use ($filters) {
                $q->where('inquiries.campaign_name', $filters['campaign'])
                  ->orWhere('inquiries.utm_campaign', $filters['campaign']);
            });
        }
        if ($filters['destination']) {
            $query->where(function ($q) use ($filters) {
                $q->where('inquiries.destination', 'like', '%' . $filters['destination'] . '%')
                  ->orWhere('inquiries.tourism_destination', 'like', '%' . $filters['destination'] . '%');
            });
        }
        if ($filters['service_type']) {
            $query->where('inquiries.service_type', 'like', '%' . $filters['service_type'] . '%');
        }

        return $query;
    }

    public function getManagementAlerts(?User $viewer, array $filters): array
    {
        $alerts = [];

        // 1. Leads without Follow-up
        $noFollowupQuery = Inquiry::query()->whereNull('next_follow_up_at');
        $this->applyBaseFilters($noFollowupQuery, $filters, $viewer);
        $noFollowupCount = $noFollowupQuery->count();
        if ($noFollowupCount > 0) {
            $alerts[] = [
                'key' => 'no_followup',
                'title' => 'عملاء بدون مواعيد متابعة مجدولة',
                'count' => $noFollowupCount,
                'type' => 'danger',
                'icon' => '📅',
                'description' => 'عملاء محتملون في السيستم لا يوجد لهم تاريخ Next Follow-up محدد.',
            ];
        }

        // 2. Overdue Follow-ups
        $overdueFollowupQuery = Inquiry::query()->where('next_follow_up_at', '<', now());
        $this->applyBaseFilters($overdueFollowupQuery, $filters, $viewer);
        $overdueFollowupCount = $overdueFollowupQuery->count();
        if ($overdueFollowupCount > 0) {
            $alerts[] = [
                'key' => 'overdue_followup',
                'title' => 'متابعات فائتة ومتأخرة (Overdue)',
                'count' => $overdueFollowupCount,
                'type' => 'warning',
                'icon' => '⏰',
                'description' => 'مواعيد متابعة كان يجب إنجازها ولم يتم تحديثها بعد.',
            ];
        }

        // 3. Unassigned Leads
        $unassignedQuery = Inquiry::query()->whereNull('assigned_user_id');
        $this->applyBaseFilters($unassignedQuery, $filters, $viewer);
        $unassignedCount = $unassignedQuery->count();
        if ($unassignedCount > 0) {
            $alerts[] = [
                'key' => 'unassigned',
                'title' => 'عملاء جدد غير مسندين لموظف مبيعات',
                'count' => $unassignedCount,
                'type' => 'danger',
                'icon' => '👥',
                'description' => 'عملاء مسجلين بالنظام بدون تعيين موظف مسئول عن المتابعة.',
            ];
        }

        // 4. Delayed Leads (48h inactivity)
        $delayedQuery = Inquiry::query();
        $this->applyBaseFilters($delayedQuery, $filters, $viewer);
        $delayedCount = $this->delayedLeadService->applyDelayedScope($delayedQuery)->count();
        if ($delayedCount > 0) {
            $alerts[] = [
                'key' => 'delayed_leads',
                'title' => 'عملاء متأخرون لأكثر من 48 ساعة دون إجراء (Delayed)',
                'count' => $delayedCount,
                'type' => 'warning',
                'icon' => '⚠️',
                'description' => 'عملاء توقفت عليهم الأنشطة والمكالمات لأكثر من 48 ساعة.',
            ];
        }

        // 5. Data Quality Alerts (Leads missing phone or status)
        $dataQualityQuery = Inquiry::query()->where(function ($q) {
            $q->whereNull('phone')->orWhere('phone', '')->orWhereNull('crm_status_id');
        });
        $this->applyBaseFilters($dataQualityQuery, $filters, $viewer);
        $dataQualityCount = $dataQualityQuery->count();
        if ($dataQualityCount > 0) {
            $alerts[] = [
                'key' => 'data_quality',
                'title' => 'عملاء ببيانات ناقصة (بدون رقم هاتف أو حالة)',
                'count' => $dataQualityCount,
                'type' => 'info',
                'icon' => '🔍',
                'description' => 'عملاء يحتاجون لتنظيف وتكامل البيانات الأساسية.',
            ];
        }

        return $alerts;
    }

    public function getExecutiveDashboardData(?User $viewer, array $filters): array
    {
        $start = $filters['start_at'];
        $end = $filters['end_at'];
        $prevStart = $filters['prev_start_at'];
        $prevEnd = $filters['prev_end_at'];

        // Current period query
        $currentQuery = Inquiry::query()->whereBetween('inquiries.created_at', [$start, $end]);
        $this->applyBaseFilters($currentQuery, $filters, $viewer);

        // Previous period query
        $prevQuery = Inquiry::query()->whereBetween('inquiries.created_at', [$prevStart, $prevEnd]);
        $this->applyBaseFilters($prevQuery, $filters, $viewer);

        $totalLeads = (clone $currentQuery)->count();
        $prevTotalLeads = (clone $prevQuery)->count();

        // Contacted leads (have calls/whatsapps/notes)
        $contactedLeads = (clone $currentQuery)->where(function ($q) {
            $q->has('calls')->orHas('whatsappLogs')->orHas('crmNotes')->orWhereNotNull('last_follow_up_at');
        })->count();
        $prevContactedLeads = (clone $prevQuery)->where(function ($q) {
            $q->has('calls')->orHas('whatsappLogs')->orHas('crmNotes')->orWhereNotNull('last_follow_up_at');
        })->count();

        // Qualified leads
        $qualifiedStatusIds = CrmStatus::query()->whereIn('slug', ['qualified', 'hot', 'interested', 'meeting-scheduled'])->pluck('id')->toArray();
        $qualifiedLeads = (clone $currentQuery)->whereIn('crm_status_id', $qualifiedStatusIds)->count();
        $prevQualifiedLeads = (clone $prevQuery)->whereIn('crm_status_id', $qualifiedStatusIds)->count();

        // Quotations
        $quotationStatusIds = CrmStatus::query()->whereIn('slug', ['quotation-sent', 'proposal', 'offer-sent'])->pluck('id')->toArray();
        $quotationsCount = (clone $currentQuery)->whereIn('crm_status_id', $quotationStatusIds)->count();

        // Bookings & Paid
        $bookingStatusIds = CrmStatus::query()->whereIn('slug', ['won', 'booked', 'complete-lead', 'documents-complete'])->pluck('id')->toArray();
        $bookingsCount = (clone $currentQuery)->whereIn('crm_status_id', $bookingStatusIds)->count();
        $prevBookingsCount = (clone $prevQuery)->whereIn('crm_status_id', $bookingStatusIds)->count();

        $revenue = (float) (clone $currentQuery)->sum('total_amount');
        $prevRevenue = (float) (clone $prevQuery)->sum('total_amount');

        $collections = (float) (clone $currentQuery)->sum('paid_amount');
        $prevCollections = (float) (clone $prevQuery)->sum('paid_amount');

        $avgBookingValue = $bookingsCount > 0 ? ($revenue / $bookingsCount) : 0;
        $conversionRate = $totalLeads > 0 ? round(($bookingsCount / $totalLeads) * 100, 1) : 0;

        return [
            'kpis' => [
                'total_leads' => [
                    'label' => 'إجمالي العملاء (Total Leads)',
                    'value' => $totalLeads,
                    'prev_value' => $prevTotalLeads,
                    'change_pct' => $this->calcPercentageChange($totalLeads, $prevTotalLeads),
                    'icon' => '👥',
                ],
                'contacted_leads' => [
                    'label' => 'تم التواصل معهم (Contacted)',
                    'value' => $contactedLeads,
                    'prev_value' => $prevContactedLeads,
                    'change_pct' => $this->calcPercentageChange($contactedLeads, $prevContactedLeads),
                    'icon' => '📞',
                ],
                'qualified_leads' => [
                    'label' => 'عملاء مؤهلون (Qualified)',
                    'value' => $qualifiedLeads,
                    'prev_value' => $prevQualifiedLeads,
                    'change_pct' => $this->calcPercentageChange($qualifiedLeads, $prevQualifiedLeads),
                    'icon' => '⭐',
                ],
                'quotations' => [
                    'label' => 'عروض الأسعار (Quotations)',
                    'value' => $quotationsCount,
                    'icon' => '📄',
                ],
                'bookings' => [
                    'label' => 'إجمالي الحجوزات (Bookings)',
                    'value' => $bookingsCount,
                    'prev_value' => $prevBookingsCount,
                    'change_pct' => $this->calcPercentageChange($bookingsCount, $prevBookingsCount),
                    'icon' => '🎯',
                ],
                'revenue' => [
                    'label' => 'المبيعات الكلية (Revenue)',
                    'value' => number_format($revenue, 2) . ' ج.م',
                    'prev_value' => number_format($prevRevenue, 2),
                    'change_pct' => $this->calcPercentageChange($revenue, $prevRevenue),
                    'icon' => '💰',
                ],
                'collections' => [
                    'label' => 'المحصّلات الفعلية (Collections)',
                    'value' => number_format($collections, 2) . ' ج.م',
                    'prev_value' => number_format($prevCollections, 2),
                    'change_pct' => $this->calcPercentageChange($collections, $prevCollections),
                    'icon' => '💵',
                ],
                'avg_booking_value' => [
                    'label' => 'متوسط قيمة الحجز (Avg Value)',
                    'value' => number_format($avgBookingValue, 2) . ' ج.م',
                    'icon' => '📊',
                ],
                'conversion_rate' => [
                    'label' => 'معدل التحويل (Conversion Rate)',
                    'value' => $conversionRate . '%',
                    'icon' => '📈',
                ],
            ],
            'alerts' => $this->getManagementAlerts($viewer, $filters),
        ];
    }

    public function getLeadFunnelReport(?User $viewer, array $filters): array
    {
        $baseQuery = Inquiry::query()->whereBetween('inquiries.created_at', [$filters['start_at'], $filters['end_at']]);
        $this->applyBaseFilters($baseQuery, $filters, $viewer);

        $total = (clone $baseQuery)->count();

        $contacted = (clone $baseQuery)->where(function ($q) {
            $q->has('calls')->orHas('whatsappLogs')->orHas('crmNotes')->orWhereNotNull('last_follow_up_at');
        })->count();

        $qualifiedStatusIds = CrmStatus::query()->whereIn('slug', ['qualified', 'hot', 'interested'])->pluck('id')->toArray();
        $qualified = (clone $baseQuery)->whereIn('crm_status_id', $qualifiedStatusIds)->count();

        $quotationStatusIds = CrmStatus::query()->whereIn('slug', ['quotation-sent', 'proposal', 'offer-sent'])->pluck('id')->toArray();
        $quotations = (clone $baseQuery)->whereIn('crm_status_id', $quotationStatusIds)->count();

        $bookingStatusIds = CrmStatus::query()->whereIn('slug', ['won', 'booked', 'complete-lead', 'documents-complete'])->pluck('id')->toArray();
        $bookings = (clone $baseQuery)->whereIn('crm_status_id', $bookingStatusIds)->count();

        $paid = (clone $baseQuery)->whereIn('crm_status_id', $bookingStatusIds)->where('paid_amount', '>', 0)->count();

        $stages = [
            ['name' => 'إجمالي الليدز الواردة (Total Leads)', 'count' => $total, 'pct' => 100],
            ['name' => 'تم التواصل معهم (Contacted)', 'count' => $contacted, 'pct' => $total > 0 ? round(($contacted / $total) * 100, 1) : 0],
            ['name' => 'عملاء مؤهلون (Qualified)', 'count' => $qualified, 'pct' => $total > 0 ? round(($qualified / $total) * 100, 1) : 0],
            ['name' => 'تم إرسال عرض سعر (Quotations)', 'count' => $quotations, 'pct' => $total > 0 ? round(($quotations / $total) * 100, 1) : 0],
            ['name' => 'تم الحجز (Bookings)', 'count' => $bookings, 'pct' => $total > 0 ? round(($bookings / $total) * 100, 1) : 0],
            ['name' => 'تم الدفع والتحصيل (Paid Bookings)', 'count' => $paid, 'pct' => $total > 0 ? round(($paid / $total) * 100, 1) : 0],
        ];

        return [
            'total' => $total,
            'stages' => $stages,
        ];
    }

    public function getSalesPerformanceReport(?User $viewer, array $filters): Collection
    {
        $usersQuery = User::query()->where('is_active', true);
        if ($viewer && method_exists($viewer, 'hasPermission') && !$viewer->hasPermission('leads.view_all') && !$viewer->is_admin) {
            $usersQuery->where('id', $viewer->id);
        }

        $users = $usersQuery->get();

        return $users->map(function ($user) use ($filters, $viewer) {
            $query = Inquiry::query()->where('assigned_user_id', $user->id)
                ->whereBetween('created_at', [$filters['start_at'], $filters['end_at']]);

            $totalLeads = (clone $query)->count();
            $contacted = (clone $query)->where(function ($q) {
                $q->has('calls')->orHas('whatsappLogs')->orHas('crmNotes')->orWhereNotNull('last_follow_up_at');
            })->count();

            $bookingStatusIds = CrmStatus::query()->whereIn('slug', ['won', 'booked', 'complete-lead', 'documents-complete'])->pluck('id')->toArray();
            $bookings = (clone $query)->whereIn('crm_status_id', $bookingStatusIds)->count();
            $revenue = (float) (clone $query)->sum('total_amount');

            $callsCount = CrmLeadCall::query()->where('user_id', $user->id)->whereBetween('created_at', [$filters['start_at'], $filters['end_at']])->count();
            $whatsappCount = CrmLeadWhatsApp::query()->where('user_id', $user->id)->whereBetween('created_at', [$filters['start_at'], $filters['end_at']])->count();
            $notesCount = CrmLeadNote::query()->where('user_id', $user->id)->whereBetween('created_at', [$filters['start_at'], $filters['end_at']])->count();

            $conversionRate = $totalLeads > 0 ? round(($bookings / $totalLeads) * 100, 1) : 0;

            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'total_leads' => $totalLeads,
                'contacted_leads' => $contacted,
                'bookings' => $bookings,
                'revenue' => $revenue,
                'calls_count' => $callsCount,
                'whatsapp_count' => $whatsappCount,
                'notes_count' => $notesCount,
                'conversion_rate' => $conversionRate,
            ];
        })->sortByDesc('revenue')->values();
    }

    public function getLeadAgingReport(?User $viewer, array $filters): array
    {
        $baseQuery = Inquiry::query();
        $this->applyBaseFilters($baseQuery, $filters, $viewer);

        $now = now();

        $brackets = [
            '0_1_day' => ['label' => 'يوم واحد أو أقل (0–1 Day)', 'query' => (clone $baseQuery)->where('created_at', '>=', $now->copy()->subDay())],
            '2_3_days' => ['label' => 'من 2 إلى 3 أيام (2–3 Days)', 'query' => (clone $baseQuery)->whereBetween('created_at', [$now->copy()->subDays(3), $now->copy()->subDays(2)])],
            '4_7_days' => ['label' => 'من 4 إلى 7 أيام (4–7 Days)', 'query' => (clone $baseQuery)->whereBetween('created_at', [$now->copy()->subDays(7), $now->copy()->subDays(4)])],
            '8_14_days' => ['label' => 'من 8 إلى 14 يوم (8–14 Days)', 'query' => (clone $baseQuery)->whereBetween('created_at', [$now->copy()->subDays(14), $now->copy()->subDays(8)])],
            '15_30_days' => ['label' => 'من 15 إلى 30 يوم (15–30 Days)', 'query' => (clone $baseQuery)->whereBetween('created_at', [$now->copy()->subDays(30), $now->copy()->subDays(15)])],
            'over_30_days' => ['label' => 'أكثر من 30 يوم (+30 Days)', 'query' => (clone $baseQuery)->where('created_at', '<', $now->copy()->subDays(30))],
        ];

        $result = [];
        foreach ($brackets as $key => $data) {
            $result[$key] = [
                'label' => $data['label'],
                'count' => $data['query']->count(),
                'leads' => $data['query']->with(['assignedUser', 'crmStatus'])->latest()->take(10)->get(),
            ];
        }

        return $result;
    }

    public function getLeadSourcePerformanceReport(?User $viewer, array $filters): Collection
    {
        $sources = CrmLeadSource::query()->where('is_active', true)->get();

        return $sources->map(function ($src) use ($filters, $viewer) {
            $query = Inquiry::query()->where('crm_source_id', $src->id)
                ->whereBetween('created_at', [$filters['start_at'], $filters['end_at']]);
            $this->applyBaseFilters($query, $filters, $viewer);

            $totalLeads = (clone $query)->count();
            $bookingStatusIds = CrmStatus::query()->whereIn('slug', ['won', 'booked', 'complete-lead', 'documents-complete'])->pluck('id')->toArray();
            $bookings = (clone $query)->whereIn('crm_status_id', $bookingStatusIds)->count();
            $revenue = (float) (clone $query)->sum('total_amount');
            $conversionRate = $totalLeads > 0 ? round(($bookings / $totalLeads) * 100, 1) : 0;

            return [
                'source_id' => $src->id,
                'source_name' => $src->name_ar ?: $src->name,
                'total_leads' => $totalLeads,
                'bookings' => $bookings,
                'revenue' => $revenue,
                'conversion_rate' => $conversionRate,
            ];
        })->sortByDesc('total_leads')->values();
    }

    public function getDrilldownLeads(Request $request, ?User $viewer): array
    {
        $key = $request->string('metric_key')->toString();
        $filters = $this->normalizeFilters($request);

        $query = Inquiry::query()->with(['assignedUser', 'crmStatus', 'crmSource']);
        $this->applyBaseFilters($query, $filters, $viewer);

        switch ($key) {
            case 'no_followup':
                $query->whereNull('next_follow_up_at');
                break;
            case 'overdue_followup':
                $query->where('next_follow_up_at', '<', now());
                break;
            case 'unassigned':
                $query->whereNull('assigned_user_id');
                break;
            case 'delayed_leads':
                $this->delayedLeadService->applyDelayedScope($query);
                break;
            case 'data_quality':
                $query->where(function ($q) {
                    $q->whereNull('phone')->orWhere('phone', '')->orWhereNull('crm_status_id');
                });
                break;
            case 'total_leads':
                $query->whereBetween('inquiries.created_at', [$filters['start_at'], $filters['end_at']]);
                break;
            case 'bookings':
                $bookingStatusIds = CrmStatus::query()->whereIn('slug', ['won', 'booked', 'complete-lead', 'documents-complete'])->pluck('id')->toArray();
                $query->whereIn('crm_status_id', $bookingStatusIds);
                break;
        }

        $leads = $query->latest('inquiries.updated_at')->take(50)->get();

        return [
            'metric_key' => $key,
            'count' => $leads->count(),
            'leads' => $leads->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'full_name' => $lead->full_name ?: 'بدون اسم',
                    'phone' => $lead->phone ?: '-',
                    'status_name' => $lead->crmStatus?->name_ar ?: 'جديد',
                    'status_color' => $lead->crmStatus?->color ?? '#6c757d',
                    'assigned_user_name' => $lead->assignedUser?->name ?: 'غير مسند',
                    'created_at_formatted' => $lead->created_at ? $lead->created_at->format('Y-m-d h:i A') : '-',
                    'edit_url' => route('admin.crm.leads.show', $lead->id),
                ];
            }),
        ];
    }

    protected function calcPercentageChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
