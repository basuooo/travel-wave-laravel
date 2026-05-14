<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected Client $http;
    protected WhatsappConfig $config;
    protected string $phoneNumberId;
    protected string $token;
    protected string $apiVersion = 'v19.0';

    public function __construct(?WhatsappConfig $config = null)
    {
        $this->config        = $config ?? WhatsappConfig::get();
        $this->phoneNumberId = (string) ($this->config->phone_number_id ?? '');
        $this->token         = (string) ($this->config->access_token ?? '');
        $this->http          = new Client([
            'base_uri' => "https://graph.facebook.com/{$this->apiVersion}/",
            'timeout'  => 20,
        ]);
    }

    public function isConfigured(): bool
    {
        return filled($this->phoneNumberId) && filled($this->token);
    }

    /**
     * Send a plain text message to a WhatsApp user.
     */
    public function sendText(string $to, string $body): ?array
    {
        if (! $this->isConfigured()) {
            Log::warning('WhatsApp not configured — cannot send message.');

            return null;
        }

        return $this->post([
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $this->normalizePhone($to),
            'type'              => 'text',
            'text'              => ['body' => $body, 'preview_url' => false],
        ]);
    }

    /**
     * Send a template message (e.g., for outbound initiation).
     */
    public function sendTemplate(string $to, string $templateName, string $languageCode = 'ar', array $components = []): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $this->normalizePhone($to),
            'type'              => 'template',
            'template'          => [
                'name'     => $templateName,
                'language' => ['code' => $languageCode],
            ],
        ];

        if (! empty($components)) {
            $payload['template']['components'] = $components;
        }

        return $this->post($payload);
    }

    /**
     * Send a message with interactive quick-reply buttons.
     */
    public function sendButtons(string $to, string $bodyText, array $buttons): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $buttonRows = array_map(function (array $btn) {
            return [
                'type'  => 'reply',
                'reply' => [
                    'id'    => $btn['id'],
                    'title' => $btn['title'],
                ],
            ];
        }, $buttons);

        return $this->post([
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $this->normalizePhone($to),
            'type'              => 'interactive',
            'interactive'       => [
                'type' => 'button',
                'body' => ['text' => $bodyText],
                'action' => ['buttons' => $buttonRows],
            ],
        ]);
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(string $messageId): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        $this->post([
            'messaging_product' => 'whatsapp',
            'status'            => 'read',
            'message_id'        => $messageId,
        ]);
    }

    /**
     * Verify the webhook challenge from Meta.
     */
    public function verifyWebhook(array $query): ?string
    {
        $verifyToken = (string) ($this->config->verify_token ?? '');
        $mode        = $query['hub_mode'] ?? null;
        $token       = $query['hub_verify_token'] ?? null;
        $challenge   = $query['hub_challenge'] ?? null;

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return (string) $challenge;
        }

        return null;
    }

    protected function post(array $payload): ?array
    {
        try {
            $response = $this->http->post("{$this->phoneNumberId}/messages", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (GuzzleException $e) {
            Log::error('WhatsApp API error', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);

            return null;
        }
    }

    protected function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone);
    }
}
