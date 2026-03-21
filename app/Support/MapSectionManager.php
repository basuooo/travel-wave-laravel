<?php

namespace App\Support;

use App\Models\Destination;
use App\Models\MapSectionAssignment;
use App\Models\VisaCountry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class MapSectionManager
{
    public static function positions(): array
    {
        return [
            'top' => 'Top of page',
            'below_hero' => 'Below hero',
            'middle' => 'Middle of page',
            'above_form' => 'Above contact form',
            'below_form' => 'Below contact form',
            'before_faq' => 'Before FAQ',
            'after_faq' => 'After FAQ',
            'bottom' => 'Bottom of page',
            'sidebar' => 'Sidebar',
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

    public static function layoutTypeOptions(): array
    {
        return [
            'split' => 'Split: content + map',
            'card' => 'Map inside card',
            'full_width' => 'Full width map',
            'compact' => 'Compact map section',
        ];
    }

    public static function backgroundStyleOptions(): array
    {
        return [
            'default' => 'Default',
            'soft' => 'Soft tinted',
            'plain' => 'Plain white',
            'dark' => 'Dark premium',
        ];
    }

    public static function spacingPresetOptions(): array
    {
        return [
            'normal' => 'Normal spacing',
            'compact' => 'Compact spacing',
            'spacious' => 'Spacious spacing',
        ];
    }

    public static function resolve(array $context): array
    {
        if (!self::tablesExist()) {
            return [];
        }

        $assignments = MapSectionAssignment::query()
            ->with('mapSection')
            ->where('is_active', true)
            ->whereHas('mapSection', fn ($query) => $query->where('is_active', true))
            ->orderBy('display_position')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (MapSectionAssignment $assignment) => self::matchesContext($assignment, $context))
            ->values();

        return $assignments
            ->groupBy('display_position')
            ->map(fn (Collection $group) => $group->sortBy('sort_order')->values()->all())
            ->all();
    }

    public static function tablesExist(): bool
    {
        return Schema::hasTable('map_sections') && Schema::hasTable('map_section_assignments');
    }

    public static function matchesContext(MapSectionAssignment $assignment, array $context): bool
    {
        return match ($assignment->assignment_type) {
            MapSectionAssignment::PAGE_KEY => ($context['page_key'] ?? null) === $assignment->target_key,
            MapSectionAssignment::PAGE_GROUP => self::matchesPageGroup($assignment->target_key, $context),
            MapSectionAssignment::VISA_COUNTRY => ($context['visa_country_id'] ?? null) === $assignment->target_id,
            MapSectionAssignment::VISA_CATEGORY => ($context['visa_category_id'] ?? null) === $assignment->target_id,
            MapSectionAssignment::DESTINATION => ($context['destination_id'] ?? null) === $assignment->target_id,
            MapSectionAssignment::DESTINATION_TYPE => ($context['destination_type'] ?? null) === $assignment->target_key,
            default => false,
        };
    }

    protected static function matchesPageGroup(?string $group, array $context): bool
    {
        return match ($group) {
            'service-pages' => in_array($context['page_key'] ?? null, ['visas.index', 'destinations.index', 'flights', 'hotels'], true),
            'visa-destinations' => !empty($context['visa_country_id']),
            'domestic-destinations' => ($context['destination_type'] ?? null) === 'domestic',
            default => false,
        };
    }

    public static function contextForPageKey(string $pageKey): array
    {
        return ['page_key' => $pageKey];
    }

    public static function contextForVisaCountry(VisaCountry $country): array
    {
        return LeadFormManager::contextForVisaCountry($country);
    }

    public static function contextForDestination(Destination $destination): array
    {
        return LeadFormManager::contextForDestination($destination);
    }
}
