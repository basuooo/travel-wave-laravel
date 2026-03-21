<?php

namespace App\Support;

use App\Models\Destination;
use App\Models\LeadForm;
use App\Models\LeadFormAssignment;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Support\Collection;

class LeadFormManager
{
    public static function positions(): array
    {
        return [
            'top' => 'Top of page',
            'below_hero' => 'Below hero',
            'middle' => 'Middle of page',
            'before_faq' => 'Before FAQ',
            'after_faq' => 'After FAQ',
            'bottom' => 'Bottom of page',
            'sidebar' => 'Sidebar',
        ];
    }

    public static function assignmentTypeOptions(): array
    {
        return [
            LeadFormAssignment::PAGE_KEY => 'Specific page',
            LeadFormAssignment::PAGE_GROUP => 'Page group',
            LeadFormAssignment::VISA_COUNTRY => 'Specific visa destination',
            LeadFormAssignment::VISA_CATEGORY => 'Visa category',
            LeadFormAssignment::DESTINATION => 'Specific domestic destination',
            LeadFormAssignment::DESTINATION_TYPE => 'Destination type',
        ];
    }

    public static function pageKeyOptions(): array
    {
        return [
            'home' => 'Homepage',
            'visas.index' => 'External Visa Services',
            'destinations.index' => 'Domestic Tourism',
            'flights' => 'Flights',
            'hotels' => 'Hotels',
            'about' => 'About Us',
            'contact' => 'Contact Us',
        ];
    }

    public static function pageGroupOptions(): array
    {
        return [
            'service-pages' => 'All service pages',
            'visa-destinations' => 'All visa destination pages',
            'domestic-destinations' => 'All domestic destination pages',
        ];
    }

    public static function destinationTypeOptions(): array
    {
        return [
            'domestic' => 'Domestic Tourism destinations',
            'visa' => 'Visa destinations',
        ];
    }

    public static function resolve(array $context): array
    {
        $assignments = LeadFormAssignment::query()
            ->with(['form.fields'])
            ->where('is_active', true)
            ->whereHas('form', fn ($query) => $query->where('is_active', true))
            ->orderBy('display_position')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (LeadFormAssignment $assignment) => self::matchesContext($assignment, $context))
            ->values();

        return $assignments
            ->groupBy('display_position')
            ->map(fn (Collection $group) => $group->sortBy('sort_order')->values()->all())
            ->all();
    }

    public static function matchesContext(LeadFormAssignment $assignment, array $context): bool
    {
        return match ($assignment->assignment_type) {
            LeadFormAssignment::PAGE_KEY => ($context['page_key'] ?? null) === $assignment->target_key,
            LeadFormAssignment::PAGE_GROUP => self::matchesPageGroup($assignment->target_key, $context),
            LeadFormAssignment::VISA_COUNTRY => ($context['visa_country_id'] ?? null) === $assignment->target_id,
            LeadFormAssignment::VISA_CATEGORY => ($context['visa_category_id'] ?? null) === $assignment->target_id,
            LeadFormAssignment::DESTINATION => ($context['destination_id'] ?? null) === $assignment->target_id,
            LeadFormAssignment::DESTINATION_TYPE => ($context['destination_type'] ?? null) === $assignment->target_key,
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

    public static function fieldTypeOptions(): array
    {
        return [
            'text' => 'Text',
            'phone' => 'Phone',
            'email' => 'Email',
            'textarea' => 'Textarea',
            'select' => 'Select',
            'date' => 'Date',
            'number' => 'Number',
            'hidden' => 'Hidden',
        ];
    }

    public static function categoryOptions(): array
    {
        return [
            'visa' => 'External Visa',
            'domestic' => 'Domestic Tourism',
            'flights' => 'Flights',
            'hotels' => 'Hotels',
            'contact' => 'Contact',
            'general' => 'General',
        ];
    }

    public static function contextForPageKey(string $pageKey): array
    {
        return ['page_key' => $pageKey];
    }

    public static function contextForVisaCountry(VisaCountry $country): array
    {
        return [
            'page_key' => null,
            'visa_country_id' => $country->id,
            'visa_category_id' => $country->visa_category_id,
            'destination_type' => 'visa',
        ];
    }

    public static function contextForDestination(Destination $destination): array
    {
        return [
            'page_key' => null,
            'destination_id' => $destination->id,
            'destination_type' => $destination->destination_type ?: 'domestic',
        ];
    }
}
