<?php

namespace App\Services\Funnels;

use App\Models\CrmLeadSource;
use App\Models\Funnel;
use App\Models\FunnelResponse;
use App\Models\Inquiry;
use App\Support\UtmAttributionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FunnelCrmSyncService
{
    /**
     * Sync completed funnel response to Travel Wave's existing Inquiry CRM.
     */
    public function syncToCrm(FunnelResponse $response): bool
    {
        $funnel = $response->funnel;
        $crmSettings = $funnel->crm_settings ?? [];

        // Check if CRM integration is ON
        $isCrmEnabled = (bool) ($crmSettings['enabled'] ?? true);
        if (! $isCrmEnabled) {
            $response->update([
                'crm_sync_status' => 'disabled',
                'last_sync_attempt' => now(),
            ]);
            return true;
        }

        // Idempotency check: if already synced and inquiry exists, do not duplicate
        if ($response->crm_inquiry_id && Inquiry::where('id', $response->crm_inquiry_id)->exists()) {
            $response->update([
                'crm_sync_status' => 'synced',
                'last_sync_attempt' => now(),
            ]);
            return true;
        }

        try {
            DB::beginTransaction();

            $response->loadMissing(['answers.element', 'result']);
            $answerMap = [];
            foreach ($response->answers as $ans) {
                if ($ans->question_label) {
                    $answerMap[$ans->question_label] = $ans->answer_value;
                }
                if ($ans->element?->question_key) {
                    $answerMap[$ans->element->question_key] = $ans->answer_value;
                }
            }

            // Extract contact fields from mapped or direct answer values
            $fullName = $this->extractField($answerMap, ['full_name', 'name', 'Full Name', 'الاسم', 'الاسم بالكامل']);
            $phone = $this->extractField($answerMap, ['phone', 'mobile', 'Phone', 'رقم الهاتف', 'الجوال', 'واتساب']);
            $email = $this->extractField($answerMap, ['email', 'Email', 'البريد الإلكتروني']);
            $country = $this->extractField($answerMap, ['country', 'nationality', 'Country', 'الدولة', 'الجنسية']);
            $destination = $this->extractField($answerMap, ['destination', 'Destination', 'الوجهة']);

            // Resolve Lead Source
            $leadSourceId = $crmSettings['source_id'] ?? $this->resolveFunnelLeadSourceId();
            $serviceTypeId = $crmSettings['service_type_id'] ?? null;

            // Build submitted_data metadata block
            $submittedData = [
                'funnel_id' => $funnel->id,
                'funnel_name' => $funnel->name,
                'funnel_slug' => $funnel->slug,
                'funnel_score' => $response->score,
                'funnel_result' => $response->result?->title,
                'all_answers' => $answerMap,
                'utm_data' => $response->utm_data,
                'submitted_at' => now()->toDateTimeString(),
            ];

            $utmData = $response->utm_data ?? [];

            // Create main Inquiry record in Travel Wave CRM
            $inquiry = Inquiry::create([
                'full_name' => $fullName ?: 'Funnel Visitor #' . $response->id,
                'phone' => $phone ?: null,
                'whatsapp_number' => $phone ?: null,
                'email' => $email ?: null,
                'country' => $country ?: null,
                'destination' => $destination ?: null,
                'type' => 'interactive_funnel',
                'form_name' => $funnel->name,
                'form_category' => 'Interactive Funnel',
                'submitted_data' => $submittedData,
                'status' => 'new',
                'crm_source_id' => $leadSourceId,
                'crm_service_type_id' => $serviceTypeId,
                'crm_status_updated_at' => now(),
                'status_1_updated_at' => now(),
                'lead_source' => 'Interactive Funnel - ' . $funnel->name,
                'campaign_name' => $utmData['utm_campaign'] ?? $funnel->name,
                'utm_source' => $utmData['utm_source'] ?? null,
                'utm_medium' => $utmData['utm_medium'] ?? null,
                'utm_campaign' => $utmData['utm_campaign'] ?? null,
                'utm_content' => $utmData['utm_content'] ?? null,
                'utm_term' => $utmData['utm_term'] ?? null,
                'priority' => $response->score >= 75 ? 'high' : 'normal',
                'additional_notes' => sprintf(
                    "Funnel: %s\nScore: %d\nResult: %s",
                    $funnel->name,
                    $response->score,
                    $response->result?->title ?: 'N/A'
                ),
            ]);

            // Update Response record
            $response->update([
                'crm_inquiry_id' => $inquiry->id,
                'crm_sync_status' => 'synced',
                'last_sync_attempt' => now(),
                'sync_error' => null,
            ]);

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Funnel CRM Sync Failed', [
                'response_id' => $response->id,
                'funnel_id' => $funnel->id,
                'error' => $e->getMessage(),
            ]);

            $response->update([
                'crm_sync_status' => 'failed',
                'last_sync_attempt' => now(),
                'sync_error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Extract field by checking multiple candidate keys.
     */
    protected function extractField(array $map, array $candidates): ?string
    {
        foreach ($candidates as $cand) {
            foreach ($map as $key => $val) {
                if (strcasecmp((string) $key, (string) $cand) === 0 || str_contains(strtolower((string) $key), strtolower((string) $cand))) {
                    if (filled($val)) {
                        return is_array($val) ? implode(', ', $val) : (string) $val;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Get or create default CRM Lead Source for Interactive Funnels.
     */
    protected function resolveFunnelLeadSourceId(): ?int
    {
        try {
            $source = CrmLeadSource::firstOrCreate(
                ['slug' => 'interactive-funnel'],
                [
                    'name_en' => 'Interactive Funnel',
                    'name_ar' => 'Funnel تفاعلي',
                    'is_active' => true,
                    'sort_order' => 10,
                ]
            );
            return $source->id;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
