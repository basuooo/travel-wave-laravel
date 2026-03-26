<?php

namespace App\Support;

use App\Models\UtmCampaign;

class UtmBuilderService
{
    public function buildUrl(string $baseUrl, array $parameters = []): string
    {
        $parts = parse_url($baseUrl);
        $existingQuery = [];

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $existingQuery);
        }

        $utmParameters = collect($parameters)
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => trim((string) $value))
            ->all();

        $query = array_merge($existingQuery, $utmParameters);
        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $user = $parts['user'] ?? '';
        $pass = isset($parts['pass']) ? ':' . $parts['pass'] : '';
        $pass = ($user || $pass) ? $pass . '@' : '';
        $path = $parts['path'] ?? '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        return $scheme . $user . $pass . $host . $port . $path . ($queryString !== '' ? '?' . $queryString : '') . $fragment;
    }

    public function validatedPayload(array $data): array
    {
        return [
            'display_name' => trim((string) ($data['display_name'] ?? '')),
            'base_url' => trim((string) ($data['base_url'] ?? '')),
            'generated_url' => $this->buildUrl((string) ($data['base_url'] ?? ''), [
                'utm_source' => $data['utm_source'] ?? null,
                'utm_medium' => $data['utm_medium'] ?? null,
                'utm_campaign' => $data['utm_campaign'] ?? null,
                'utm_id' => $data['utm_id'] ?? null,
                'utm_term' => $data['utm_term'] ?? null,
                'utm_content' => $data['utm_content'] ?? null,
            ]),
            'utm_source' => $this->nullableString($data['utm_source'] ?? null),
            'utm_medium' => $this->nullableString($data['utm_medium'] ?? null),
            'utm_campaign' => $this->nullableString($data['utm_campaign'] ?? null),
            'utm_id' => $this->nullableString($data['utm_id'] ?? null),
            'utm_term' => $this->nullableString($data['utm_term'] ?? null),
            'utm_content' => $this->nullableString($data['utm_content'] ?? null),
            'platform' => $this->nullableString($data['platform'] ?? null),
            'owner_user_id' => filled($data['owner_user_id'] ?? null) ? (int) $data['owner_user_id'] : null,
            'status' => $this->nullableString($data['status'] ?? null) ?: UtmCampaign::STATUS_ACTIVE,
            'notes' => $this->nullableString($data['notes'] ?? null),
        ];
    }

    protected function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
