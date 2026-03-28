<?php

namespace App\Support;

use App\Models\Destination;
use App\Models\VisaCountry;
use Illuminate\Support\Collection;

class DestinationPageData
{
    public static function fromVisaCountry(VisaCountry $country): array
    {
        $summaryItems = collect($country->quick_summary_items ?: [])
            ->filter(fn (array $item) => ($item['is_active'] ?? true))
            ->sortBy('sort_order')
            ->values();

        $destinationSummaryItem = [
            'label_en' => trim((string) ($country->quick_summary_destination_label_en ?: __('ui.destination'))),
            'label_ar' => trim((string) ($country->quick_summary_destination_label_ar ?: __('ui.destination'))),
            'value_en' => trim((string) $country->name_en),
            'value_ar' => trim((string) $country->name_ar),
            'icon' => trim((string) ($country->quick_summary_destination_icon ?: 'globe')),
            'sort_order' => 0,
            'is_active' => true,
        ];

        if ($summaryItems->isEmpty()) {
            $summaryItems = collect([
                ['title_en' => 'Visa Type', 'title_ar' => 'نوع الخدمة', 'value_en' => $country->localized('visa_type'), 'value_ar' => $country->localized('visa_type'), 'icon' => 'globe'],
                ['title_en' => 'Processing Time', 'title_ar' => 'مدة المعالجة', 'value_en' => $country->localized('processing_time'), 'value_ar' => $country->localized('processing_time'), 'icon' => 'clock'],
                ['title_en' => 'Approx. Fees', 'title_ar' => 'الرسوم', 'value_en' => $country->localized('fees'), 'value_ar' => $country->localized('fees'), 'icon' => 'money'],
                ['title_en' => 'Stay Duration', 'title_ar' => 'مدة الإقامة', 'value_en' => $country->localized('stay_duration'), 'value_ar' => $country->localized('stay_duration'), 'icon' => 'calendar'],
            ])->filter(fn (array $item) => filled($country->repeaterValue($item, 'value')));
        }

        $summaryItems = collect([$destinationSummaryItem])
            ->merge($summaryItems)
            ->values();

        $heroTitle = $country->localized('hero_title') ?: $country->localized('name');
        $heroSubtitle = $country->localized('hero_subtitle') ?: $country->localized('overview');

        return [
            'model' => $country,
            'type' => 'visa',
            'title' => $country->localized('name'),
            'subtitle' => $country->localized('excerpt'),
            'meta_title' => $country->localized('meta_title') ?: $heroTitle,
            'meta_description' => $country->localized('meta_description') ?: $country->localized('excerpt'),
            'og_image' => self::storageUrl($country->og_image ?: $country->hero_image),
            'breadcrumbs' => array_values(array_filter([
                ['label' => __('ui.home'), 'url' => route('home')],
                ['label' => __('ui.visas'), 'url' => route('visas.index')],
                $country->category ? ['label' => $country->category->localized('name'), 'url' => route('visas.category', $country->category)] : null,
                ['label' => $country->localized('name'), 'url' => null],
            ])),
            'hero' => [
                'enabled' => true,
                'badge' => $country->localized('hero_badge'),
                'title' => $heroTitle,
                'subtitle' => $heroSubtitle,
                'background_image' => self::storageUrl($country->hero_image),
                'mobile_background_image' => self::storageUrl($country->hero_mobile_image ?: $country->hero_image),
                'flag_image' => self::storageUrl($country->flag_image),
                'overlay_opacity' => (float) ($country->hero_overlay_opacity ?? 0.45),
                'primary_button' => [
                    'text' => $country->localized('hero_cta_text') ?: __('ui.inquire_now'),
                    'url' => $country->hero_cta_url ?: '#destination-form',
                ],
                'secondary_button' => [
                    'text' => __('ui.quick_summary'),
                    'url' => '#destination-summary',
                ],
            ],
            'quick_info' => [
                'enabled' => true,
                'title' => __('ui.quick_summary'),
                'items' => self::localizedRepeater($summaryItems, $country, fn (array $item) => [
                    'label' => $country->repeaterValue($item, 'label', fallback: $country->repeaterValue($item, 'title')),
                    'value' => $country->repeaterValue($item, 'value'),
                    'icon' => $item['icon'] ?? '',
                ]),
            ],
            'about' => [
                'enabled' => true,
                'title' => $country->localized('introduction_title') ?: __('ui.visa_overview'),
                'description' => $country->localized('overview'),
                'image' => self::storageUrl($country->intro_image ?: $country->hero_image),
                'badge' => $country->localized('introduction_badge'),
                'points' => self::localizedRepeater($country->introduction_points ?: [], $country, fn (array $item) => [
                    'text' => $country->repeaterValue($item, 'text'),
                ]),
            ],
            'details' => [
                'enabled' => true,
                'title' => $country->localized('detailed_title') ?: __('ui.visa_details'),
                'description' => $country->localized('detailed_description'),
            ],
            'best_time' => self::normalizeBestTimeSection([
                'enabled' => true,
                'badge' => $country->localized('best_time_badge') ?: self::defaultVisaCountryBestTimeCopy('badge'),
                'title' => $country->localized('best_time_title') ?: self::defaultVisaCountryBestTimeCopy('title'),
                'description' => $country->localized('best_time_description') ?: self::defaultVisaCountryBestTimeCopy('description'),
            ]),
            'highlights' => [
                'enabled' => collect($country->highlights ?: [])->isNotEmpty(),
                'label' => $country->localized('highlights_section_label') ?: (app()->getLocale() === 'ar' ? 'أهم الإرشادات' : 'Helpful Guidance Points'),
                'title' => $country->localized('highlights_section_title') ?: (app()->getLocale() === 'ar' ? 'أبرز النقاط المهمة' : 'Key Highlights'),
                'items' => self::localizedRepeater($country->highlights ?: [], $country, fn (array $item) => [
                    'title' => $country->repeaterValue($item, 'title') ?: self::compactHighlightTitle($country->repeaterValue($item, 'text'), app()->getLocale()),
                    'description' => $country->repeaterValue($item, 'description') ?: $country->repeaterValue($item, 'text'),
                    'image' => self::storageUrl($item['image'] ?? null),
                    'icon' => 'star',
                ], active: true),
            ],
            'services' => [
                'enabled' => collect($country->why_choose_items ?: [])->isNotEmpty(),
                'title' => $country->localized('why_choose_title') ?: __('ui.why_choose_travel_wave'),
                'description' => $country->localized('why_choose_intro'),
                'items' => self::localizedRepeater($country->why_choose_items ?: [], $country, fn (array $item) => [
                    'title' => $country->repeaterValue($item, 'title'),
                    'description' => $country->repeaterValue($item, 'description'),
                    'icon' => $item['icon'] ?? '',
                ], active: true),
            ],
            'documents' => [
                'enabled' => collect($country->document_items ?: [])->isNotEmpty(),
                'title' => $country->localized('documents_title') ?: __('ui.required_documents'),
                'description' => $country->localized('documents_subtitle'),
                'items' => self::localizedRepeater($country->document_items ?: [], $country, fn (array $item) => [
                    'title' => $country->repeaterValue($item, 'name'),
                    'description' => $country->repeaterValue($item, 'description'),
                    'icon' => 'check',
                ], active: true),
            ],
            'steps' => [
                'enabled' => collect($country->step_items ?: [])->isNotEmpty(),
                'title' => $country->localized('steps_title') ?: __('ui.application_steps'),
                'items' => self::localizedRepeater($country->step_items ?: [], $country, fn (array $item) => [
                    'number' => $item['step_number'] ?? null,
                    'title' => $country->repeaterValue($item, 'title'),
                    'description' => $country->repeaterValue($item, 'description'),
                    'icon' => '',
                ], active: true),
            ],
            'pricing' => [
                'enabled' => filled($country->localized('fees_title')) || collect($country->fee_items ?: [])->isNotEmpty(),
                'title' => $country->localized('fees_title') ?: __('ui.fees_processing'),
                'description' => $country->localized('fees_notes') ?: $country->localized('fees'),
                'items' => self::localizedRepeater($country->fee_items ?: [], $country, fn (array $item) => [
                    'label' => $country->repeaterValue($item, 'label'),
                    'value' => $country->repeaterValue($item, 'value'),
                    'note' => '',
                ], active: true),
            ],
            'faq' => [
                'enabled' => collect($country->faqs ?: [])->isNotEmpty(),
                'title' => $country->localized('faq_title') ?: __('ui.faq'),
                'items' => self::localizedRepeater($country->faqs ?: [], $country, fn (array $item) => [
                    'question' => $country->repeaterValue($item, 'question'),
                    'answer' => $country->repeaterValue($item, 'answer'),
                ], active: true),
            ],
            'map' => [
                'enabled' => (bool) $country->map_is_active && filled($country->map_embed_code),
                'title' => $country->localized('map_title') ?: __('ui.location_map'),
                'description' => $country->localized('map_description'),
                'embed_code' => $country->map_embed_code,
            ],
            'cta' => [
                'enabled' => (bool) $country->final_cta_is_active,
                'title' => $country->localized('cta_title') ?: __('ui.ready_to_apply'),
                'description' => $country->localized('cta_text'),
                'background_image' => self::storageUrl($country->final_cta_background_image),
                'buttons' => array_values(array_filter([
                    [
                        'text' => $country->localized('cta_button') ?: __('ui.inquire_now'),
                        'url' => $country->cta_url ?: '#destination-form',
                        'style' => 'primary',
                    ],
                    ($country->support_is_active && filled($country->localized('support_button')))
                        ? [
                            'text' => $country->localized('support_button'),
                            'url' => $country->support_button_link ?: '#destination-form',
                            'style' => 'outline',
                        ]
                        : null,
                ])),
            ],
            'form' => [
                'enabled' => (bool) $country->inquiry_form_is_active,
                'section_label' => $country->localized('inquiry_form_label') ?: __('ui.contact_us'),
                'title' => $country->localized('inquiry_form_title') ?: __('ui.ask_about_visa'),
                'subtitle' => $country->localized('inquiry_form_subtitle'),
                'type' => 'visa',
                'source' => $country->localized('name') . ' Visa',
                'destination' => $country->localized('name'),
                'default_visa_type' => $country->inquiry_form_default_service_type ?: $country->localized('visa_type'),
                'config' => [
                    'title' => $country->localized('inquiry_form_title') ?: __('ui.ask_about_visa'),
                    'subtitle' => $country->localized('inquiry_form_subtitle'),
                    'submit_text' => $country->localized('inquiry_form_button') ?: __('ui.inquire_now'),
                    'default_service_type' => $country->inquiry_form_default_service_type ?: $country->localized('name') . ' Visa',
                    'success_message' => $country->localized('inquiry_form_success'),
                    'visible_fields' => $country->inquiry_form_visible_fields ?: ['full_name', 'phone', 'whatsapp_number', 'email', 'service_type', 'destination', 'travel_date', 'message'],
                ],
                'highlights' => self::localizedRepeater($summaryItems->take(3), $country, fn (array $item) => [
                    'label' => $country->repeaterValue($item, 'label', fallback: $country->repeaterValue($item, 'title')),
                    'value' => $country->repeaterValue($item, 'value'),
                ]),
            ],
        ];
    }

