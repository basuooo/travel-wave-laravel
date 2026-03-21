<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MetaConversionApiService
{
    public function isEnabled(?Setting $settings = null): bool
    {
        $settings ??= Setting::query()->first();

        return (bool) $settings?->metaConversionApiConfigured();
    }

    public function track(string $eventName, Request $request, array $payload = [], ?Setting $settings = null): bool
    {
        $settings ??= Setting::query()->first();

        if (! $settings?->metaConversionApiConfigured()) {
            return false;
        }

        $pixelId = $settings->metaPixelId();

        if (! $pixelId) {
            return false;
        }

        $eventId = (string) ($payload['event_id'] ?? Str::uuid());
        $eventSourceUrl = $payload['event_source_url']
            ?? $request->fullUrl()
            ?? $settings->meta_conversion_api_default_event_source_url;

        $requestPayload = [
            'data' => [[
                'event_name' => $eventName,
                'event_time' => (int) ($payload['event_time'] ?? now()->timestamp),
                'event_id' => $eventId,
                'event_source_url' => $eventSourceUrl,
                'action_source' => 'website',
                'user_data' => $this->buildUserData($request, $payload['user_data'] ?? []),
                'custom_data' => $this->cleanPayload($payload['custom_data'] ?? []),
            ]],
            'access_token' => $settings->meta_conversion_api_access_token,
        ];

        if (filled($settings->meta_conversion_api_test_event_code)) {
            $requestPayload['test_event_code'] = $settings->meta_conversion_api_test_event_code;
        }

        try {
            $response = Http::asJson()
                ->timeout(8)
                ->post("https://graph.facebook.com/v18.0/{$pixelId}/events", $requestPayload);

            if ($response->failed()) {
                Log::warning('Meta Conversion API request failed.', [
                    'event_name' => $eventName,
                    'event_id' => $eventId,
                    'pixel_id' => $pixelId,
                    'status' => $response->status(),
                    'body' => Str::limit((string) $response->body(), 1000),
                ]);

                return false;
            }

            return true;
        } catch (Throwable $exception) {
            Log::warning('Meta Conversion API exception.', [
                'event_name' => $eventName,
                'event_id' => $eventId,
                'pixel_id' => $pixelId,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    protected function buildUserData(Request $request, array $userData = []): array
    {
        $fullName = $userData['full_name'] ?? $userData['name'] ?? null;
        [$firstName, $lastName] = $this->splitName($fullName);

        $payload = array_filter([
            'em' => $this->hashValue($this->normalizeEmail($userData['email'] ?? null)),
            'ph' => $this->hashValue($this->normalizePhone($userData['phone'] ?? null)),
            'fn' => $this->hashValue($this->normalizeName($userData['first_name'] ?? $firstName)),
            'ln' => $this->hashValue($this->normalizeName($userData['last_name'] ?? $lastName)),
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
            'fbp' => $request->cookie('_fbp'),
            'fbc' => $request->cookie('_fbc'),
        ], fn ($value) => filled($value));

        return $payload;
    }

    protected function splitName(?string $name): array
    {
        $parts = preg_split('/\s+/u', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);

        if (empty($parts)) {
            return [null, null];
        }

        if (count($parts) === 1) {
            return [$parts[0], $parts[0]];
        }

        return [Arr::first($parts), Arr::last($parts)];
    }

    protected function normalizeEmail(?string $email): ?string
    {
        $email = Str::lower(trim((string) $email));

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    protected function normalizePhone(?string $phone): ?string
    {
        $phone = preg_replace('/\D+/', '', (string) $phone);

        return $phone !== '' ? $phone : null;
    }

    protected function normalizeName(?string $name): ?string
    {
        $name = Str::lower(trim((string) $name));
        $name = preg_replace('/\s+/u', ' ', $name);

        return $name !== '' ? $name : null;
    }

    protected function hashValue(?string $value): ?string
    {
        return filled($value) ? hash('sha256', $value) : null;
    }

    protected function cleanPayload(array $payload): array
    {
        return collect($payload)
            ->map(fn ($value) => is_array($value) ? $this->cleanPayload($value) : $value)
            ->filter(fn ($value) => ! is_null($value) && $value !== '')
            ->all();
    }
};
