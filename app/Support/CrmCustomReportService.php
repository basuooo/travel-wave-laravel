<?php

namespace App\Support;

use App\Models\AccountingCustomerAccount;
use App\Models\CrmCustomReportTemplate;
use App\Models\CrmCustomer;
use App\Models\CrmFollowUp;
use App\Models\CrmLeadSource;
use App\Models\CrmStatus;
use App\Models\Inquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CrmCustomReportService
{
    public function getEntityRegistry(): array
    {
        return [
            'inquiries' => [
                'label_ar' => '👥 العملاء المحتملون والمبيعات (Leads & Inquiries)',
                'model' => Inquiry::class,
                'columns' => [
                    'id' => ['label_ar' => '# المعرف ID', 'type' => 'number', 'field' => 'id'],
                    'full_name' => ['label_ar' => 'اسم العميل', 'type' => 'text', 'field' => 'full_name'],
                    'phone' => ['label_ar' => 'رقم الهاتف', 'type' => 'text', 'field' => 'phone'],
                    'whatsapp_number' => ['label_ar' => 'رقم الواتساب', 'type' => 'text', 'field' => 'whatsapp_number'],
                    'email' => ['label_ar' => 'البريد الإلكتروني', 'type' => 'text', 'field' => 'email'],
                    'country' => ['label_ar' => 'الدولة', 'type' => 'text', 'field' => 'country'],
                    'nationality' => ['label_ar' => 'الجنسية', 'type' => 'text', 'field' => 'nationality'],
                    'crm_status_id' => ['label_ar' => 'حالة الليد الحالية', 'type' => 'relation', 'relation' => 'crmStatus', 'display_key' => 'name_ar', 'field' => 'crm_status_id'],
                    'assigned_user_id' => ['label_ar' => 'الموظف المسئول', 'type' => 'relation', 'relation' => 'assignedUser', 'display_key' => 'name', 'field' => 'assigned_user_id'],
                    'crm_source_id' => ['label_ar' => 'المصدر الإعلاني', 'type' => 'relation', 'relation' => 'crmSource', 'display_key' => 'name_ar', 'field' => 'crm_source_id'],
                    'campaign_name' => ['label_ar' => 'اسم الحملة الإعلانية', 'type' => 'text', 'field' => 'campaign_name'],
                    'destination' => ['label_ar' => 'الوجهة السياحية', 'type' => 'text', 'field' => 'destination'],
                    'service_type' => ['label_ar' => 'نوع الخدمة', 'type' => 'text', 'field' => 'service_type'],
                    'travelers_count' => ['label_ar' => 'عدد المسافرين', 'type' => 'number', 'field' => 'travelers_count'],
                    'created_at' => ['label_ar' => 'تاريخ التسجيل بالنظام', 'type' => 'date', 'field' => 'created_at'],
                    'next_follow_up_at' => ['label_ar' => 'تاريخ المتابعة القادمة', 'type' => 'date', 'field' => 'next_follow_up_at'],
                    'last_follow_up_at' => ['label_ar' => 'تاريخ آخر تواصل', 'type' => 'date', 'field' => 'last_follow_up_at'],
                    'total_amount' => ['label_ar' => 'السعر الإجمالي (EGP)', 'type' => 'money', 'field' => 'total_amount'],
                    'paid_amount' => ['label_ar' => 'المبلغ المدفوع (EGP)', 'type' => 'money', 'field' => 'paid_amount'],
                    'remaining_amount' => ['label_ar' => 'المبلغ المتبقي (EGP)', 'type' => 'money', 'field' => 'remaining_amount'],
                    'payment_status' => ['label_ar' => 'حالة الدفع المالية', 'type' => 'text', 'field' => 'payment_status'],
                ],
            ],
            'crm_customers' => [
                'label_ar' => '🏅 عملاء CRM المؤكدون (CRM Customers)',
                'model' => CrmCustomer::class,
                'columns' => [
                    'id' => ['label_ar' => '# المعرف ID', 'type' => 'number', 'field' => 'id'],
                    'name' => ['label_ar' => 'اسم العميل', 'type' => 'text', 'field' => 'name'],
                    'phone' => ['label_ar' => 'رقم الهاتف', 'type' => 'text', 'field' => 'phone'],
                    'email' => ['label_ar' => 'البريد', 'type' => 'text', 'field' => 'email'],
                    'city' => ['label_ar' => 'المدينة / المحافظة', 'type' => 'text', 'field' => 'city'],
                    'user_id' => ['label_ar' => 'الموظف المسئول', 'type' => 'relation', 'relation' => 'assignedUser', 'display_key' => 'name', 'field' => 'user_id'],
                    'created_at' => ['label_ar' => 'تاريخ الإضافة', 'type' => 'date', 'field' => 'created_at'],
                ],
            ],
        ];
    }

    public function getFilterOperators(): array
    {
        return [
            'equals' => 'يساوي (=)',
            'not_equals' => 'لا يساوي (!=)',
            'contains' => 'يحتوي على',
            'greater_than' => 'أكبر من (>)',
            'less_than' => 'أقل من (<)',
            'date_today' => 'تاريخ اليوم',
            'date_this_month' => 'خلال هذا الشهر',
            'is_null' => 'فارغ (بدون قيمة)',
            'is_not_null' => 'غير فارغ (يحتوي قيمة)',
        ];
    }

    public function buildReportData(
        string $entityType,
        array $selectedColumnKeys,
        array $filterConditions,
        ?string $groupByKey,
        ?User $viewer
    ): array {
        CrmCustomReportTemplate::ensureTableExists();
        $registry = $this->getEntityRegistry();

        if (!isset($registry[$entityType])) {
            $entityType = 'inquiries';
        }

        $entityMeta = $registry[$entityType];
        $allColumns = $entityMeta['columns'];

        // Fallback default columns if empty
        if (empty($selectedColumnKeys)) {
            $selectedColumnKeys = array_slice(array_keys($allColumns), 0, 6);
        }

        $modelClass = $entityMeta['model'];
        /** @var Builder $query */
        $query = $modelClass::query();

        // Apply Scope by user permission if Inquiry
        if ($entityType === 'inquiries' && $viewer && method_exists($viewer, 'hasPermission')) {
            if (!$viewer->hasPermission('leads.view_all') && !$viewer->is_admin) {
                $query->where('assigned_user_id', $viewer->id);
            }
        }

        // Apply dynamic filters
        foreach ($filterConditions as $cond) {
            $field = $cond['field'] ?? null;
            $operator = $cond['operator'] ?? 'equals';
            $val = $cond['value'] ?? null;

            if (!$field || !isset($allColumns[$field])) {
                continue;
            }

            $columnDbField = $allColumns[$field]['field'];

            switch ($operator) {
                case 'equals':
                    if ($val !== null && $val !== '') {
                        $query->where($columnDbField, $val);
                    }
                    break;
                case 'not_equals':
                    if ($val !== null && $val !== '') {
                        $query->where($columnDbField, '!=', $val);
                    }
                    break;
                case 'contains':
                    if ($val !== null && $val !== '') {
                        $query->where($columnDbField, 'like', '%' . $val . '%');
                    }
                    break;
                case 'greater_than':
                    if ($val !== null && $val !== '') {
                        $query->where($columnDbField, '>', $val);
                    }
                    break;
                case 'less_than':
                    if ($val !== null && $val !== '') {
                        $query->where($columnDbField, '<', $val);
                    }
                    break;
                case 'date_today':
                    $query->whereDate($columnDbField, Carbon::today());
                    break;
                case 'date_this_month':
                    $query->whereBetween($columnDbField, [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                case 'is_null':
                    $query->whereNull($columnDbField);
                    break;
                case 'is_not_null':
                    $query->whereNotNull($columnDbField);
                    break;
            }
        }

        // Include necessary relationship eager loads
        $withRelations = [];
        foreach ($selectedColumnKeys as $colKey) {
            if (isset($allColumns[$colKey]) && $allColumns[$colKey]['type'] === 'relation') {
                $withRelations[] = $allColumns[$colKey]['relation'];
            }
        }
        if (!empty($withRelations)) {
            $query->with(array_unique($withRelations));
        }

        $records = $query->latest('id')->take(200)->get();

        // Format result rows according to selected columns
        $headers = [];
        foreach ($selectedColumnKeys as $colKey) {
            if (isset($allColumns[$colKey])) {
                $headers[$colKey] = $allColumns[$colKey]['label_ar'];
            }
        }

        $rows = [];
        foreach ($records as $record) {
            $row = [];
            foreach ($selectedColumnKeys as $colKey) {
                if (!isset($allColumns[$colKey])) {
                    continue;
                }

                $meta = $allColumns[$colKey];
                $val = '-';

                if ($meta['type'] === 'relation') {
                    $relObj = $record->{$meta['relation']} ?? null;
                    $val = $relObj ? ($relObj->{$meta['display_key']} ?? '-') : '-';
                } elseif ($meta['type'] === 'date') {
                    $dt = $record->{$meta['field']} ?? null;
                    $val = $dt ? (is_string($dt) ? Carbon::parse($dt)->format('Y-m-d h:i A') : $dt->format('Y-m-d h:i A')) : '-';
                } elseif ($meta['type'] === 'money') {
                    $amt = $record->{$meta['field']} ?? 0;
                    $val = number_format((float) $amt, 2) . ' EGP';
                } else {
                    $val = $record->{$meta['field']} ?? '-';
                }

                $row[$colKey] = $val;
            }
            $rows[] = $row;
        }

        // Calculate Grouping if GroupBy is active
        $groupedData = [];
        if ($groupByKey && isset($allColumns[$groupByKey])) {
            $grpMeta = $allColumns[$groupByKey];
            $groupedData = $records->groupBy(function ($rec) use ($grpMeta) {
                if ($grpMeta['type'] === 'relation') {
                    $relObj = $rec->{$grpMeta['relation']} ?? null;
                    return $relObj ? ($relObj->{$grpMeta['display_key']} ?? 'غير محدد') : 'غير محدد';
                }
                return $rec->{$grpMeta['field']} ?: 'غير محدد';
            })->map(function ($groupRecords, $grpName) {
                return [
                    'group_name' => $grpName,
                    'count' => $groupRecords->count(),
                    'sum_total_amount' => (float) $groupRecords->sum('total_amount'),
                    'sum_paid_amount' => (float) $groupRecords->sum('paid_amount'),
                ];
            })->values();
        }

        return [
            'entity_type' => $entityType,
            'headers' => $headers,
            'rows' => $rows,
            'total_count' => count($rows),
            'grouped_data' => $groupedData,
            'selected_columns' => $selectedColumnKeys,
        ];
    }
}