    public static function fromDestination(Destination $destination): array
    {
        $heroTitle = $destination->localized('hero_title') ?: $destination->localized('title');
        $heroSubtitle = $destination->localized('hero_subtitle') ?: $destination->localized('subtitle') ?: $destination->localized('excerpt');
        $quickInfoItems = collect($destination->quick_info_items ?: [])
            ->filter(fn (array $item) => ($item['is_active'] ?? true))
            ->sortBy('sort_order')
            ->values();
        $destinationSummaryItem = [
            'label_en' => trim((string) ($destination->quick_summary_destination_label_en ?: __('ui.destination'))),
            'label_ar' => trim((string) ($destination->quick_summary_destination_label_ar ?: __('ui.destination'))),
            'value_en' => trim((string) $destination->title_en),
            'value_ar' => trim((string) $destination->title_ar),
            'icon' => trim((string) ($destination->quick_summary_destination_icon ?: 'globe')),
            'sort_order' => 0,
            'is_active' => true,
        ];
        $quickInfoSummaryItems = collect([$destinationSummaryItem])
            ->merge($quickInfoItems)
            ->values();

        return [
            'model' => $destination,
            'type' => $destination->destination_type ?: 'domestic',
            'title' => $destination->localized('title'),
            'subtitle' => $destination->localized('subtitle') ?: $destination->localized('excerpt'),
            'meta_title' => $destination->localized('meta_title') ?: $heroTitle,
            'meta_description' => $destination->localized('meta_description') ?: $destination->localized('excerpt'),
            'og_image' => self::storageUrl($destination->featured_image ?: $destination->hero_image),
            'breadcrumbs' => [
                ['label' => __('ui.home'), 'url' => route('home')],
                ['label' => __('ui.domestic_tourism'), 'url' => route('destinations.index')],
                ['label' => $destination->localized('title'), 'url' => null],
            ],
            'hero' => [
                'enabled' => (bool) $destination->show_hero,
                'badge' => $destination->localized('hero_badge'),
                'title' => $heroTitle,
                'subtitle' => $heroSubtitle,
                'background_image' => self::storageUrl($destination->hero_image),
                'mobile_background_image' => self::storageUrl($destination->hero_mobile_image ?: $destination->hero_image),
                'flag_image' => self::storageUrl($destination->flag_image),
                'overlay_opacity' => (float) ($destination->hero_overlay_opacity ?? 0.45),
                'primary_button' => [
                    'text' => $destination->localized('hero_cta_text') ?: __('ui.book_now'),
                    'url' => $destination->hero_cta_url ?: '#destination-form',
                ],
                'secondary_button' => [
                    'text' => $destination->localized('hero_secondary_cta_text') ?: __('ui.discover_more'),
                    'url' => $destination->hero_secondary_cta_url ?: '#destination-highlights',
                ],
            ],
            'quick_info' => [
                'enabled' => (bool) $destination->show_quick_info && $quickInfoSummaryItems->isNotEmpty(),
                'title' => $destination->localized('quick_info_title') ?: __('ui.quick_summary'),
                'items' => self::localizedRepeater($quickInfoSummaryItems, $destination, fn (array $item) => [
                    'label' => $destination->repeaterValue($item, 'label', fallback: $destination->repeaterValue($item, 'title')),
                    'value' => $destination->repeaterValue($item, 'value'),
                    'icon' => $item['icon'] ?? '',
                ]),
            ],
            'about' => [
                'enabled' => (bool) $destination->show_about,
                'title' => $destination->localized('about_title') ?: __('ui.destination_overview'),
                'description' => $destination->localized('about_description') ?: $destination->localized('overview'),
                'image' => self::storageUrl($destination->about_image ?: $destination->featured_image ?: $destination->hero_image),
                'badge' => $destination->localized('subtitle'),
                'points' => self::localizedRepeater($destination->about_points ?: [], $destination, fn (array $item) => [
                    'text' => $destination->repeaterValue($item, 'text'),
                ]),
            ],
            'details' => [
                'enabled' => (bool) $destination->show_detailed && filled($destination->localized('detailed_description')),
                'title' => $destination->localized('detailed_title') ?: __('ui.service_details'),
                'description' => $destination->localized('detailed_description'),
            ],
            'best_time' => self::normalizeBestTimeSection([
                'enabled' => (bool) $destination->show_best_time,
                'badge' => $destination->localized('best_time_badge') ?: self::defaultDestinationBestTimeCopy('badge'),
                'title' => $destination->localized('best_time_title') ?: self::defaultDestinationBestTimeCopy('title'),
                'description' => $destination->localized('best_time_description') ?: self::defaultDestinationBestTimeCopy('description'),
            ]),
            'highlights' => [
                'enabled' => (bool) $destination->show_highlights && collect($destination->highlight_items ?: [])->isNotEmpty(),
                'label' => $destination->localized('highlights_section_label') ?: ($destination->destination_type === 'domestic'
                    ? (app()->getLocale() === 'ar' ? 'أهم الإرشادات' : 'Helpful Guidance Points')
                    : __('ui.highlights')),
                'title' => $destination->localized('highlights_title') ?: __('ui.top_highlights'),
                'items' => self::localizedRepeater($destination->highlight_items ?: [], $destination, fn (array $item) => [
                    'title' => $destination->repeaterValue($item, 'title'),
                    'description' => $destination->repeaterValue($item, 'description'),
                    'image' => self::storageUrl($item['image'] ?? null),
                    'icon' => $item['icon'] ?? '',
                ], active: true),
            ],
            'services' => [
                'enabled' => (bool) $destination->show_services && collect($destination->service_items ?: [])->isNotEmpty(),
                'title' => $destination->localized('services_title') ?: __('ui.included_services'),
                'description' => $destination->localized('services_intro'),
                'items' => self::localizedRepeater($destination->service_items ?: [], $destination, fn (array $item) => [
                    'title' => $destination->repeaterValue($item, 'title'),
                    'description' => $destination->repeaterValue($item, 'description'),
                    'icon' => $item['icon'] ?? '',
                ], active: true),
            ],
            'documents' => [
                'enabled' => (bool) $destination->show_documents && collect($destination->document_items ?: [])->isNotEmpty(),
                'title' => $destination->localized('documents_title') ?: __('ui.required_documents'),
                'description' => $destination->localized('documents_subtitle'),
                'items' => self::localizedRepeater($destination->document_items ?: [], $destination, fn (array $item) => [
                    'title' => $destination->repeaterValue($item, 'title'),
                    'description' => $destination->repeaterValue($item, 'description'),
                    'icon' => $item['icon'] ?? '',
                ], active: true),
            ],
            'steps' => [
                'enabled' => (bool) $destination->show_steps && collect($destination->step_items ?: [])->isNotEmpty(),
                'title' => $destination->localized('steps_title') ?: __('ui.application_steps'),
                'items' => self::localizedRepeater($destination->step_items ?: [], $destination, fn (array $item) => [
                    'number' => $item['step_number'] ?? null,
                    'title' => $destination->repeaterValue($item, 'title'),
                    'description' => $destination->repeaterValue($item, 'description'),
                    'icon' => $item['icon'] ?? '',
                ], active: true),
            ],
            'pricing' => [
                'enabled' => (bool) $destination->show_pricing && collect($destination->pricing_items ?: [])->isNotEmpty(),
                'title' => $destination->localized('pricing_title') ?: __('ui.pricing_information'),
                'description' => $destination->localized('pricing_notes'),
                'items' => self::localizedRepeater($destination->pricing_items ?: [], $destination, fn (array $item) => [
                    'label' => $destination->repeaterValue($item, 'label'),
                    'value' => $destination->repeaterValue($item, 'value'),
                    'note' => $destination->repeaterValue($item, 'note'),
                ], active: true),
            ],
            'faq' => [
                'enabled' => (bool) $destination->show_faq && collect($destination->faqs ?: [])->isNotEmpty(),
                'title' => $destination->localized('faq_title') ?: __('ui.faq'),
                'items' => self::localizedRepeater($destination->faqs ?: [], $destination, fn (array $item) => [
                    'question' => $destination->repeaterValue($item, 'question'),
                    'answer' => $destination->repeaterValue($item, 'answer'),
                ]),
            ],
            'map' => [
                'enabled' => false,
                'title' => '',
                'description' => '',
                'embed_code' => '',
            ],
            'cta' => [
                'enabled' => (bool) $destination->show_cta,
                'title' => $destination->localized('cta_title') ?: __('ui.ready_to_book'),
                'description' => $destination->localized('cta_text'),
                'background_image' => self::storageUrl($destination->cta_background_image),
                'buttons' => array_values(array_filter([
                    [
                        'text' => $destination->localized('cta_button') ?: __('ui.book_now'),
                        'url' => $destination->cta_url ?: '#destination-form',
                        'style' => 'primary',
                    ],
                    filled($destination->localized('cta_secondary_button'))
                        ? [
                            'text' => $destination->localized('cta_secondary_button'),
                            'url' => $destination->cta_secondary_url ?: '#destination-form',
                            'style' => 'outline',
                        ]
                        : null,
                ])),
            ],
            'form' => [
                'enabled' => (bool) $destination->show_form,
                'title' => $destination->localized('form_title') ?: __('ui.book_your_trip'),
                'subtitle' => $destination->localized('form_subtitle'),
                'type' => 'destination',
                'source' => $destination->localized('title'),
                'destination' => $destination->localized('title'),
                'config' => [
                    'title' => $destination->localized('form_title') ?: __('ui.book_your_trip'),
                    'subtitle' => $destination->localized('form_subtitle'),
                    'submit_text' => $destination->localized('form_submit_text') ?: __('ui.send_request'),
                    'default_service_type' => $destination->localized('title'),
                    'visible_fields' => $destination->form_visible_fields ?: ['email', 'travel_date', 'message'],
                ],
                'highlights' => self::localizedRepeater($quickInfoSummaryItems->take(3), $destination, fn (array $item) => [
                    'label' => $destination->repeaterValue($item, 'label', fallback: $destination->repeaterValue($item, 'title')),
                    'value' => $destination->repeaterValue($item, 'value'),
                ]),
            ],
        ];
    }

