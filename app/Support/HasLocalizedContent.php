<?php

namespace App\Support;

trait HasLocalizedContent
{
    public function localized(string $field, ?string $locale = null, mixed $fallback = ''): mixed
    {
        $locale = $locale ?: app()->getLocale();
        $localizedField = "{$field}_{$locale}";
        $fallbackField = "{$field}_" . config('app.fallback_locale', 'en');

        return $this->{$localizedField}
            ?? $this->{$fallbackField}
            ?? $this->{$field}
            ?? $fallback;
    }
}
