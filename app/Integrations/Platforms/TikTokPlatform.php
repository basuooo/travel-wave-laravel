<?php

namespace App\Integrations\Platforms;

use App\Integrations\BasePlatform;
use App\Models\CrmIntegration;
use Illuminate\Http\Request;

class TikTokPlatform extends BasePlatform
{
    public function verifyWebhook(Request $request, CrmIntegration $integration): bool
    {
        // TikTok uses a signature in the header
        $signature = $request->header('X-TikTok-Signature');
        if (!$signature) return false;

        $expectedSignature = hash_hmac('sha256', $request->getContent(), $integration->credentials['client_secret'] ?? '');

        return hash_equals($signature, $expectedSignature);
    }

    public function normalizeLead(array $payload): array
    {
        return [
            'external_id' => $payload['lead_id'] ?? null,
            'full_name' => $payload['full_name'] ?? ($payload['first_name'] . ' ' . ($payload['last_name'] ?? '')),
            'phone' => $payload['phone_number'] ?? null,
            'email' => $payload['email'] ?? null,
            'platform' => 'tiktok',
            'campaign_name' => $payload['campaign_name'] ?? null,
            'adset_name' => $payload['adgroup_name'] ?? null,
            'ad_name' => $payload['ad_name'] ?? null,
            'source' => 'TikTok Lead Generation',
            'country' => $payload['country'] ?? null,
            'metadata' => $payload,
        ];
    }

    public function testConnection(CrmIntegration $integration): bool
    {
        // Simple TikTok API test
        $response = $this->makeRequest($integration, 'GET', "https://business-api.tiktok.com/open_api/v1.3/user/info/", [
            'headers' => ['Access-Token' => $integration->credentials['access_token'] ?? '']
        ]);

        return $response->successful();
    }

    public function getSettingsFields(): array
    {
        return [
            'app_id' => ['type' => 'text', 'label' => 'App ID'],
            'client_secret' => ['type' => 'password', 'label' => 'Client Secret'],
            'access_token' => ['type' => 'textarea', 'label' => 'Access Token'],
        ];
    }
}
