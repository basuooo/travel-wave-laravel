<?php

namespace App\Integrations;

use App\Integrations\Platforms\MetaPlatform;
use App\Integrations\Platforms\TikTokPlatform;
use App\Models\CrmIntegration;
use Exception;

class PlatformManager
{
    /**
     * Get the platform instance based on the integration.
     */
    public static function make(CrmIntegration|string $platform): PlatformInterface
    {
        $type = $platform instanceof CrmIntegration ? $platform->platform : $platform;

        return match ($type) {
            'meta' => new MetaPlatform(),
            'tiktok' => new TikTokPlatform(),
            default => throw new Exception("Platform [{$type}] not supported."),
        };
    }
}
