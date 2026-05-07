<?php

namespace App\Jobs;

use App\Integrations\PlatformManager;
use App\Models\CrmWebhookLog;
use App\Services\LeadService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected CrmWebhookLog $webhookLog
    ) {}

    /**
     * Execute the job.
     */
    public function handle(LeadService $leadService)
    {
        try {
            $this->webhookLog->update(['status' => 'processing']);

            $integration = $this->webhookLog->integration;
            if (!$integration) {
                throw new Exception("Integration not found for webhook log #{$this->webhookLog->id}");
            }

            $platform = PlatformManager::make($integration);
            
            // Normalize the lead
            $normalizedData = $platform->normalizeLead($this->webhookLog->payload);
            
            // Process the lead (deduplicate, save, assign)
            $leadService->processLead($normalizedData, $integration);

            $this->webhookLog->update(['status' => 'processed']);
        } catch (Exception $e) {
            Log::error("Error processing webhook #{$this->webhookLog->id}: " . $e->getMessage());
            
            $this->webhookLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            // Track in failed webhooks table for manual retry
            $this->webhookLog->failure()->create([
                'error' => $e->getMessage(),
                'retry_count' => 0
            ]);

            throw $e;
        }
    }
}
