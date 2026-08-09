<?php

namespace Database\Seeders;

use App\Models\LandingPage\LpSection;
use App\Models\LandingPage\LpSectionCategory;
use App\Models\LandingPage\LpTemplate;
use App\Models\LandingPage\LpTemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LandingPageBuilderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Template Categories
        $visaCat = LpTemplateCategory::firstOrCreate(
            ['slug' => 'visa-travel-offers'],
            [
                'name_en' => 'Visa & Schengen Offers',
                'name_ar' => 'عروض التأشيرات والشنغن',
                'icon' => 'bi-passport',
                'sort_order' => 1,
            ]
        );

        $domesticCat = LpTemplateCategory::firstOrCreate(
            ['slug' => 'domestic-packages'],
            [
                'name_en' => 'Domestic Packages & Resorts',
                'name_ar' => 'عروض الرحلات والسياحة الداخلية',
                'icon' => 'bi-geo-alt',
                'sort_order' => 2,
            ]
        );

        $bundleCat = LpTemplateCategory::firstOrCreate(
            ['slug' => 'flight-hotel-bundles'],
            [
                'name_en' => 'Flight & Hotel Bundles',
                'name_ar' => 'باقات الطيران والفنادق',
                'icon' => 'bi-airplane',
                'sort_order' => 3,
            ]
        );

        // 2. Section Categories
        $heroSecCat = LpSectionCategory::firstOrCreate(
            ['slug' => 'hero-banners'],
            [
                'name_en' => 'Hero Banners',
                'name_ar' => 'بنرات الهيدر الرئيسية',
                'icon' => 'bi-layout-header',
                'sort_order' => 1,
            ]
        );

        $featuresSecCat = LpSectionCategory::firstOrCreate(
            ['slug' => 'features-benefits'],
            [
                'name_en' => 'Features & Benefits',
                'name_ar' => 'المميزات والخدمات',
                'icon' => 'bi-check-circle',
                'sort_order' => 2,
            ]
        );

        $formsSecCat = LpSectionCategory::firstOrCreate(
            ['slug' => 'lead-forms'],
            [
                'name_en' => 'Lead & Booking Forms',
                'name_ar' => 'نماذج الحجز والاستفسار',
                'icon' => 'bi-ui-checks',
                'sort_order' => 3,
            ]
        );

        // 3. Pre-built Master Templates
        // Template 1: Schengen Visa Premium
        LpTemplate::firstOrCreate(
            ['slug' => 'schengen-visa-premium-template'],
            [
                'template_category_id' => $visaCat->id,
                'name_en' => 'Schengen & Europe Visa Express',
                'name_ar' => 'قالب تأشيرة شنغن وأوروبا السريعة',
                'description_en' => 'High-converting landing page template for Schengen visa application campaigns.',
                'description_ar' => 'قالب احترافي عالي التحويل لإعلانات تأشيرات الشنغن والشركات.',
                'is_active' => true,
                'is_global' => true,
                'structure' => [
                    'version' => '2.0',
                    'canvas' => ['background' => '#ffffff'],
                    'elements' => [
                        [
                            'id' => 'hero_schengen',
                            'type' => 'hero',
                            'content' => [
                                'eyebrow_en' => 'Schengen Visa Express Assistance',
                                'eyebrow_ar' => 'استخراج تأشيرة شنغن بسهولة وسرعة',
                                'title_en' => 'Apply for Your European Visa Now',
                                'title_ar' => 'احصل على تأشيرة أوروبا وشنغن بأعلى نسبة قبول',
                                'subtitle_en' => 'Professional appointment booking, document translation, and full application support.',
                                'subtitle_ar' => 'حجز مواعيد سفارات، ترجمة معتمدة، وتجهيز الملف بالكامل بأعلى معايير الدقة.',
                                'cta_text_en' => 'Get Visa Support',
                                'cta_text_ar' => 'قدم طلبك الآن',
                                'cta_link' => '#lead_form_section',
                            ],
                            'style' => [
                                'background_color' => '#0f172a',
                                'text_color' => '#ffffff',
                            ],
                        ],
                        [
                            'id' => 'form_schengen',
                            'type' => 'lead_form',
                            'content' => [
                                'title_en' => 'Submit Your Inquiry',
                                'title_ar' => 'نموذج تقديم طلب التأشيرة السريع',
                            ],
                        ],
                    ],
                ],
            ]
        );

        // Template 2: Red Sea & Resort Package
        LpTemplate::firstOrCreate(
            ['slug' => 'red-sea-resorts-template'],
            [
                'template_category_id' => $domesticCat->id,
                'name_en' => 'Red Sea & Luxury Resorts',
                'name_ar' => 'قالب عروض وتخفيضات منتجعات البحر الأحمر',
                'description_en' => 'Stunning template for domestic trips, beach resorts, and all-inclusive packages.',
                'description_ar' => 'قالب متميز لعروض الرحلات البحرية والمنتجعات الشاطئية الشاملة.',
                'is_active' => true,
                'is_global' => true,
                'structure' => [
                    'version' => '2.0',
                    'canvas' => ['background' => '#ffffff'],
                    'elements' => [
                        [
                            'id' => 'hero_resorts',
                            'type' => 'hero',
                            'content' => [
                                'eyebrow_en' => 'Summer Special Offers 2026',
                                'eyebrow_ar' => 'عروض وتخفيضات صيف 2026',
                                'title_en' => 'Luxury Red Sea Vacation Packages',
                                'title_ar' => 'استمتع بأفضل إجازة في منتجعات البحر الأحمر',
                                'subtitle_en' => '5-Star hotels, all-inclusive meals, and private beach access at exclusive prices.',
                                'subtitle_ar' => 'فننادق 5 نجوم شاملة الإقامة والوجبات والأنشطة الترفيهية بأسعار خاصة.',
                                'cta_text_en' => 'Explore Packages',
                                'cta_text_ar' => 'استعرض الباقات',
                                'cta_link' => '#lead_form_section',
                            ],
                            'style' => [
                                'background_color' => '#0284c7',
                                'text_color' => '#ffffff',
                            ],
                        ],
                        [
                            'id' => 'form_resorts',
                            'type' => 'lead_form',
                            'content' => [
                                'title_en' => 'Book Your Vacation',
                                'title_ar' => 'احجز إجازتك الآن وتواصل معنا',
                            ],
                        ],
                    ],
                ],
            ]
        );

        // 4. Pre-built Ready Sections
        LpSection::firstOrCreate(
            ['name_en' => 'Visa Express Hero Banner'],
            [
                'section_category_id' => $heroSecCat->id,
                'name_ar' => 'بنر تأشيرة السفر السريع',
                'description_en' => 'Hero banner tailored for visa services.',
                'description_ar' => 'بنر هيدر خاص بخدمات استخراج التأشيرات.',
                'is_active' => true,
                'is_global' => true,
                'structure' => [
                    'type' => 'hero',
                    'content' => [
                        'title_en' => 'Fast & Easy Visa Services',
                        'title_ar' => 'خدمات استخراج التأشيرات السريعة والمضمونة',
                    ],
                ],
            ]
        );

        LpSection::firstOrCreate(
            ['name_en' => 'Lead Inquiry Form Block'],
            [
                'section_category_id' => $formsSecCat->id,
                'name_ar' => 'بلوك نموذج حجز واستفسار متكامل',
                'description_en' => 'Lead collection form with responsive container.',
                'description_ar' => 'نموذج استقبال طلبات العملاء محكم التجاوب.',
                'is_active' => true,
                'is_global' => true,
                'structure' => [
                    'type' => 'lead_form',
                    'content' => [
                        'title_en' => 'Request Information',
                        'title_ar' => 'تواصل معنا واستلم تفاصيل العرض',
                    ],
                ],
            ]
        );
    }
}
