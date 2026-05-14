<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Services\WhatsApp\WhatsAppBotProcessor;
use App\Services\WhatsApp\WhatsAppService;
use App\Support\ChatbotKnowledgeManager;
use App\Support\SiteChatbotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        protected array $messageData,
        protected array $contactData
    ) {}

    public function handle(): void
    {
        try {
            $whatsappConfig = \App\Models\WhatsappConfig::get();
            $aiConfig       = \App\Models\AiBotConfig::getDefault();

            if (! $whatsappConfig->enabled) {
                return;
            }

            $whatsAppService  = new WhatsAppService($whatsappConfig);
            $knowledgeManager = app(ChatbotKnowledgeManager::class);
            $chatbotService   = new SiteChatbotService($knowledgeManager);
            $processor        = new WhatsAppBotProcessor($whatsappConfig, $aiConfig, $whatsAppService, $chatbotService);

            $processor->process($this->messageData, $this->contactData);
        } catch (\Throwable $e) {
            Log::error('ProcessWhatsAppMessage job failed', [
                'error'        => $e->getMessage(),
                'messageData'  => $this->messageData,
            ]);

            throw $e;
        }
    }
}
