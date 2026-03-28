<?php

namespace App\Support;

class IconLibrary
{
    public static function render(?string $icon, string $fallback = 'sparkles'): string
    {
        if (static::isIconifyIcon($icon)) {
            return sprintf(
                '<iconify-icon icon="%s" aria-hidden="true" class="tw-iconify-icon"></iconify-icon>',
                e(trim((string) $icon))
            );
        }

        return static::svg($icon, $fallback);
    }

    public static function svg(?string $icon, string $fallback = 'sparkles'): string
    {
        $key = static::normalize($icon) ?: $fallback;
        $path = static::paths()[$key] ?? static::paths()[$fallback];

        return sprintf(
            '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" class="tw-icon-svg"><path d="%s" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
            $path
        );
    }

    protected static function normalize(?string $icon): ?string
    {
        $value = strtolower(trim((string) $icon));

        return match ($value) {
            '', 'tw', 'ok', 'vs', 'pt', 'fe', 'sd', '•', '-', '--' => null,
            'shield', 'security', 'trust' => 'shield',
            'file', 'document', 'documents', 'paper' => 'file',
            'calendar', 'appointment', 'schedule', 'date' => 'calendar',
            'support', 'chat', 'help' => 'support',
            'phone', 'call' => 'phone',
            'mail', 'email' => 'mail',
            'location', 'map', 'pin' => 'location',
            'globe', 'visa', 'travel' => 'globe',
            'star', 'highlight' => 'star',
            'clock', 'time' => 'clock',
            'users', 'group', 'team' => 'users',
            'plane', 'flight' => 'plane',
            'hotel', 'stay', 'bed' => 'hotel',
            'check', 'check-circle', 'approved' => 'check',
            'money', 'fees', 'price' => 'money',
            default => $value,
        };
    }

    public static function isIconifyIcon(?string $icon): bool
    {
        $value = trim((string) $icon);

        return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*:[a-z0-9]+(?:-[a-z0-9]+)*$/i', $value);
    }

    protected static function paths(): array
    {
        return [
            'sparkles' => 'M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3zm6 10l.8 2.2L21 16l-2.2.8L18 19l-.8-2.2L15 16l2.2-.8L18 13zM6 14l1 2.8L10 18l-3 .9L6 22l-1-3.1L2 18l3-.2L6 14z',
            'shield' => 'M12 3l7 3v5c0 4.5-2.9 8.6-7 10-4.1-1.4-7-5.5-7-10V6l7-3zm0 5v8m-3-3l3 3 3-3',
            'file' => 'M8 3h6l4 4v14H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm6 0v4h4M9 12h6M9 16h6',
            'calendar' => 'M7 3v3M17 3v3M4 8h16M5 5h14a1 1 0 0 1 1 1v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a1 1 0 0 1 1-1zm3 7h3v3H8z',
            'support' => 'M12 19v-2m0-10a5 5 0 0 1 5 5v2a2 2 0 0 1-2 2h-1l-2 2v-4H9a2 2 0 0 1-2-2v-2a5 5 0 0 1 5-5z',
            'phone' => 'M6.5 4h3L11 8l-2 2a16 16 0 0 0 7 7l2-2 4 1.5v3a2 2 0 0 1-2 2C11.7 21.5 2.5 12.3 2.5 4a2 2 0 0 1 2-2z',
            'mail' => 'M4 6h16v12H4zM4 7l8 6 8-6',
            'location' => 'M12 21s-6-5.6-6-10a6 6 0 1 1 12 0c0 4.4-6 10-6 10zm0-8.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z',
            'globe' => 'M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18M5 7.5h14M5 16.5h14M12 3a9 9 0 1 1 0 18',
            'star' => 'M12 3l2.7 5.5 6 .9-4.3 4.2 1 6-5.4-2.9-5.4 2.9 1-6L3.3 9.4l6-.9L12 3z',
            'clock' => 'M12 7v5l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z',
            'users' => 'M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2m13-9a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM21 21v-2a4 4 0 0 0-3-3.9M14 4.1a4 4 0 0 1 0 7.8',
            'plane' => 'M3 11l18-5-5 18-2-8-8-2zm11 5l2-7-7 2 5 5z',
            'hotel' => 'M4 20V6h10v14M14 10h6v10M7 9h2m-2 4h2m-2 4h2',
            'check' => 'M5 12l4 4L19 6',
            'money' => 'M4 7h16v10H4zM8 12h8M12 9v6M6 9.5A2.5 2.5 0 0 0 8.5 12 2.5 2.5 0 0 0 6 14.5m12-5A2.5 2.5 0 0 1 15.5 12 2.5 2.5 0 0 1 18 14.5',
        ];
    }
}
