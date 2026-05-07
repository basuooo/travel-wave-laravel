<?php

namespace App\Support;

use App\Models\CrmLeadSource;
use App\Models\CrmServiceSubtype;
use App\Models\CrmServiceType;
use App\Models\CrmStatus;
use App\Models\Inquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class CrmLeadTransferService
{
    public const DUPLICATE_MODE_NONE = 'none';
    public const DUPLICATE_MODE_SKIP = 'skip';
    public const DUPLICATE_MODE_MERGE = 'merge_existing';
    public const DUPLICATE_DETECTOR_NAME = 'full_name';
    public const DUPLICATE_DETECTOR_PHONE = 'phone';
    public const DUPLICATE_DETECTOR_WHATSAPP = 'whatsapp_number';

    public function __construct(
        protected SimpleSpreadsheet $spreadsheet
    ) {
    }

    public function duplicateDetectorOptions(): array
    {
        return [
            self::DUPLICATE_DETECTOR_NAME => [
                'label_ar' => 'الاسم',
                'label_en' => 'Full Name',
            ],
            self::DUPLICATE_DETECTOR_PHONE => [
                'label_ar' => 'رقم الهاتف',
                'label_en' => 'Phone Number',
            ],
            self::DUPLICATE_DETECTOR_WHATSAPP => [
                'label_ar' => 'رقم واتساب',
                'label_en' => 'WhatsApp Number',
            ],
        ];
    }

    public function duplicateHandlingOptions(): array
    {
        return [
            self::DUPLICATE_MODE_NONE => [
                'label_ar' => 'لا يوجد / بدون كشف تكرار',
                'label_en' => 'No duplicate detection',
            ],
            self::DUPLICATE_MODE_SKIP => [
                'label_ar' => 'تخطي البيانات المكررة',
                'label_en' => 'Skip duplicate rows',
            ],
            self::DUPLICATE_MODE_MERGE => [
                'label_ar' => 'تحديث بيانات العميل المحتمل الحالي',
                'label_en' => 'Update existing lead',
            ],
        ];
    }

    public function fieldDefinitions(): array
    {
        return [
            'full_name' => ['label_ar' => 'اسم العميل', 'label_en' => 'Customer Name'],
            'phone' => ['label_ar' => 'رقم الموبايل', 'label_en' => 'Mobile Number'],
            'whatsapp_number' => ['label_ar' => 'رقم الواتساب', 'label_en' => 'WhatsApp Number'],
            'email' => ['label_ar' => 'الإيميل', 'label_en' => 'Email'],
            'crm_status' => ['label_ar' => 'الحالة', 'label_en' => 'Status'],
            'crm_service_type' => ['label_ar' => 'النوع', 'label_en' => 'Type'],
            'crm_service_subtype' => ['label_ar' => 'تصنيف النوع', 'label_en' => 'Subtype'],
            'service_country_name' => ['label_ar' => 'الدولة', 'label_en' => 'Country Name'],
            'tourism_destination' => ['label_ar' => 'الوجهة السياحية', 'label_en' => 'Tourism Destination'],
            'travel_destination' => ['label_ar' => 'جهة السفر', 'label_en' => 'Travel Destination'],
            'hotel_destination' => ['label_ar' => 'وجهة الفندق', 'label_en' => 'Hotel Destination'],
            'travelers_count' => ['label_ar' => 'العدد', 'label_en' => 'Number of Persons'],
            'admin_notes' => ['label_ar' => 'ملاحظات', 'label_en' => 'Notes'],
            'additional_notes' => ['label_ar' => 'ملاحظات أخرى', 'label_en' => 'Additional Notes'],
            'total_price' => ['label_ar' => 'السعر الإجمالي', 'label_en' => 'Total Price'],
            'expenses' => ['label_ar' => 'المصروفات', 'label_en' => 'Expenses'],
            'net_price' => ['label_ar' => 'السعر الصافي', 'label_en' => 'Net Price'],
            'crm_source' => ['label_ar' => 'مصدر الليد', 'label_en' => 'Lead Source'],
            'assigned_user' => ['label_ar' => 'البائع / المسؤول', 'label_en' => 'Seller / Assigned User'],
            'created_at' => ['label_ar' => 'تاريخ الإنشاء', 'label_en' => 'Created At'],
        ];
    }

    public function templateHeaders(string $locale = 'ar'): array
    {
        return collect($this->fieldDefinitions())
            ->map(fn (array $field) => $locale === 'ar' ? $field['label_ar'] : $field['label_en'])
            ->values()
            ->all();
    }

    public function sampleRows(): array
    {
        return [[
            'أحمد محمد',
            '01012345678',
            '01012345678',
            'ahmed@example.com',
            'ليد جديد',
            'تأشيرات خارجية',
            'الاتحاد الأوروبي',
            'فرنسا',
            '',
            '',
            '',
            '2',
            'مهتم بالسفر خلال الصيف',
            'يريد متابعة بعد يومين',
            '15000',
            '2000',
            '13000',
            'Facebook (lead Generation)',
            'Admin',
            now()->format('Y-m-d H:i'),
        ]];
    }

    public function exportFieldOptions(): array
    {
        return $this->fieldDefinitions();
    }

    public function previewFromUpload(
        UploadedFile $file,
        string $duplicateMode = self::DUPLICATE_MODE_NONE,
        ?string $duplicateDetector = null
    ): array
    {
        $sheet = $this->spreadsheet->readUploadedFile($file);

        return $this->buildPreview($sheet['headers'] ?? [], $sheet['rows'] ?? [], $duplicateMode, $duplicateDetector);
    }

    public function previewFromGoogleSheet(
        string $url,
        string $duplicateMode = self::DUPLICATE_MODE_NONE,
        ?string $duplicateDetector = null
    ): array
    {
        $csvUrl = $this->normalizeGoogleSheetUrl($url);
        $response = Http::timeout(20)->get($csvUrl);

        if (! $response->ok()) {
            throw new RuntimeException('Unable to fetch the Google Sheet data.');
        }

        $sheet = $this->spreadsheet->readRemoteCsv($response->body());

        return $this->buildPreview($sheet['headers'] ?? [], $sheet['rows'] ?? [], $duplicateMode, $duplicateDetector);
    }

    public function buildPreview(
        array $headers,
        array $rows,
        string $duplicateMode = self::DUPLICATE_MODE_NONE,
        ?string $duplicateDetector = null
    ): array
    {
        $duplicateMode = $this->normalizeDuplicateMode($duplicateMode);
        $duplicateDetector = $duplicateMode === self::DUPLICATE_MODE_NONE
            ? null
            : $this->normalizeDuplicateDetector($duplicateDetector ?? self::DUPLICATE_DETECTOR_PHONE);
        $normalizedHeaders = $this->normalizeHeaders($headers);
        $previewRows = [];
        $seenKeys = [];
        $summary = [
            'total_rows' => count($rows),
            'valid_rows' => 0,
            'importable_rows' => 0,
            'new_rows' => 0,
            'error_rows' => 0,
            'duplicate_rows' => 0,
            'skipped_duplicate_rows' => 0,
            'merged_rows' => 0,
        ];

        foreach ($rows as $index => $row) {
            $mapped = $this->mapRow($normalizedHeaders, $row);
            $errors = $this->validateMappedRow($mapped);
            $duplicate = $duplicateMode === self::DUPLICATE_MODE_NONE
                ? ['is_duplicate' => false]
                : $this->findDuplicateLead($mapped, $duplicateDetector, $seenKeys, $index + 2);
            $hasDuplicate = ($duplicate['is_duplicate'] ?? false) === true;
            $action = $this->determineRowAction($duplicateMode, $errors, $hasDuplicate);

            if ($hasDuplicate) {
                $summary['duplicate_rows']++;
            }

            if ($errors !== []) {
                $summary['error_rows']++;
            } elseif ($action === 'import') {
                $summary['valid_rows']++;
                $summary['importable_rows']++;
                $summary['new_rows']++;
            } elseif ($action === 'merge') {
                $summary['valid_rows']++;
                $summary['importable_rows']++;
                $summary['merged_rows']++;
            } else {
                $summary['valid_rows']++;
                $summary['skipped_duplicate_rows']++;
            }

            $previewRows[] = [
                'row_number' => $index + 2,
                'raw' => $row,
                'mapped' => $mapped,
                'errors' => $errors,
                'duplicate_detector' => $duplicateDetector,
                'duplicate_field' => $duplicate['field'] ?? null,
                'duplicate_value' => $duplicate['display_value'] ?? null,
                'duplicate_reason' => $duplicate['reason'] ?? null,
                'duplicate_source' => $duplicate['source'] ?? null,
                'duplicate_with_row' => $duplicate['row_number'] ?? null,
                'duplicate_id' => ($duplicate['inquiry'] ?? null)?->id,
                'duplicate_name' => ($duplicate['inquiry'] ?? null)?->full_name,
                'action' => $action,
                'will_import' => in_array($action, ['import', 'merge'], true),
            ];
        }

        return [
            'headers' => $headers,
            'normalized_headers' => $normalizedHeaders,
            'rows' => $previewRows,
            'summary' => $summary,
            'duplicate_mode' => $duplicateMode,
            'duplicate_detector' => $duplicateDetector,
        ];
    }

    public function importPreview(array $preview, User $actor): array
    {
        $mergeStatus = $this->ensureMergedStatus();
        $summary = [
            'imported' => 0,
            'merged' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];
        $processedKeys = [];

        foreach ($preview['rows'] ?? [] as $row) {
            if (! empty($row['errors'])) {
                $summary['errors']++;
                continue;
            }

            $action = $row['action'] ?? 'skip';
            if (! in_array($action, ['import', 'merge'], true)) {
                $summary['skipped']++;
                continue;
            }

            $payload = $this->resolveMappedRow($row['mapped'], $actor);
            $duplicateKey = $this->duplicateComparableValue(
                $preview['duplicate_detector'] ?? self::DUPLICATE_DETECTOR_PHONE,
                $row['mapped'][$preview['duplicate_detector'] ?? self::DUPLICATE_DETECTOR_PHONE] ?? null
            );

            if ($action === 'merge') {
                $targetLead = null;

                if (! empty($row['duplicate_id'])) {
                    $targetLead = Inquiry::query()->find($row['duplicate_id']);
                } elseif ($duplicateKey && isset($processedKeys[$duplicateKey])) {
                    $targetLead = $processedKeys[$duplicateKey];
                }

                if (! $targetLead) {
                    $summary['skipped']++;
                    continue;
                }

                $targetLead->update(array_merge($payload, [
                    'crm_status_id' => $mergeStatus->id,
                    'status' => $mergeStatus->slug,
                    'crm_status_updated_by' => $actor->id,
                    'crm_status_updated_at' => now(),
                    'status_1_updated_at' => now(),
                    'status_1_updated_by' => $actor->id,
                ]));
                $summary['merged']++;
                $summary['updated']++;

                if ($duplicateKey) {
                    $processedKeys[$duplicateKey] = $targetLead->fresh();
                }

                continue;
            }

            $created = Inquiry::query()->create($payload);
            $summary['imported']++;

            if ($duplicateKey) {
                $processedKeys[$duplicateKey] = $created;
            }
        }

        return $summary;
    }

    public function buildIssueExport(array $preview, string $issueType, string $locale = 'ar'): array
    {
        $headers = $preview['headers'] ?? [];
        $issueRows = collect($preview['rows'] ?? [])->filter(function (array $row) use ($issueType) {
            return match ($issueType) {
                'duplicates' => ! empty($row['duplicate_reason']),
                'merged' => ($row['action'] ?? null) === 'merge',
                'invalid' => ! empty($row['errors']),
                default => false,
            };
        });

        $metaHeaders = match ($issueType) {
            'duplicates' => [
                $locale === 'ar' ? 'سبب الاستبعاد' : 'Issue',
                $locale === 'ar' ? 'حقل التطابق' : 'Matched Field',
                $locale === 'ar' ? 'قيمة التطابق' : 'Matched Value',
                $locale === 'ar' ? 'نوع التكرار' : 'Duplicate Source',
                $locale === 'ar' ? 'رقم الصف المطابق' : 'Matched Row',
                $locale === 'ar' ? 'الليد الموجود' : 'Existing Lead',
            ],
            'merged' => [
                $locale === 'ar' ? 'إجراء الاستيراد' : 'Import Action',
                $locale === 'ar' ? 'حقل التطابق' : 'Matched Field',
                $locale === 'ar' ? 'قيمة التطابق' : 'Matched Value',
                $locale === 'ar' ? 'الليد الموجود' : 'Existing Lead',
            ],
            'invalid' => [
                $locale === 'ar' ? 'سبب الاستبعاد' : 'Issue',
            ],
            default => [],
        };

        $rows = $issueRows->map(function (array $row) use ($issueType) {
            $baseRow = $row['raw'] ?? [];

            if ($issueType === 'duplicates') {
                $sourceLabel = match ($row['duplicate_source'] ?? null) {
                    'database' => 'database',
                    'file' => 'file',
                    default => '',
                };

                $baseRow[] = $row['duplicate_reason'] ?? '';
                $baseRow[] = $row['duplicate_field'] ?? '';
                $baseRow[] = $row['duplicate_value'] ?? '';
                $baseRow[] = $sourceLabel;
                $baseRow[] = $row['duplicate_with_row'] ?? '';
                $baseRow[] = $row['duplicate_name'] ?? '';

                return $baseRow;
            }

            if ($issueType === 'merged') {
                $baseRow[] = 'merged';
                $baseRow[] = $row['duplicate_field'] ?? '';
                $baseRow[] = $row['duplicate_value'] ?? '';
                $baseRow[] = $row['duplicate_name'] ?? '';

                return $baseRow;
            }

            $baseRow[] = implode(' | ', $row['errors'] ?? []);

            return $baseRow;
        })->values()->all();

        return [
            'headers' => array_merge($headers, $metaHeaders),
            'rows' => $rows,
        ];
    }

    public function buildImportResult(array $preview, array $importSummary): array
    {
        return [
            'summary' => [
                'total_rows' => $preview['summary']['total_rows'] ?? 0,
                'imported_rows' => $importSummary['imported'] ?? 0,
                'duplicate_rows' => $preview['summary']['duplicate_rows'] ?? 0,
                'skipped_duplicate_rows' => $preview['summary']['skipped_duplicate_rows'] ?? 0,
                'merged_rows' => $importSummary['merged'] ?? 0,
                'invalid_rows' => $preview['summary']['error_rows'] ?? 0,
                'skipped_rows' => $importSummary['skipped'] ?? 0,
            ],
            'duplicate_mode' => $preview['duplicate_mode'] ?? self::DUPLICATE_MODE_NONE,
            'duplicate_detector' => $preview['duplicate_detector'] ?? self::DUPLICATE_DETECTOR_PHONE,
            'has_duplicates' => ($preview['summary']['duplicate_rows'] ?? 0) > 0,
            'has_merged_rows' => ($importSummary['merged'] ?? 0) > 0,
            'has_invalid_rows' => ($preview['summary']['error_rows'] ?? 0) > 0,
            'preview' => $preview,
        ];
    }

    public function exportRows(Collection $leads, array $fields, string $locale = 'ar'): array
    {
        $definitions = $this->fieldDefinitions();
        $selectedFields = collect($fields)
            ->filter(fn ($field) => array_key_exists($field, $definitions))
            ->values();

        if ($selectedFields->isEmpty()) {
            $selectedFields = collect(array_keys($definitions));
        }

        $headers = $selectedFields
            ->map(fn ($field) => $locale === 'ar' ? $definitions[$field]['label_ar'] : $definitions[$field]['label_en'])
            ->all();

        $rows = $leads->map(function (Inquiry $lead) use ($selectedFields, $locale) {
            return $selectedFields->map(function (string $field) use ($lead, $locale) {
                return match ($field) {
                    'crm_status' => $lead->crmStatus?->localizedName($locale) ?: $lead->localizedStatus(),
                    'crm_service_type' => $lead->crmServiceType?->localizedName($locale) ?: $lead->localizedServiceType(),
                    'crm_service_subtype' => $lead->crmServiceSubtype?->localizedName($locale) ?: '',
                    'crm_source' => $lead->crmSource?->localizedName($locale) ?: $lead->lead_source,
                    'assigned_user' => $lead->assignedUser?->name,
                    default => $lead->{$field},
                };
            })->all();
        })->all();

        return compact('headers', 'rows');
    }

    protected function normalizeHeaders(array $headers): array
    {
        return collect($headers)->map(function ($header) {
            $normalized = Str::of((string) $header)
                ->trim()
                ->lower()
                ->replace(['_', '-', '.', '/', '\\'], ' ')
                ->squish()
                ->value();

            return match ($normalized) {
                'اسم العميل', 'الاسم', 'الاسم بالكامل', 'customer name', 'full name', 'name' => 'full_name',
                'رقم الموبايل', 'الموبايل', 'الجوال', 'الهاتف', 'phone', 'mobile', 'mobile number' => 'phone',
                'رقم الواتساب', 'واتساب', 'whatsapp', 'whatsapp number' => 'whatsapp_number',
                'الايميل', 'الإيميل', 'البريد الالكتروني', 'email' => 'email',
                'الحالة', 'حالة الليد', 'status', 'lead status' => 'crm_status',
                'النوع', 'type', 'service type' => 'crm_service_type',
                'تصنيف النوع', 'القسم', 'subtype', 'service subtype', 'visa region', 'visa category' => 'crm_service_subtype',
                'الدولة', 'country', 'country name' => 'service_country_name',
                'الوجهة السياحية', 'tourism destination' => 'tourism_destination',
                'جهة السفر', 'travel destination', 'destination' => 'travel_destination',
                'وجهة الفندق', 'hotel destination', 'hotel city', 'city country' => 'hotel_destination',
                'العدد', 'عدد الافراد', 'number of persons', 'travellers', 'travelers count' => 'travelers_count',
                'ملاحظات', 'ملاحظات الادارة', 'notes', 'comment' => 'admin_notes',
                'ملاحظات أخرى', 'additional notes' => 'additional_notes',
                'السعر الإجمالي', 'total price' => 'total_price',
                'المصروفات', 'expenses' => 'expenses',
                'السعر الصافي', 'net price' => 'net_price',
                'مصدر الليد', 'المصدر', 'lead source', 'source' => 'crm_source',
                'البائع المسؤول', 'اسم البائع', 'البائع', 'المسؤول', 'assigned user', 'seller', 'assigned seller', 'seller name', 'seller assigned user' => 'assigned_user',
                'تاريخ الإنشاء', 'created at', 'created date' => 'created_at',
                default => $normalized,
            };
        })->all();
    }

    protected function mapRow(array $headers, array $row): array
    {
        $mapped = [];

        foreach ($headers as $index => $header) {
            if (! $header) {
                continue;
            }

            $mapped[$header] = trim((string) ($row[$index] ?? ''));
        }

        return $mapped;
    }

    protected function validateMappedRow(array $mapped): array
    {
        $errors = [];

        if (blank($mapped['full_name'] ?? null)) {
            $errors[] = 'Customer name is required.';
        }

        if (blank($mapped['phone'] ?? null) && blank($mapped['whatsapp_number'] ?? null) && blank($mapped['email'] ?? null)) {
            $errors[] = 'At least one contact field is required.';
        }

        return $errors;
    }

    protected function findDuplicateLead(array $mapped, string $detector, array &$seenKeys, int $rowNumber): array
    {
        $field = $this->normalizeDuplicateDetector($detector);
        $value = $this->duplicateComparableValue($field, $mapped[$field] ?? null);
        $displayValue = trim((string) ($mapped[$field] ?? ''));

        if (blank($value)) {
            return [
                'is_duplicate' => false,
                'field' => $field,
                'display_value' => $displayValue,
            ];
        }

        if (isset($seenKeys[$value])) {
            return [
                'is_duplicate' => true,
                'field' => $field,
                'display_value' => $displayValue,
                'source' => 'file',
                'reason' => 'Duplicate row found in the imported file.',
                'row_number' => $seenKeys[$value],
            ];
        }

        $duplicate = Inquiry::query()
            ->whereNotNull($field)
            ->get()
            ->first(function (Inquiry $lead) use ($field, $value) {
                return $this->duplicateComparableValue($field, $lead->{$field}) === $value;
            });

        $seenKeys[$value] = $rowNumber;

        if (! $duplicate) {
            return [
                'is_duplicate' => false,
                'field' => $field,
                'display_value' => $displayValue,
            ];
        }

        return [
            'is_duplicate' => true,
            'field' => $field,
            'display_value' => $displayValue,
            'source' => 'database',
            'reason' => 'Duplicate row matched an existing CRM lead.',
            'inquiry' => $duplicate,
        ];
    }

    protected function normalizeDuplicateDetector(string $detector): string
    {
        return in_array($detector, [
            self::DUPLICATE_DETECTOR_NAME,
            self::DUPLICATE_DETECTOR_PHONE,
            self::DUPLICATE_DETECTOR_WHATSAPP,
        ], true) ? $detector : self::DUPLICATE_DETECTOR_PHONE;
    }

    protected function normalizeDuplicateMode(string $mode): string
    {
        return in_array($mode, [
            self::DUPLICATE_MODE_NONE,
            self::DUPLICATE_MODE_SKIP,
            self::DUPLICATE_MODE_MERGE,
        ], true) ? $mode : self::DUPLICATE_MODE_NONE;
    }

    protected function determineRowAction(string $duplicateMode, array $errors, bool $hasDuplicate): string
    {
        if ($errors !== []) {
            return 'invalid';
        }

        if (! $hasDuplicate || $duplicateMode === self::DUPLICATE_MODE_NONE) {
            return 'import';
        }

        return $duplicateMode === self::DUPLICATE_MODE_MERGE ? 'merge' : 'skip';
    }

    protected function ensureMergedStatus(): CrmStatus
    {
        return CrmStatus::query()->firstOrCreate(
            ['slug' => 'merged'],
            [
                'name_ar' => 'دمج',
                'name_en' => 'Merged',
                'status_group' => 'primary',
                'is_active' => true,
                'sort_order' => 999,
            ]
        );
    }

    protected function duplicateComparableValue(string $field, mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (in_array($field, ['phone', 'whatsapp_number'], true)) {
            $digits = preg_replace('/\D+/', '', $value) ?: '';

            return $digits !== '' ? $digits : null;
        }

        return Str::lower(Str::squish($value));
    }

    protected function resolveMappedRow(array $mapped, User $actor): array
    {
        $status = $this->resolveStatus($mapped['crm_status'] ?? null);
        $source = $this->resolveSource($mapped['crm_source'] ?? null);
        $serviceType = $this->resolveServiceType($mapped['crm_service_type'] ?? null);
        $serviceSubtype = $serviceType ? $this->resolveServiceSubtype($serviceType, $mapped['crm_service_subtype'] ?? null) : null;
        $assignedUser = $this->resolveAssignedUser($mapped['assigned_user'] ?? null);
        $createdAt = ! blank($mapped['created_at'] ?? null) ? Carbon::parse($mapped['created_at']) : now();

        $payload = [
            'type' => 'general',
            'full_name' => $mapped['full_name'] ?? null,
            'phone' => $mapped['phone'] ?? null,
            'whatsapp_number' => $mapped['whatsapp_number'] ?? null,
            'email' => $mapped['email'] ?? null,
            'crm_status_id' => $status?->id,
            'status' => $status?->slug ?: 'new',
            'crm_source_id' => $source?->id,
            'lead_source' => $source?->name_en ?: ($mapped['crm_source'] ?? null),
            'crm_service_type_id' => $serviceType?->id,
            'crm_service_subtype_id' => $serviceSubtype?->id,
            'service_country_name' => $mapped['service_country_name'] ?? null,
            'tourism_destination' => $mapped['tourism_destination'] ?? null,
            'travel_destination' => $mapped['travel_destination'] ?? null,
            'hotel_destination' => $mapped['hotel_destination'] ?? null,
            'travelers_count' => $this->nullableInt($mapped['travelers_count'] ?? null),
            'admin_notes' => $mapped['admin_notes'] ?? null,
            'additional_notes' => $mapped['additional_notes'] ?? null,
            'total_price' => $this->nullableFloat($mapped['total_price'] ?? null),
            'expenses' => $this->nullableFloat($mapped['expenses'] ?? null),
            'net_price' => $this->nullableFloat($mapped['net_price'] ?? null),
            'assigned_user_id' => $assignedUser?->id,
            'created_at' => $createdAt,
            'updated_at' => now(),
        ];

        if ($serviceType) {
            $payload['service_type'] = $serviceType->localizedName('ar');
        }

        if ($serviceSubtype) {
            $payload['service_type'] = $serviceSubtype->localizedName('ar');
        }

        $payload['destination'] = $payload['service_country_name']
            ?: $payload['tourism_destination']
            ?: $payload['travel_destination']
            ?: $payload['hotel_destination']
            ?: null;

        $payload['country'] = $payload['service_country_name'] ?: null;
        $payload['crm_status_updated_by'] = $actor->id;
        $payload['crm_status_updated_at'] = now();

        if ($payload['net_price'] === null && $payload['total_price'] !== null && $payload['expenses'] !== null) {
            $payload['net_price'] = $payload['total_price'] - $payload['expenses'];
        }

        return $payload;
    }

    protected function resolveStatus(?string $value): ?CrmStatus
    {
        if (blank($value)) {
            return CrmStatus::query()->where('slug', 'new-lead')->first();
        }

        return CrmStatus::query()
            ->where(function ($query) use ($value) {
                $query->where('slug', Str::slug($value))
                    ->orWhere('name_en', $value)
                    ->orWhere('name_ar', $value);
            })
            ->first();
    }

    protected function resolveSource(?string $value): ?CrmLeadSource
    {
        if (blank($value)) {
            return null;
        }

        return CrmLeadSource::query()
            ->where(function ($query) use ($value) {
                $query->where('slug', Str::slug($value))
                    ->orWhere('name_en', $value)
                    ->orWhere('name_ar', $value);
            })
            ->first();
    }

    protected function resolveServiceType(?string $value): ?CrmServiceType
    {
        if (blank($value)) {
            return null;
        }

        return CrmServiceType::query()
            ->where(function ($query) use ($value) {
                $query->where('slug', Str::slug($value))
                    ->orWhere('name_en', $value)
                    ->orWhere('name_ar', $value);
            })
            ->first();
    }

    protected function resolveServiceSubtype(CrmServiceType $serviceType, ?string $value): ?CrmServiceSubtype
    {
        if (blank($value)) {
            return null;
        }

        return CrmServiceSubtype::query()
            ->where('crm_service_type_id', $serviceType->id)
            ->where(function ($query) use ($value) {
                $query->where('slug', Str::slug($value))
                    ->orWhere('name_en', $value)
                    ->orWhere('name_ar', $value);
            })
            ->first();
    }

    protected function resolveAssignedUser(?string $value): ?User
    {
        if (blank($value)) {
            return null;
        }

        $value = trim($value);

        // Try exact name or email first
        $user = User::query()
            ->where(function ($query) use ($value) {
                $query->where('name', $value)
                    ->orWhere('email', $value)
                    ->orWhere('phone', $value);
            })
            ->first();

        if ($user) {
            return $user;
        }

        // Try normalized Arabic name search
        $normalizedValue = $this->normalizeArabic($value);
        if ($normalizedValue !== '') {
            $users = User::query()->where('is_active', true)->get();
            foreach ($users as $u) {
                if ($this->normalizeArabic($u->name) === $normalizedValue) {
                    return $u;
                }
            }
        }

        return null;
    }

    protected function normalizeArabic(string $value): string
    {
        return Str::of($value)
            ->trim()
            ->replace(['أ', 'إ', 'آ'], 'ا')
            ->replace(['ة'], 'ه')
            ->replace(['ى'], 'ي')
            ->replace(['ؤ', 'ئ'], 'ء')
            ->replace([' ', '_', '-'], '')
            ->value();
    }

    protected function normalizeGoogleSheetUrl(string $url): string
    {
        if (! str_contains($url, 'docs.google.com/spreadsheets')) {
            return $url;
        }

        if (preg_match('#/d/([^/]+)/#', $url, $matches) !== 1) {
            return $url;
        }

        $sheetId = $matches[1];
        $gid = '0';

        if (preg_match('/gid=([0-9]+)/', $url, $gidMatches) === 1) {
            $gid = $gidMatches[1];
        }

        return "https://docs.google.com/spreadsheets/d/{$sheetId}/export?format=csv&gid={$gid}";
    }

    protected function nullableInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    protected function nullableFloat(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }
}
