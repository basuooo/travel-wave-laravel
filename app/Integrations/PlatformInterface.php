<?php

namespace App\Integrations;

use App\Models\CrmIntegration;
use Illuminate\Http\Request;

interface PlatformInterface
{
    /**
     * Verify the incoming webhook request.
     */
    public function verifyWebhook(Request $request, CrmIntegration $integration): bool;

    /**
     * Normalize the raw payload into a standard lead array.
     */
    public function normalizeLead(array $payload): array;

    /**
     * Test the connection to the platform.
     */
    public function testConnection(CrmIntegration $integration): bool;

    /**
     * Get platform-specific settings fields.
     */
    public function getSettingsFields(): array;
}
