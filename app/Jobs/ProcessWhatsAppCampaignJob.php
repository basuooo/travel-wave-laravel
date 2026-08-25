<?php

namespace App\Jobs;

use App\Models\WhatsAppCampaign;
use App\Services\WhatsApp\WhatsAppCampaignProcessorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $campaignId;

    public function __construct(int $campaignId)
    {
        $this->campaignId = $campaignId;
    }

    public function handle(WhatsAppCampaignProcessorService $processor): void
    {
        $campaign = WhatsAppCampaign::find($this->campaignId);

        if (!$campaign || $campaign->status !== 'running') {
            return;
        }

        Log::info("Executing background ProcessWhatsAppCampaignJob for Campaign #{$this->campaignId}");
        $processor->processCampaignBatch($campaign, 20);
    }
}