    protected static function localizedRepeater(iterable $items, object $model, callable $map, bool $active = false): array
    {
        return collect($items)
            ->when($active, fn (Collection $collection) => $collection->filter(fn (array $item) => ($item['is_active'] ?? true)))
            ->sortBy('sort_order')
            ->map(fn (array $item) => $map($item))
            ->filter(fn (array $item) => collect($item)->filter(fn ($value) => filled($value))->isNotEmpty())
            ->values()
            ->all();
    }

    protected static function normalizeBestTimeSection(array $section): array
    {
        return [
            'enabled' => (bool) ($section['enabled'] ?? false),
            'badge' => trim((string) ($section['badge'] ?? __('ui.best_time'))),
            'title' => trim((string) ($section['title'] ?? ($section['default_title'] ?? ''))),
            'description' => trim((string) ($section['description'] ?? ($section['default_description'] ?? ''))),
        ];
    }

    protected static function defaultVisaCountryBestTimeCopy(string $field): string
    {
        $isArabic = app()->getLocale() === 'ar';

        return match ($field) {
            'badge' => $isArabic ? 'أفضل وقت' : 'Best Time',
            'title' => $isArabic ? 'أفضل وقت للتقديم' : 'Best Time to Apply',
            'description' => $isArabic
                ? 'تتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف.'
                : 'Processing usually takes between 15 and 30 business days depending on the season, embassy workload, and how complete the application file is.',
            default => '',
        };
    }

