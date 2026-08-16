<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchZapierWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        protected string $targetUrl,
        protected array $payload
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'TravelWave-ZapierWebhook/1.0',
                ])
                ->post($this->targetUrl, $this->payload);

            if ($response->failed()) {
                Log::warning("Zapier Webhook delivery returned status {$response->status()} for URL {$this->targetUrl}");
            }
        } catch (Exception $e) {
            Log::error("Failed to dispatch Zapier webhook to {$this->targetUrl}: " . $e->getMessage());
            throw $e;
        }
    }
}
