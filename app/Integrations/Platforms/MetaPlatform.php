<?php

namespace App\Integrations\Platforms;

use App\Integrations\BasePlatform;
use App\Models\CrmIntegration;
use Illuminate\Http\Request;

class MetaPlatform extends BasePlatform
{
    public function verifyWebhook(Request $request, CrmIntegration $integration): bool
    {
        // Meta uses a verify_token for setup and X-Hub-Signature-256 for requests
        if ($request->has('hub_mode') && $request->get('hub_mode') === 'subscribe') {
            return $request->get('hub_verify_token') === $integration->webhook_verify_token;
        }

        $signature = $request->header('X-Hub-Signature-256');
        if (!$signature) return false;

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $integration->credentials['app_secret'] ?? '');

        return hash_equals($signature, $expectedSignature);
    }

    public function normalizeLead(array $payload): array
    {
        // Meta lead payload structure is usually deeply nested
        // We'll map it to our unified structure
        $fieldData = [];
        foreach ($payload['field_data'] ?? [] as $field) {
            $fieldData[$field['name']] = $field['values'][0] ?? null;
        }

        return [
            'external_id' => $payload['id'] ?? null,
            'full_name' => $fieldData['full_name'] ?? $fieldData['first_name'] . ' ' . ($fieldData['last_name'] ?? ''),
            'phone' => $fieldData['phone_number'] ?? null,
            'email' => $fieldData['email'] ?? null,
            'platform' => 'meta',
            'campaign_name' => $payload['campaign_name'] ?? null,
            'adset_name' => $payload['adset_name'] ?? null,
            'ad_name' => $payload['ad_name'] ?? null,
            'source' => 'Meta Lead Ads',
            'country' => $fieldData['country'] ?? null,
            'metadata' => $payload,
        ];
    }

    public function testConnection(CrmIntegration $integration): bool
    {
        $response = $this->makeRequest($integration, 'GET', "https://graph.facebook.com/v19.0/me", [
            'query' => ['access_token' => $integration->credentials['access_token'] ?? '']
        ]);

        return $response->successful();
    }

    public function getSettingsFields(): array
    {
        return [
            'app_id' => ['type' => 'text', 'label' => 'App ID'],
            'app_secret' => ['type' => 'password', 'label' => 'App Secret'],
            'access_token' => ['type' => 'textarea', 'label' => 'Page Access Token'],
        ];
    }
}
