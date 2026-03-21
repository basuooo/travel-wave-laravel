<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Database\Seeder;

class DestinationPageSystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDomestic();
        $this->seedVisaCountries();
    }

    protected function seedDomestic(): void
    {
        foreach ($this->domesticPages() as $index => $page) {
            Destination::query()->updateOrCreate(
                ['slug' => $page['slug']],
                $page + [
                    'destination_type' => 'domestic',
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }

    protected function seedVisaCountries(): void
    {
        $category = VisaCategory::query()->firstOrCreate(
            ['slug' => 'european-union'],
            [
                'name_en' => 'European Union',
                'name_ar' => 'الاتحاد الأوروبي',
                'short_description_en' => 'Europe visa support with practical file preparation.',
                'short_description_ar' => 'دعم تأشيرات أوروبا مع تجهيز عملي للملف.',
                'icon' => 'EU',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        foreach ($this->visaPages() as $index => $page) {
            VisaCountry::query()->updateOrCreate(
                ['slug' => $page['slug']],
                $page + [
                    'visa_category_id' => $category->id,
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }

    protected function domesticPages(): array
    {
        return [
            $this->domesticPage(
                slug: 'sharm-el-sheikh',
                titleEn: 'Sharm El Sheikh',
                titleAr: 'شرم الشيخ',
                subtitleEn: 'Beach escapes, resort stays, and organized Red Sea programs.',
                subtitleAr: 'رحلات شاطئية وإقامات فندقية وبرامج منظمة على البحر الأحمر.',
                bestTimeAr: 'الربيع والخريف غالبًا أفضل الفترات من حيث الطقس والراحة، بينما يبقى الصيف مناسبًا للعائلات وبرامج المنتجعات.',
                highlightsAr: [
                    ['خليج نعمة', 'منطقة مناسبة للتنزه والخروجات المسائية السهلة.'],
                    ['رأس محمد', 'من أشهر المناطق البحرية للرحلات والأنشطة المرتبطة بالبحر.'],
                    ['المنتجعات', 'خيارات متنوعة من الإقامة تناسب الأزواج والعائلات.'],
                ],
                servicesAr: [
                    ['حجز الفنادق', 'ترشيح الفندق المناسب حسب الميزانية وطبيعة الرحلة.'],
                    ['التنقلات', 'تنسيق الانتقالات المرتبطة بالوصول والمغادرة.'],
                    ['برنامج الرحلة', 'موازنة واضحة بين الاسترخاء والأنشطة.'],
                    ['متابعة مستمرة', 'تواصل أوضح قبل السفر وبعد تأكيد الحجز.'],
                ],
                pricingAr: [
                    ['برنامج 3 ليالٍ', 'يبدأ من 6,900 جنيه', 'مناسب للإجازات القصيرة.'],
                    ['برنامج 5 ليالٍ', 'يبدأ من 9,250 جنيه', 'أنسب للمزج بين الإقامة والأنشطة.'],
                ]
            ),
            $this->domesticPage(
                slug: 'marsa-alam',
                titleEn: 'Marsa Alam',
                titleAr: 'مرسى علم',
                subtitleEn: 'Quiet Red Sea stays, diving experiences, and resort-focused domestic travel.',
                subtitleAr: 'إقامات هادئة على البحر الأحمر وتجارب بحرية وبرامج داخلية بطابع استرخائي.',
                bestTimeAr: 'الخريف والشتاء والربيع من الفترات المناسبة للزيارة، بينما يفضلها البعض صيفًا للإقامة داخل المنتجعات.',
                highlightsAr: [
                    ['الرحلات البحرية', 'مناسبة للسنوركلينج والأنشطة الساحلية.'],
                    ['المنتجعات الهادئة', 'تجربة مناسبة لمحبي الراحة والخصوصية.'],
                    ['الإقامة الطويلة', 'الوجهة مناسبة لعدد ليالٍ أكبر من الرحلات السريعة.'],
                ],
                servicesAr: [
                    ['اختيار الفندق', 'مقارنة فئات المنتجعات ومستوى الراحة.'],
                    ['تنسيق التنقلات', 'تنظيم أفضل لتوقيتات الوصول والمغادرة.'],
                    ['برنامج مرن', 'الموازنة بين الرحلات البحرية والوقت الحر.'],
                ],
                pricingAr: [
                    ['إقامة 4 ليالٍ', 'يبدأ من 8,400 جنيه', 'بحسب فئة المنتجع والموسم.'],
                ]
            ),
            $this->domesticPage(
                slug: 'hurghada',
                titleEn: 'Hurghada',
                titleAr: 'الغردقة',
                subtitleEn: 'A balanced domestic destination for beach stays, family travel, and activity-focused resort holidays.',
                subtitleAr: 'وجهة داخلية متوازنة للشاطئ والعائلات وبرامج الإقامة المليئة بالأنشطة.',
                bestTimeAr: 'تناسب الغردقة معظم شهور السنة، ويظل الربيع والخريف من الفترات المفضلة لتوازن الطقس.',
                highlightsAr: [
                    ['فنادق عائلية', 'خيارات كثيرة مناسبة للعائلات والإجازات الجماعية.'],
                    ['عروض موسمية', 'برامج تتغير حسب الموسم وفئة الفندق.'],
                    ['تنوع في الإقامة', 'اختيارات واسعة تناسب مستويات مختلفة من الميزانية.'],
                ],
                servicesAr: [
                    ['مقارنة الفنادق', 'إظهار الفروق بين الفئات والمواقع ونظام الوجبات.'],
                    ['دعم الحجز', 'تنسيق أوضح لتأكيد البرنامج والتفاصيل.'],
                    ['متابعة قبل السفر', 'مراجعة البيانات النهائية قبل الانطلاق.'],
                ],
                pricingAr: [
                    ['الباقة القياسية', 'يبدأ من 5,800 جنيه', 'بحسب الفندق وعدد الليالي.'],
                ]
            ),
        ];
    }

    protected function visaPages(): array
    {
        return [
            $this->visaPage(
                slug: 'germany-visa',
                nameEn: 'Germany',
                nameAr: 'ألمانيا',
                flag: 'visa-countries/germany-flag.svg',
                supportAr: 'تساعدك Travel Wave في تنظيم الملف ومراجعة التناسق بين المستندات والحجوزات وشرح ما يلزم قبل الموعد.'
            ),
            $this->visaPage(
                slug: 'italy-visa',
                nameEn: 'Italy',
                nameAr: 'إيطاليا',
                flag: 'visa-countries/italy-flag.svg',
                supportAr: 'تساعدك Travel Wave في تجهيز الملف بطريقة أوضح ومراجعة المستندات وتنظيم الحجوزات بما يدعم غرض السفر المعلن.'
            ),
        ];
    }

    protected function domesticPage(
        string $slug,
        string $titleEn,
        string $titleAr,
        string $subtitleEn,
        string $subtitleAr,
        string $bestTimeAr,
        array $highlightsAr,
        array $servicesAr,
        array $pricingAr
    ): array {
        return [
            'slug' => $slug,
            'title_en' => $titleEn,
            'title_ar' => $titleAr,
            'subtitle_en' => $subtitleEn,
            'subtitle_ar' => $subtitleAr,
            'excerpt_en' => $subtitleEn,
            'excerpt_ar' => $subtitleAr,
            'hero_badge_en' => 'Domestic Tourism',
            'hero_badge_ar' => 'السياحة الداخلية',
            'hero_title_en' => $titleEn . ' Trips with Travel Wave',
            'hero_title_ar' => 'رحلات ' . $titleAr . ' مع Travel Wave',
            'hero_subtitle_en' => 'Travel Wave helps organize the hotel, timing, and trip details in a cleaner domestic travel experience.',
            'hero_subtitle_ar' => 'تساعدك Travel Wave على تنظيم الفندق والتوقيت وتفاصيل الرحلة في تجربة سفر داخلية أوضح وأكثر راحة.',
            'hero_cta_text_en' => 'Book Now',
            'hero_cta_text_ar' => 'احجز الآن',
            'hero_cta_url' => '#destination-form',
            'hero_secondary_cta_text_en' => 'View Highlights',
            'hero_secondary_cta_text_ar' => 'استعرض المزايا',
            'hero_secondary_cta_url' => '#destination-highlights',
            'hero_image' => 'hero-slides/slide-3.svg',
            'hero_mobile_image' => 'hero-slides/slide-3.svg',
            'featured_image' => 'hero-slides/slide-3.svg',
            'about_image' => 'hero-slides/slide-1.svg',
            'quick_info_title_en' => 'Quick Info',
            'quick_info_title_ar' => 'معلومات سريعة',
            'quick_info_items' => [
                ['label_en' => 'Trip Type', 'label_ar' => 'نوع البرنامج', 'value_en' => 'Domestic tourism package', 'value_ar' => 'برنامج سياحة داخلية', 'icon' => 'TP', 'sort_order' => 1, 'is_active' => true],
                ['label_en' => 'Suggested Duration', 'label_ar' => 'المدة المقترحة', 'value_en' => '3 to 5 nights', 'value_ar' => '3 إلى 5 ليالٍ', 'icon' => 'DU', 'sort_order' => 2, 'is_active' => true],
                ['label_en' => 'Starting Price', 'label_ar' => 'السعر يبدأ من', 'value_en' => 'Ask for latest offer', 'value_ar' => 'اطلب أحدث عرض', 'icon' => 'PR', 'sort_order' => 3, 'is_active' => true],
                ['label_en' => 'Best Time', 'label_ar' => 'أفضل وقت', 'value_en' => 'Depends on season', 'value_ar' => 'بحسب الموسم', 'icon' => 'BT', 'sort_order' => 4, 'is_active' => true],
            ],
            'about_title_en' => 'About the Destination',
            'about_title_ar' => 'نبذة عن الوجهة',
            'about_description_en' => $titleEn . ' remains one of the practical domestic options for travelers looking for a more organized holiday with hotels, transfers, and balanced activities.',
            'about_description_ar' => $titleAr . ' من الوجهات المحلية المناسبة لمن يريد رحلة منظمة بشكل أفضل تشمل الفندق والتنقلات والأنشطة دون تعقيد في الحجز.',
            'about_points' => [
                ['text_en' => 'Suitable for couples, families, or short leisure breaks.', 'text_ar' => 'مناسبة للأزواج والعائلات والإجازات القصيرة.'],
                ['text_en' => 'Flexible hotel levels and package structure.', 'text_ar' => 'مرونة في مستويات الفنادق وشكل البرنامج.'],
                ['text_en' => 'Travel Wave helps compare the options more clearly.', 'text_ar' => 'تساعد Travel Wave على مقارنة الخيارات بشكل أوضح.'],
            ],
            'detailed_title_en' => 'Trip Details',
            'detailed_title_ar' => 'تفاصيل الرحلة',
            'detailed_description_en' => "Travel Wave organizes the destination around the right stay length, hotel category, and guest preferences.\n\nThis makes the trip easier to compare and confirm without getting lost in scattered options.",
            'detailed_description_ar' => "تنظم Travel Wave الوجهة حسب مدة الإقامة المناسبة وفئة الفندق واحتياج المسافرين.\n\nوهذا يجعل الرحلة أسهل في المقارنة والتأكيد دون تشتت بين الخيارات.",
            'best_time_title_en' => 'Best Time to Visit',
            'best_time_title_ar' => 'أفضل وقت للزيارة',
            'best_time_description_en' => 'Best travel timing depends on weather preference, budget level, and the type of activities planned.',
            'best_time_description_ar' => $bestTimeAr,
            'highlights_title_en' => 'Top Highlights',
            'highlights_title_ar' => 'أهم المعالم والأنشطة',
            'highlight_items' => collect($highlightsAr)->map(fn ($item, $index) => [
                'title_en' => $item[0],
                'title_ar' => $item[0],
                'description_en' => $item[1],
                'description_ar' => $item[1],
                'icon' => strtoupper(substr($item[0], 0, 2)),
                'sort_order' => $index + 1,
                'is_active' => true,
            ])->all(),
            'services_title_en' => 'Included Services',
            'services_title_ar' => 'الخدمات المتضمنة',
            'services_intro_en' => 'The trip can be built around your timing, comfort level, and package preference.',
            'services_intro_ar' => 'يمكن تصميم الرحلة حسب توقيتك ومستوى الراحة المناسب والبرنامج المطلوب.',
            'service_items' => collect($servicesAr)->map(fn ($item, $index) => [
                'title_en' => $item[0],
                'title_ar' => $item[0],
                'description_en' => $item[1],
                'description_ar' => $item[1],
                'icon' => strtoupper(substr($item[0], 0, 2)),
                'sort_order' => $index + 1,
                'is_active' => true,
            ])->all(),
            'documents_title_en' => 'Required Before Booking',
            'documents_title_ar' => 'ما يلزم قبل الحجز',
            'documents_subtitle_en' => 'Domestic travel does not need visa paperwork, but some basic details help confirm the booking faster.',
            'documents_subtitle_ar' => 'السفر الداخلي لا يحتاج مستندات تأشيرة، لكن بعض البيانات الأساسية تساعد على تأكيد الحجز بسرعة.',
            'document_items' => [
                ['title_en' => 'Traveler Names', 'title_ar' => 'أسماء المسافرين', 'description_en' => 'Correct names for reservation records.', 'description_ar' => 'الأسماء الصحيحة لبيانات الحجز.', 'icon' => 'NM', 'sort_order' => 1, 'is_active' => true],
                ['title_en' => 'Travel Dates', 'title_ar' => 'تواريخ السفر', 'description_en' => 'Preferred travel period and stay length.', 'description_ar' => 'فترة السفر المفضلة وعدد الليالي.', 'icon' => 'DT', 'sort_order' => 2, 'is_active' => true],
                ['title_en' => 'Guest Count', 'title_ar' => 'عدد الأفراد', 'description_en' => 'Used to match room type and offer level.', 'description_ar' => 'لتحديد نوع الغرفة ومستوى العرض.', 'icon' => 'GS', 'sort_order' => 3, 'is_active' => true],
            ],
            'steps_title_en' => 'Booking Steps',
            'steps_title_ar' => 'خطوات الحجز',
            'step_items' => [
                ['title_en' => 'Choose the destination', 'title_ar' => 'اختر الوجهة', 'description_en' => 'Share the trip type and preferred dates.', 'description_ar' => 'حدد طبيعة الرحلة والتواريخ المناسبة.', 'step_number' => 1, 'sort_order' => 1, 'is_active' => true],
                ['title_en' => 'Review the options', 'title_ar' => 'راجع الخيارات', 'description_en' => 'Compare the suggested hotels and packages.', 'description_ar' => 'قارن الفنادق والباقات المقترحة.', 'step_number' => 2, 'sort_order' => 2, 'is_active' => true],
                ['title_en' => 'Confirm the booking', 'title_ar' => 'أكد الحجز', 'description_en' => 'Choose the final option and complete the details.', 'description_ar' => 'اختر العرض النهائي واستكمل التفاصيل.', 'step_number' => 3, 'sort_order' => 3, 'is_active' => true],
                ['title_en' => 'Receive confirmation', 'title_ar' => 'استلم التأكيد', 'description_en' => 'Travel Wave confirms the reservation and next steps.', 'description_ar' => 'تؤكد Travel Wave الحجز والخطوات التالية.', 'step_number' => 4, 'sort_order' => 4, 'is_active' => true],
            ],
            'pricing_title_en' => 'Pricing Overview',
            'pricing_title_ar' => 'نظرة على الأسعار',
            'pricing_notes_en' => 'Final pricing changes by hotel level, trip duration, and season.',
            'pricing_notes_ar' => 'تتغير الأسعار النهائية حسب فئة الفندق ومدة الرحلة والموسم.',
            'pricing_items' => collect($pricingAr)->map(fn ($item, $index) => [
                'label_en' => $item[0],
                'label_ar' => $item[0],
                'value_en' => $item[1],
                'value_ar' => $item[1],
                'note_en' => $item[2],
                'note_ar' => $item[2],
                'sort_order' => $index + 1,
                'is_active' => true,
            ])->all(),
            'faq_title_en' => 'Frequently Asked Questions',
            'faq_title_ar' => 'الأسئلة الشائعة',
            'faqs' => [
                ['question_en' => 'Can the package be adjusted?', 'question_ar' => 'هل يمكن تعديل البرنامج؟', 'answer_en' => 'Yes, package level and stay details can often be tailored around your needs.', 'answer_ar' => 'نعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'sort_order' => 1, 'is_active' => true],
                ['question_en' => 'Are family options available?', 'question_ar' => 'هل توجد برامج مناسبة للعائلات؟', 'answer_en' => 'Yes, the destination can be prepared with family-friendly hotel and room options.', 'answer_ar' => 'نعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'sort_order' => 2, 'is_active' => true],
            ],
            'cta_title_en' => 'Ready to Book with More Clarity?',
            'cta_title_ar' => 'جاهز للحجز بشكل أوضح؟',
            'cta_text_en' => 'Travel Wave helps compare the right options and organize the next step more smoothly.',
            'cta_text_ar' => 'تساعدك Travel Wave على مقارنة الخيارات المناسبة وتنظيم الخطوة التالية بسهولة أكبر.',
            'cta_button_en' => 'Start Booking',
            'cta_button_ar' => 'ابدأ الحجز',
            'cta_url' => '#destination-form',
            'cta_secondary_button_en' => 'WhatsApp',
            'cta_secondary_button_ar' => 'واتساب',
            'cta_secondary_url' => 'https://wa.me/201000000000',
            'cta_background_image' => 'hero-slides/slide-2.svg',
            'form_title_en' => 'Ask About ' . $titleEn,
            'form_title_ar' => 'استفسر عن ' . $titleAr,
            'form_subtitle_en' => 'Send your details and Travel Wave will help with a suitable package.',
            'form_subtitle_ar' => 'أرسل بياناتك وستساعدك Travel Wave في الوصول إلى البرنامج الأنسب.',
            'form_submit_text_en' => 'Send Request',
            'form_submit_text_ar' => 'أرسل الطلب',
            'form_visible_fields' => ['email', 'travel_date', 'return_date', 'travelers_count', 'message'],
        ];
    }

    protected function visaPage(string $slug, string $nameEn, string $nameAr, string $flag, string $supportAr): array
    {
        return [
            'slug' => $slug,
            'name_en' => $nameEn,
            'name_ar' => $nameAr,
            'excerpt_en' => $nameEn . ' visa is commonly requested for tourism, family visits, and selected business travel under the short-stay Schengen category.',
            'excerpt_ar' => 'تأشيرة ' . $nameAr . ' من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.',
            'hero_badge_en' => 'Schengen Visa Support',
            'hero_badge_ar' => 'دعم تأشيرة شنغن',
            'hero_title_en' => $nameEn . ' Visa Support with Better Organization',
            'hero_title_ar' => 'دعم تأشيرة ' . $nameAr . ' بملف أكثر تنظيمًا ووضوحًا',
            'hero_subtitle_en' => 'Travel Wave helps applicants prepare the file, align the bookings, and understand the process before submission.',
            'hero_subtitle_ar' => 'تساعدك Travel Wave على تجهيز الملف وتنسيق الحجوزات وفهم خطوات التقديم قبل التسليم.',
            'hero_cta_text_en' => 'Start ' . $nameEn . ' Visa Request',
            'hero_cta_text_ar' => 'ابدأ طلب تأشيرة ' . $nameAr,
            'hero_cta_url' => '#destination-form',
            'hero_image' => 'hero-slides/slide-2.svg',
            'hero_mobile_image' => 'hero-slides/slide-2.svg',
            'flag_image' => $flag,
            'overview_en' => $nameEn . ' remains one of the popular Schengen destinations and usually benefits from a clear, well-structured file before the appointment stage.',
            'overview_ar' => 'تعد ' . $nameAr . ' من الوجهات الشائعة ضمن شنغن، ويستفيد طلبها عادة من ملف واضح ومنظم قبل مرحلة الموعد.',
            'visa_type_en' => 'Short-Stay Schengen Visa',
            'visa_type_ar' => 'تأشيرة شنغن قصيرة الإقامة',
            'stay_duration_en' => 'Up to 90 days within 180 days',
            'stay_duration_ar' => 'حتى 90 يومًا خلال 180 يومًا',
            'quick_summary_items' => [
                ['title_en' => 'Visa Type', 'title_ar' => 'نوع التأشيرة', 'value_en' => 'Short-Stay Schengen', 'value_ar' => 'شنغن قصيرة الإقامة', 'icon' => 'VS', 'sort_order' => 1, 'is_active' => true],
                ['title_en' => 'Processing Time', 'title_ar' => 'مدة المعالجة', 'value_en' => '15 to 30 working days', 'value_ar' => '15 إلى 30 يوم عمل', 'icon' => 'PT', 'sort_order' => 2, 'is_active' => true],
                ['title_en' => 'Stay Duration', 'title_ar' => 'مدة الإقامة', 'value_en' => 'Up to 90 days', 'value_ar' => 'حتى 90 يومًا', 'icon' => 'SD', 'sort_order' => 3, 'is_active' => true],
                ['title_en' => 'Approx. Fees', 'title_ar' => 'الرسوم التقريبية', 'value_en' => 'Quoted after review', 'value_ar' => 'تحدد بعد المراجعة', 'icon' => 'FE', 'sort_order' => 4, 'is_active' => true],
            ],
            'introduction_title_en' => $nameEn . ' Visa Overview',
            'introduction_title_ar' => 'نظرة عامة على تأشيرة ' . $nameAr,
            'introduction_badge_en' => 'Travel Wave Support',
            'introduction_badge_ar' => 'دعم Travel Wave',
            'introduction_points' => [
                ['text_en' => 'Suitable for tourism, family visits, and selected business travel.', 'text_ar' => 'مناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.'],
                ['text_en' => 'A better organized file usually creates a clearer submission path.', 'text_ar' => 'يساعد تنظيم الملف على جعل مسار التقديم أوضح.'],
                ['text_en' => 'Travel Wave clarifies the next step before each stage.', 'text_ar' => $supportAr],
            ],
            'detailed_title_en' => 'Detailed Visa Explanation',
            'detailed_title_ar' => 'شرح التأشيرة بالتفصيل',
            'detailed_description_en' => "Short-stay Schengen files usually need consistency between the stated purpose of travel, the financial documents, and the booking evidence.\n\nTravel Wave helps applicants reduce confusion by organizing the file in a more readable and practical way.",
            'detailed_description_ar' => "تحتاج ملفات شنغن قصيرة الإقامة عادة إلى اتساق بين الغرض المعلن من السفر والمستندات المالية والحجوزات الداعمة.\n\nتساعدك Travel Wave على تقليل الارتباك من خلال تنظيم الملف بطريقة أكثر وضوحًا وعملية.",
            'highlights' => [
                ['text_en' => 'Suitable for tourism and family visits.', 'text_ar' => 'مناسبة للسياحة والزيارات العائلية.'],
                ['text_en' => 'Early preparation can help in busy seasons.', 'text_ar' => 'يفيد التحضير المبكر في المواسم المزدحمة.'],
            ],
            'why_choose_title_en' => 'Why Choose Travel Wave',
            'why_choose_title_ar' => 'لماذا تختار Travel Wave',
            'why_choose_intro_en' => 'We help applicants move through the process with more clarity and fewer last-minute surprises.',
            'why_choose_intro_ar' => 'نساعد المتقدمين على المرور في الخطوات بوضوح أكبر ومفاجآت أقل في اللحظات الأخيرة.',
            'why_choose_items' => [
                ['title_en' => 'Document Review', 'title_ar' => 'مراجعة المستندات', 'description_en' => 'Checking the consistency of the file before submission.', 'description_ar' => 'مراجعة اتساق الملف قبل التقديم.', 'icon' => 'shield', 'sort_order' => 1, 'is_active' => true],
                ['title_en' => 'Booking Alignment', 'title_ar' => 'تنسيق الحجوزات', 'description_en' => 'Aligning hotel, flight, and insurance timing.', 'description_ar' => 'تنسيق توقيتات الفندق والطيران والتأمين.', 'icon' => 'calendar', 'sort_order' => 2, 'is_active' => true],
                ['title_en' => 'Step Follow-Up', 'title_ar' => 'متابعة الخطوات', 'description_en' => 'Clarifying the next practical step after each stage.', 'description_ar' => $supportAr, 'icon' => 'support', 'sort_order' => 3, 'is_active' => true],
            ],
            'documents_title_en' => 'Required Documents',
            'documents_title_ar' => 'المستندات المطلوبة',
            'documents_subtitle_en' => 'The exact list may vary by profile, but these are common supporting items.',
            'documents_subtitle_ar' => 'قد تختلف القائمة حسب الملف، لكن هذه من المستندات الأساسية الشائعة.',
            'document_items' => [
                ['name_en' => 'Valid Passport', 'name_ar' => 'جواز سفر ساري', 'description_en' => 'Passport validity and blank pages should be suitable for the request.', 'description_ar' => 'يجب أن تكون مدة الصلاحية والصفحات المتاحة مناسبة للطلب.', 'sort_order' => 1, 'is_active' => true],
                ['name_en' => 'Bank Statement', 'name_ar' => 'كشف حساب بنكي', 'description_en' => 'Financial movement should support the travel plan.', 'description_ar' => 'ينبغي أن يدعم الحركة المالية خطة الرحلة.', 'sort_order' => 2, 'is_active' => true],
                ['name_en' => 'Employment or Study Proof', 'name_ar' => 'إثبات عمل أو دراسة', 'description_en' => 'Supporting the declared profile and return intention.', 'description_ar' => 'لدعم الملف والارتباطات بعد السفر.', 'sort_order' => 3, 'is_active' => true],
                ['name_en' => 'Hotel, Flight, and Insurance', 'name_ar' => 'حجوزات الفندق والطيران والتأمين', 'description_en' => 'Dates should stay aligned across the itinerary.', 'description_ar' => 'يجب أن تكون التواريخ متسقة في كامل خط السير.', 'sort_order' => 4, 'is_active' => true],
            ],
            'steps_title_en' => 'Application Steps',
            'steps_title_ar' => 'خطوات التقديم',
            'step_items' => [
                ['title_en' => 'Send the basic details', 'title_ar' => 'أرسل البيانات الأساسية', 'description_en' => 'Purpose, timing, and profile overview.', 'description_ar' => 'الغرض من السفر والتوقيت ونبذة عن الملف.', 'step_number' => 1, 'sort_order' => 1, 'is_active' => true],
                ['title_en' => 'Review the file', 'title_ar' => 'مراجعة الملف', 'description_en' => 'Clarify missing points and organize priorities.', 'description_ar' => 'توضيح النواقص وترتيب الأولويات.', 'step_number' => 2, 'sort_order' => 2, 'is_active' => true],
                ['title_en' => 'Prepare the documents', 'title_ar' => 'تجهيز المستندات', 'description_en' => 'Organize the file and supporting bookings.', 'description_ar' => 'تنظيم الملف والحجوزات الداعمة.', 'step_number' => 3, 'sort_order' => 3, 'is_active' => true],
                ['title_en' => 'Submit and follow up', 'title_ar' => 'التقديم والمتابعة', 'description_en' => 'Complete the submission and continue request tracking.', 'description_ar' => 'إتمام التقديم ثم متابعة حالة الطلب.', 'step_number' => 4, 'sort_order' => 4, 'is_active' => true],
            ],
            'fees_title_en' => 'Fees and Processing Time',
            'fees_title_ar' => 'الرسوم ومدة المعالجة',
            'fee_items' => [
                ['label_en' => 'Embassy Fee', 'label_ar' => 'رسوم السفارة', 'value_en' => 'Varies by profile', 'value_ar' => 'تختلف حسب الملف', 'sort_order' => 1, 'is_active' => true],
                ['label_en' => 'Service Fee', 'label_ar' => 'رسوم الخدمة', 'value_en' => 'Quoted after review', 'value_ar' => 'تحدد بعد المراجعة', 'sort_order' => 2, 'is_active' => true],
                ['label_en' => 'Processing Time', 'label_ar' => 'مدة المعالجة', 'value_en' => 'Usually 15 to 30 working days', 'value_ar' => 'عادة من 15 إلى 30 يوم عمل', 'sort_order' => 3, 'is_active' => true],
            ],
            'fees_notes_en' => 'Final timing depends on seasonality and file readiness.',
            'fees_notes_ar' => 'تعتمد المدة النهائية على الموسم ومدى جاهزية الملف.',
            'faq_title_en' => 'Frequently Asked Questions',
            'faq_title_ar' => 'الأسئلة الشائعة',
            'faqs' => [
                ['question_en' => 'Is this a Schengen visa?', 'question_ar' => 'هل هذه تأشيرة شنغن؟', 'answer_en' => 'Yes, in most short-stay travel cases the request falls under Schengen rules.', 'answer_ar' => 'نعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.', 'sort_order' => 1, 'is_active' => true],
                ['question_en' => 'When should I start?', 'question_ar' => 'متى أبدأ؟', 'answer_en' => 'Early preparation is usually better, especially before high-demand travel periods.', 'answer_ar' => 'يفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.', 'sort_order' => 2, 'is_active' => true],
            ],
            'inquiry_form_title_en' => 'Ask About ' . $nameEn . ' Visa',
            'inquiry_form_title_ar' => 'استفسر عن تأشيرة ' . $nameAr,
            'inquiry_form_subtitle_en' => 'Send your details and Travel Wave will guide you on the next practical step.',
            'inquiry_form_subtitle_ar' => 'أرسل بياناتك وستساعدك Travel Wave في تحديد الخطوة العملية التالية.',
            'inquiry_form_button_en' => 'Send Inquiry',
            'inquiry_form_button_ar' => 'أرسل الاستفسار',
            'inquiry_form_default_service_type' => $nameEn . ' Visa',
            'inquiry_form_visible_fields' => ['email', 'travel_date', 'message'],
            'inquiry_form_is_active' => true,
            'cta_title_en' => 'Ready to Start ' . $nameEn . ' Visa Preparation?',
            'cta_title_ar' => 'جاهز لبدء تجهيز تأشيرة ' . $nameAr . '؟',
            'cta_text_en' => 'Travel Wave helps make the request more organized and easier to follow.',
            'cta_text_ar' => 'تساعدك Travel Wave على جعل الطلب أكثر تنظيمًا وأسهل في المتابعة.',
            'cta_button_en' => 'Start Now',
            'cta_button_ar' => 'ابدأ الآن',
            'cta_url' => '#destination-form',
            'final_cta_background_image' => 'hero-slides/slide-1.svg',
            'final_cta_is_active' => true,
        ];
    }
}
