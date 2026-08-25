<?php

namespace App\Services\WhatsApp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class WhatsAppGatewayService
{
    protected Client $http;
    protected string $gatewayUrl;

    public function __construct(?string $gatewayUrl = null)
    {
        $this->gatewayUrl = rtrim($gatewayUrl ?: config('services.whatsapp_gateway.url', 'http://127.0.0.1:3001'), '/');
        $this->http = new Client([
            'base_uri' => $this->gatewayUrl,
            'timeout'  => 10,
        ]);
    }

    public function getQrCode(int $accountId): array
    {
        try {
            $response = $this->http->get("/qr/{$accountId}");
            return json_decode((string) $response->getBody(), true) ?: ['status' => 'error'];
        } catch (GuzzleException $e) {
            return [
                'status'  => 'offline',
                'message' => 'خادم الواتساب (Gateway) غير متاح حالياً. يرجى التأكد من تشغيل خادم Gateway على البورت 3001.'
            ];
        }
    }

    public function getStatus(int $accountId): string
    {
        try {
            $response = $this->http->get("/status/{$accountId}");
            $data = json_decode((string) $response->getBody(), true);
            return $data['status'] ?? 'disconnected';
        } catch (GuzzleException $e) {
            return 'offline';
        }
    }

    public function sendMessage(int $accountId, string $phone, string $message): ?array
    {
        try {
            $response = $this->http->post("/send", [
                'json' => [
                    'accountId' => $accountId,
                    'phone'     => $phone,
                    'message'   => $message,
                ],
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (GuzzleException $e) {
            Log::error("WhatsAppGatewayService error on account #{$accountId}: " . $e->getMessage());
            return null;
        }
    }
}
