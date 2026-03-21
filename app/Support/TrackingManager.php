<?php

namespace App\Support;

use App\Models\Destination;
use App\Models\TrackingIntegration;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class TrackingManager
{
    public static function integrationTypeOptions(): array
    {
        return [
            TrackingIntegration::TYPE_GTM => 'Google Tag Manager',
            TrackingIntegration::TYPE_GA4 => 'Google Analytics 4',
            TrackingIntegration::TYPE_META_PIXEL => 'Meta Pixel',
            TrackingIntegration::TYPE_CUSTOM_SCRIPT => 'Custom Script / Pixel',
        ];
    }

    public static function placementOptions(): array
    {
        return [
            'standard' => 'Standard placement',
            'head' => 'Head',
            'body_open' => 'Body open',
            'body_end' => 'Body end',
        ];
    }

    public static function visibilityModeOptions(): array
    {
        return [
            'all' => 'All pages',
            'only_selected' => 'Only selected pages',
            'exclude_selected' => 'Exclude selected pages',
        ];
    }

    public static function pageKeyOptions(): array
    {
        return LeadFormManager::pageKeyOptions();
    }

    public static function pageGroupOptions(): array
    {
        return LeadFormManager::pageGroupOptions();
    }

    public static function destinationTypeOptions(): array
    {
        return LeadFormManager::destinationTypeOptions();
    }

    public static function tablesExist(): bool
    {
        return Schema::hasTable('tracking_integrations');
    }

    public static function contextFromRequest(?Request $request = null): array
    {
        $request ??= request();
        $route = $request->route();
        $routeName = $route?->getName();

        $pageKey = match ($routeName) {
            'home' => 'home',
            'visas.index' => 'visas.index',
            'destinations.index' => 'destinations.index',
            'flights' => 'flights',
            'hotels' => 'hotels',
            'about' => 'about',
            'contact' => 'contact',
            default => null,
        };

        /** @var VisaCountry|null $visaCountry */
        $visaCountry = $route?->parameter('country');
        /** @var Destination|null $destination */
        $destination = $route?->parameter('destination');

        return array_filter([
            'route_name' => $routeName,
            'page_key' => $pageKey,
            'visa_country_id' => $visaCountry?->id,
            'visa_category_id' => $visaCountry?->visa_category_id,
            'destination_id' => $destination?->id,
            'destination_type' => $visaCountry ? 'visa' : ($destination?->destination_type ?: ($destination ? 'domestic' : null)),
        ], fn ($value) => $value !== null && $value !== '');
    }

    public static function resolveForPlacement(string $placement, array $context): array
    {
        if (! self::tablesExist()) {
            return [];
        }

        return TrackingIntegration::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(fn (TrackingIntegration $integration) => self::matchesVisibility($integration, $context))
            ->filter(fn (TrackingIntegration $integration) => self::shouldRenderAtPlacement($integration, $placement))
            ->unique(fn (TrackingIntegration $integration) => implode('|', [
                $integration->integration_type,
                $integration->tracking_code,
                $integration->placement,
                md5((string) $integration->script_code),
            ]))
            ->values()
            ->all();
    }

    public static function visibilityTargetOptions(): array
    {
        return [
            'Specific pages' => collect(self::pageKeyOptions())->mapWithKeys(fn ($label, $key) => ['page_key|' . $key => $label])->all(),
            'Page groups' => collect(self::pageGroupOptions())->mapWithKeys(fn ($label, $key) => ['page_group|' . $key => $label])->all(),
            'Visa destinations' => VisaCountry::query()->orderBy('sort_order')->get()
                ->mapWithKeys(fn (VisaCountry $country) => ['visa_country|' . $country->id => $country->name_en . ' / ' . $country->name_ar])
                ->all(),
            'Visa categories' => VisaCategory::query()->orderBy('sort_order')->get()
                ->mapWithKeys(fn (VisaCategory $category) => ['visa_category|' . $category->id => $category->name_en . ' / ' . $category->name_ar])
                ->all(),
            'Domestic destinations' => Destination::query()->orderBy('sort_order')->get()
                ->mapWithKeys(fn (Destination $destination) => ['destination|' . $destination->id => $destination->title_en . ' / ' . $destination->title_ar])
                ->all(),
            'Destination types' => collect(self::destinationTypeOptions())->mapWithKeys(fn ($label, $key) => ['destination_type|' . $key => $label])->all(),
        ];
    }

    protected static function matchesVisibility(TrackingIntegration $integration, array $context): bool
    {
        $mode = $integration->visibility_mode ?: 'all';
        $targets = collect($integration->visibility_targets ?? [])->filter()->values()->all();

        if ($mode === 'all' || $targets === []) {
            return $mode !== 'only_selected' || $targets !== [];
        }

        $matches = self::contextMatchesTargets($context, $targets);

        return match ($mode) {
            'exclude_selected' => ! $matches,
            'only_selected' => $matches,
            default => true,
        };
    }

    protected static function contextMatchesTargets(array $context, array $targets): bool
    {
        foreach ($targets as $target) {
            if (! is_string($target) || ! str_contains($target, '|')) {
                continue;
            }

            [$type, $value] = array_pad(explode('|', $target, 2), 2, null);

            if (! $type || ! $value) {
                continue;
            }

            $matches = match ($type) {
                'page_key' => ($context['page_key'] ?? null) === $value,
                'page_group' => self::matchesPageGroup($value, $context),
                'visa_country' => (int) ($context['visa_country_id'] ?? 0) === (int) $value,
                'visa_category' => (int) ($context['visa_category_id'] ?? 0) === (int) $value,
                'destination' => (int) ($context['destination_id'] ?? 0) === (int) $value,
                'destination_type' => ($context['destination_type'] ?? null) === $value,
                default => false,
            };

            if ($matches) {
                return true;
            }
        }

        return false;
    }

    protected static function matchesPageGroup(string $group, array $context): bool
    {
        return match ($group) {
            'service-pages' => in_array($context['page_key'] ?? null, ['visas.index', 'destinations.index', 'flights', 'hotels'], true),
            'visa-destinations' => ! empty($context['visa_country_id']),
            'domestic-destinations' => ($context['destination_type'] ?? null) === 'domestic',
            default => false,
        };
    }

    protected static function shouldRenderAtPlacement(TrackingIntegration $integration, string $placement): bool
    {
        $configuredPlacement = $integration->placement ?: 'standard';

        return match ($integration->integration_type) {
            TrackingIntegration::TYPE_GTM => in_array($placement, ['head', 'body_open'], true)
                && in_array($configuredPlacement, ['standard', $placement], true),
            TrackingIntegration::TYPE_GA4,
            TrackingIntegration::TYPE_META_PIXEL => $placement === 'head'
                && in_array($configuredPlacement, ['standard', 'head'], true),
            TrackingIntegration::TYPE_CUSTOM_SCRIPT => $configuredPlacement === $placement,
            default => false,
        };
    }
}
