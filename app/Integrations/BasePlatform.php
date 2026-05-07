<?php

namespace App\Integrations;

use App\Models\CrmApiLog;
use App\Models\CrmIntegration;
use Illuminate\Support\Facades\Http;

abstract class BasePlatform implements PlatformInterface
{
    /**
     * Log an API request.
     */
    protected function logApiCall(CrmIntegration $integration, string $endpoint, string $method, array $request, array $response, int $statusCode, int $duration)
    {
        CrmApiLog::create([
            'integration_id' => $integration->id,
            'endpoint' => $endpoint,
            'method' => $method,
            'request_payload' => $request,
            'response_payload' => $response,
            'status_code' => $statusCode,
            'duration_ms' => $duration,
        ]);
    }

    /**
     * Common HTTP request helper with logging.
     */
    protected function makeRequest(CrmIntegration $integration, string $method, string $url, array $options = [])
    {
        $start = microtime(true);
        $response = Http::send($method, $url, $options);
        $duration = (int) ((microtime(true) - $start) * 1000);

        $this->logApiCall(
            $integration,
            $url,
            $method,
            $options['json'] ?? $options['form_params'] ?? [],
            $response->json() ?? ['raw' => $response->body()],
            $response->status(),
            $duration
        );

        return $response;
    }
}