    protected static function defaultDestinationBestTimeCopy(string $field): string
    {
        $isArabic = app()->getLocale() === 'ar';

        return match ($field) {
            'badge' => $isArabic ? 'أفضل وقت' : 'Best Time',
            'title' => $isArabic ? 'أفضل وقت للزيارة' : 'Best Time to Visit',
            'description' => $isArabic
                ? 'يعتمد اختيار أفضل وقت للزيارة على الطقس المفضل وميزانية الرحلة وطبيعة الأنشطة التي تريد تضمينها في البرنامج.'
                : 'The best time to visit depends on the weather you prefer, your trip budget, and the type of activities you want to include.',
            default => '',
        };
    }

    protected static function compactHighlightTitle(?string $text, string $locale): string
    {
        $text = trim((string) $text);

        if ($text === '') {
            return $locale === 'ar' ? 'نقطة مهمة' : 'Key Point';
        }

        $firstSegment = preg_split('/[،,:;\-\.!\?\n\r]+/u', $text)[0] ?? $text;
        $words = preg_split('/\s+/u', trim($firstSegment), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $title = implode(' ', array_slice($words, 0, 4));

        return $title !== '' ? $title : ($locale === 'ar' ? 'نقطة مهمة' : 'Key Point');
    }

    protected static function storageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
