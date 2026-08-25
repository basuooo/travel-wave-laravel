<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsApp\WhatsAppCampaignProcessorService;

class ProcessWhatsAppCampaignsCommand extends Command
{
    protected $signature = 'whatsapp:process-campaigns';
    protected $description = 'Process running WhatsApp campaigns and send queued messages in background';

    public function handle(WhatsAppCampaignProcessorService $processor): int
    {
        $this->info('Starting WhatsApp campaign queue processing...');
        $count = $processor->processActiveCampaigns();
        $this->info("Processed {$count} WhatsApp campaign messages successfully.");

        return Command::SUCCESS;
    }
}
