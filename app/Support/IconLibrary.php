<?php

namespace App\Support;

class IconLibrary
{
    public static function render(?string $icon, string $fallback = 'sparkles'): string
    {
        $value = trim((string) $icon);

        if ($value === '') {
            return static::svg(null, $fallback);
        }

        // 1. Raw HTML or SVG code (starts with '<' or contains '<svg' / '<i')
        if (str_starts_with($value, '<') || str_contains($value, '<svg') || str_contains($value, '<i')) {
            return sprintf('<span class="tw-custom-icon-code d-inline-flex align-items-center justify-content-center" aria-hidden="true">%s</span>', $value);
        }

        // 2. Iconify string (e.g. material-symbols:travel-explore-rounded)
        if (static::isIconifyIcon($value)) {
            return sprintf(
                '<iconify-icon icon="%s" aria-hidden="true" class="tw-iconify-icon"></iconify-icon>',
                e($value)
            );
        }

        // 3. CSS Class string (e.g. "fa-solid fa-plane" or "bi bi-star")
        if (str_contains($value, 'fa-') || str_contains($value, 'bi-') || str_contains($value, 'ri-')) {
            return sprintf('<i class="%s" aria-hidden="true"></i>', e($value));
        }

        // 4. Predefined SVG key or keyword
        return static::svg($value, $fallback);
    }

    public static function svg(?string $icon, string $fallback = 'sparkles'): string
    {
        $key = static::normalize($icon) ?: $fallback;
        $paths = static::paths();
        $path = $paths[$key] ?? $paths[$fallback] ?? $paths['sparkles'];

        return sprintf(
            '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" class="tw-icon-svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="%s"></path></svg>',
            $path
        );
    }

    protected static function normalize(?string $icon): ?string
    {
        $value = strtolower(trim((string) $icon));

        if ($value === '' || in_array($value, ['tw', 'ok', 'vs', 'pt', 'fe', 'sd', '•', '-', '--'], true)) {
            return null;
        }

        if (str_contains($value, 'material-symbols:')) {
            $name = str_replace('material-symbols:', '', $value);

            if (str_contains($name, 'passport')) {
                return 'passport';
            }

            if (str_contains($name, 'travel') || str_contains($name, 'explore') || str_contains($name, 'trip') || str_contains($name, 'flight')) {
                return 'globe';
            }

            if (str_contains($name, 'verified') || str_contains($name, 'check') || str_contains($name, 'approve')) {
                return 'check';
            }

            if (str_contains($name, 'money') || str_contains($name, 'attach') || str_contains($name, 'currency')) {
                return 'money';
            }

            if (str_contains($name, 'group') || str_contains($name, 'people') || str_contains($name, 'team')) {
                return 'users';
            }

            if (str_contains($name, 'flash') || str_contains($name, 'bolt')) {
                return 'sparkles';
            }

            if (str_contains($name, 'star')) {
                return 'star';
            }

            if (str_contains($name, 'map') || str_contains($name, 'location') || str_contains($name, 'pin')) {
                return 'location';
            }

            if (str_contains($name, 'card') || str_contains($name, 'plane') || str_contains($name, 'air')) {
                return 'plane';
            }

            if (str_contains($name, 'shield')) {
                return 'shield';
            }

            if (str_contains($name, 'phone')) {
                return 'phone';
            }

            if (str_contains($name, 'mail') || str_contains($name, 'email')) {
                return 'mail';
            }

            if (str_contains($name, 'support') || str_contains($name, 'chat')) {
                return 'support';
            }

            if (str_contains($name, 'calendar') || str_contains($name, 'date') || str_contains($name, 'schedule')) {
                return 'calendar';
            }

            return 'sparkles';
        }

        return match ($value) {
            'passport' => 'passport',
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
            'sun', 'summer' => 'sun',
            'compass', 'guide' => 'compass',
            'ticket' => 'ticket',
            default => $value,
        };
    }

    public static function isIconifyIcon(?string $icon): bool
    {
        $value = trim((string) $icon);

        return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*:[a-z0-9]+(?:-[a-z0-9]+)*$/i', $value);
    }

    public static function presets(): array
    {
        return [
            ['value' => 'globe', 'label' => '🌍 طيران وسفريات (Globe/Travel)'],
            ['value' => 'plane', 'label' => '✈️ طائرة ورحلات (Plane)'],
            ['value' => 'passport', 'label' => '🛂 جواز سفر وتأشيرة (Passport)'],
            ['value' => 'shield', 'label' => '🛡️ أمان وثقة (Shield)'],
            ['value' => 'file', 'label' => '📄 مستندات وأوراق (Documents)'],
            ['value' => 'calendar', 'label' => '📅 مواعيد وتقويم (Calendar)'],
            ['value' => 'support', 'label' => '🎧 دعم واستشارات (Support)'],
            ['value' => 'phone', 'label' => '📞 هاتف وتواصل (Phone)'],
            ['value' => 'mail', 'label' => '✉️ بريد إلكتروني (Email)'],
            ['value' => 'location', 'label' => '📍 موقع وخريطة (Location)'],
            ['value' => 'star', 'label' => '⭐ نجمة ومميز (Star)'],
            ['value' => 'clock', 'label' => '🕒 وقت وسرعة (Clock)'],
            ['value' => 'users', 'label' => '👥 فريق وعملاء (Users)'],
            ['value' => 'hotel', 'label' => '🏨 فنادق وإقامة (Hotel)'],
            ['value' => 'check', 'label' => '✅ توثيق وتأكيد (Check)'],
            ['value' => 'money', 'label' => '💵 أسعار ورسوم (Money)'],
            ['value' => 'sun', 'label' => '☀️ وجهات صيفية (Sun)'],
            ['value' => 'compass', 'label' => '🧭 استكشاف ودليل (Compass)'],
            ['value' => 'ticket', 'label' => '🎫 تذاكر وحجوزات (Ticket)'],
            ['value' => 'sparkles', 'label' => '✨ ميزات عامة (Sparkles)'],
        ];
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
            'passport' => 'M4 4h16v16H4zM9 8h6M9 12h6M12 16a2 2 0 1 0 0-4 2 2 0 0 0 0 4z',
            'sun' => 'M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0-5v2m0 14v2m-7.07-14.07l1.41 1.41m11.32 11.32l1.41 1.41M3 12h2m14 0h2m-14.07 7.07l1.41-1.41m11.32-11.32l1.41-1.41',
            'compass' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm4.24 5.76l-2.83 6.36-6.36 2.83 2.83-6.36 6.36-2.83z',
            'ticket' => 'M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4V9z',
        ];
    }
}
